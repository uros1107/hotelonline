<?php
function getBookedRooms($from_time, $to_time, $room_id = 0)
{
    global $db;
    global $res_room;
    
    $days = array();
    $booked = array();
    
    $query_book = '
        SELECT stock, br.id_room as room_id, from_date, to_date
        FROM pm_booking as b, pm_booking_room as br, pm_room as r
        WHERE
            lang = '.DEFAULT_LANG.'
            AND
            br.id_room = r.id
            AND br.id_booking = b.id
            AND (status = 4 OR (status = 1 AND (b.add_date > '.(time()-900).' OR payment_option IN(\'arrival\',\'check\'))))
            AND from_date < '.$to_time.'
            AND to_date > '.$from_time.'
            AND r.checked = 1';
			if(!empty($room_id)) $query_book .= '
			AND r.id = '.$room_id;
			$query_book .= '
		GROUP BY br.id
		
		UNION ALL
		
		SELECT stock, r.id as room_id, from_date, to_date
        FROM pm_room as r, pm_room_lock as rl
        WHERE
            lang = '.DEFAULT_LANG.'
            AND rl.id_room = r.id
            AND rl.add_date > '.(time()-900).'
            AND r.checked = 1';
			if(!empty($room_id)) $query_book .= '
			AND r.id = '.$room_id;
			if(isset($_SESSION['book']['sessid'])) $query_book .= '
			AND sessid != '.$db->quote($_SESSION['book']['sessid']);
			$query_book .= '
			
		UNION ALL
		
		SELECT (r.stock-rc.stock)+1 as stock, r.id as room_id, from_date, to_date
		FROM pm_room as r, pm_room_closing as rc
		WHERE
			lang = '.DEFAULT_LANG.'
			AND rc.id_room = r.id
			AND r.checked = 1
			AND from_date <= '.$to_time.'
			AND to_date >= '.$from_time;
			if(!empty($room_id)) $query_book .= '
			AND r.id = '.$room_id;
    
    $result_book = $db->query($query_book);
    if($result_book !== false){
        foreach($result_book as $i => $row){
            $start_date = $row['from_date'];
            $end_date = $row['to_date'];
            $id_room = $row['room_id'];
            $room_stock = $row['stock'];
			
            $start = ($start_date < $from_time) ? $from_time : $start_date;
            $end = ($end_date > $to_time) ? $to_time : $end_date;
            
            for($date = $start; $date < $end; $date += 86400){

                $days[$id_room][$date] = isset($days[$id_room][$date]) ? $days[$id_room][$date]+1 : 1;
			
                if($days[$id_room][$date]+1 > $room_stock && !in_array($date, $booked)) $booked[$id_room][] = $date;
            }
            $max = isset($days[$id_room]) ? max($days[$id_room]) : 0;
			
            $res_room[$id_room]['room_stock'] = max(0, $room_stock-$max);
        }
    }
    return $booked;
}

function getRoomsResult($from_time, $to_time, $num_adults, $num_children, $strict = false, $room_id = 0)
{
    global $db;
    global $texts;
    global $res_room;
    
    $res_room = array();
    
    $amount = 0;
    $total_nights = 0;
    $booked = getBookedRooms($from_time, $to_time, $room_id);
    
    $num_nights = ($to_time-$from_time)/86400;
    
    $tax_id = 0;
    $result_incl_tax = $db->prepare('SELECT * FROM pm_tax WHERE id = :tax_id AND checked = 1 AND value > 0 GROUP BY id ORDER BY rank LIMIT 1');
    $result_incl_tax->bindParam(':tax_id', $tax_id);
    
    $taxes_id = '';
    $result_tax = $db->prepare('SELECT * FROM pm_tax WHERE (FIND_IN_SET(id, :taxes_id) OR id = :tax_id) AND checked = 1 AND value > 0 GROUP BY id ORDER BY rank');
    $result_tax->bindParam(':taxes_id', $taxes_id);
    $result_tax->bindParam(':tax_id', $tax_id);
    
    $query_rate = '
        SELECT DISTINCT name, min_nights, max_nights, days, max_adults, max_children, min_people, max_people, id_room, start_date, end_date, ra.price, child_price, discount, discount_type, people, price_sup, fixed_sup, day_start, day_end, id_tax, taxes
        FROM pm_rate as ra, pm_room as ro, pm_package as p, pm_lang as l
        WHERE
            ro.lang = l.id
            AND l.checked = 1
            AND id_package = p.id
            AND id_room = ro.id
            AND ro.checked = 1
            AND stock > 0
            AND (end_lock IS NULL OR end_lock < '.$from_time.' OR
                start_lock IS NULL OR start_lock > '.$to_time.')
            AND start_date <= '.$to_time.'
            AND end_date >= '.$from_time;
    if(!empty($booked)) $query_rate .= ' AND id_room NOT IN('.implode(',', array_keys($booked)).')';
    if(!empty($room_id)) $query_rate .= ' AND ro.id = '.$room_id;
    $query_rate .= '
        ORDER BY min_nights DESC';

    $result_rate = $db->query($query_rate);
    if($result_rate !== false){
        foreach($result_rate as $i => $row){

            $id_room = $row['id_room'];
            $start_date = $row['start_date'];
            $end_date = $row['end_date'];
            $price = $row['price'];
            $child_price = $row['child_price'];
            $discount = $row['discount'];
            $discount_type = $row['discount_type'];
            $people = $row['people'];
            $price_sup = $row['price_sup'];
            $fixed_sup = $row['fixed_sup'];
            $day_start = $row['day_start'];
            $day_end = $row['day_end'];
            $days = explode(',', $row['days']);
            $tax_id = $row['id_tax'];
            $taxes_id = $row['taxes'];
            $min_stay = $row['min_nights'];
            $max_stay = $row['max_nights'];
            $min_people = $row['min_people'];
            $max_people = $row['max_people'];
            $max_adults = $row['max_adults'];
            $max_children = $row['max_children'];
            
            $num_people = $num_adults+$num_children;
            
            if(!isset($res_room[$id_room]['days'])) $res_room[$id_room]['days'] = array();
            
            $from_n = gmdate('N', $from_time);
            $to_n = gmdate('N', $to_time);
            
            $error = false;
            if($num_nights < $min_stay){
                if(!isset($res_room[$id_room]['min_stay'])) $res_room[$id_room]['min_stay'] = $min_stay;
                $error = true;
                $res_room[$id_room]['notice'] = $texts['MIN_NIGHTS'].' : '.$min_stay;
            }
            if($num_adults+$num_children > $max_people){
                $error = true;
                $res_room[$id_room]['notice'] = $texts['MAX_PEOPLE'].' : '.$max_people;
                if(!$strict){
                    $num_adults = $max_adults;
                    $num_children = 0;
                }
            }
            if($num_adults+$num_children < $min_people){
                $error = true;
                $res_room[$id_room]['notice'] = $texts['MIN_PEOPLE'].' : '.$min_people;
                if(!$strict){
                    $num_adults = $min_people;
                    $num_children = 0;
                }
            }
            if($num_adults > $max_adults){
                $error = true;
                $res_room[$id_room]['notice'] = $texts['MAX_ADULTS'].' : '.$max_adults;
                if(!$strict)
                    $num_adults = $max_adults;
            }
            if($num_children > $max_children){
                $error = true;
                $res_room[$id_room]['notice'] = $texts['MAX_CHILDREN'].' : '.$max_children;
                if(!$strict)
                    $num_children = $max_children;
            }
            
            if($error === false || !$strict){
                
                /// package with default conditions
                if(($num_nights >= $min_stay || empty($min_stay))
                && ($num_nights <= $max_stay || empty($max_stay))
                && ($from_n == $day_start || empty($day_start))
                && ($to_n == $day_end || empty($day_end))){
                    
                    // get common period between current rate and selected period
                    $start = ($start_date < $from_time) ? $from_time : $start_date;
                    $end = ($end_date > $to_time) ? $to_time : $end_date;
                    
                    $start = gm_strtotime(gmdate('Y', $start).'-'.gmdate('n', $start).'-'.gmdate('j', $start).' 00:00:00');
                    $end = gm_strtotime(gmdate('Y', $end).'-'.gmdate('n', $end).'-'.gmdate('j', $end).' 00:00:00');
                    
                    // number of nights
                    $nnights = 0;
					if($id_room == 1) var_dump($start);
					if($id_room == 1) var_dump($end);
                    
                    for($date = $start; $date < $end; $date += 86400){
                        
                        $d = gmdate('N', $date);
                        
                        if(!in_array($date, $res_room[$id_room]['days']) && in_array($d, $days)){
                            $res_room[$id_room]['days'][] = $date;
                            $nnights++;
                        }
                    }
				

                    if($num_people > $people){
                        
                        if($people == 0) $price = 0;
                        
                        $extra_adults = ($num_adults > $people) ? $num_adults-$people : 0;
                        $extra_children = ($num_children > 0) ? $num_people-$people-$extra_adults : 0;
                        
                        if($child_price == 0 && $price_sup > 0) $child_price = $price_sup;
                        if($extra_children > 0) $price += $child_price*$extra_children;
                        if($price_sup > 0) $price += $price_sup*$extra_adults;
                    }
                    
                    $price = $nnights*$price;
                    $full_price = $price;
                    if($discount > 0){
                        if($discount_type == 'fixed') $price = $price-($nnights*$discount);
                        elseif($discount_type == 'rate') $price = $price-($price*$discount/100);
                    }

                    if(!isset($res_room[$id_room]['total_nights']) || $res_room[$id_room]['total_nights']+$nnights <= $num_nights){
                        
                        if(!isset($res_room[$id_room]['amount'])) $res_room[$id_room]['amount'] = 0;
                        $res_room[$id_room]['amount'] += $price;
                        
                        if(!isset($res_room[$id_room]['full_price'])) $res_room[$id_room]['full_price'] = 0;
                        $res_room[$id_room]['full_price'] += $full_price;
                        
                        if(!isset($res_room[$id_room]['total_nights'])) $res_room[$id_room]['total_nights'] = 0;
                        $res_room[$id_room]['total_nights'] += $nnights;
                        
                        if(!isset($res_room[$id_room]['fixed_sup'])) $res_room[$id_room]['fixed_sup'] = 0;
                        if($fixed_sup > $res_room[$id_room]['fixed_sup'])
                            $res_room[$id_room]['fixed_sup'] = $fixed_sup;
                        
                        $duty_free = $price;
                        $duty_free_sup = $fixed_sup;
                        if($result_incl_tax->execute() !== false && $db->last_row_count() > 0){
                            $incl_tax = $result_incl_tax->fetch();
                            $duty_free = $price/($incl_tax['value']/100+1);
                            $duty_free_sup = $res_room[$id_room]['fixed_sup']/($incl_tax['value']/100+1);
                        }
                        
                        if(!isset($res_room[$id_room]['duty_free'])) $res_room[$id_room]['duty_free'] = 0;
                        $res_room[$id_room]['duty_free'] += $duty_free;
                        $res_room[$id_room]['duty_free_sup'] = $duty_free_sup;
                        
                        if($result_tax->execute() !== false){
                            foreach($result_tax as $tax){
                                if(!isset($res_room[$id_room]['taxes'][$tax['id']]['amount'])) $res_room[$id_room]['taxes'][$tax['id']]['amount'] = 0;
                                $res_room[$id_room]['taxes'][$tax['id']]['amount'] += $duty_free*($tax['value']/100);
                                $res_room[$id_room]['taxes'][$tax['id']]['fixed_sup'] = $duty_free_sup*($tax['value']/100);
                            }
                        }
                        
                        $res_room[$id_room]['min_stay'] = ((isset($res_room[$id_room]['min_stay']) && $min_stay > $res_room[$id_room]['min_stay']) || !isset($res_room[$id_room]['min_stay'])) ? $min_stay : 0;
                        if($num_nights < $res_room[$id_room]['min_stay']){
                            $res_room[$id_room]['error'] = true;
                            $res_room[$id_room]['notice'] = $texts['MIN_NIGHTS'].' : '.$res_room[$id_room]['min_stay'];
                        }
                    }
                }
            }else
                if($error) $res_room[$id_room]['error'] = true;
        }
        
        foreach($res_room as $id_room => $result){
            if(!isset($result['amount']) || $result['amount'] == 0 || $result['total_nights'] != $num_nights) $res_room[$id_room]['error'] = true;
            elseif(isset($res_room[$id_room]['error'])) unset($res_room[$id_room]['error']);
        }
    }
    return $res_room;
}

function getBookingSummary($room_id = null, $index = null, $amount = 0, $available = true)
{
    global $texts;
    
    $html = '';
    $total = 0;
    $num_rooms = 0;
    $num_adults = 0;
    $num_children = 0;
    if(isset($_POST['amount']) && is_array($_POST['amount'])){
        foreach($_POST['amount'] as $id_room => $values){
            foreach($values as $i => $value){
                if(isset($_POST['num_adults'][$id_room][$i]) && isset($_POST['num_children'][$id_room][$i])){
                    $adults = $_POST['num_adults'][$id_room][$i];
                    $children = $_POST['num_children'][$id_room][$i];
                    if(($adults+$children) > 0){
                        $num_adults += $adults;
                        $num_children += $children;
                        $num_rooms++;
                        if($id_room != $room_id || $i != $index) $total += $value;
                    }
                }
            }
        }
    }
    if(!is_null($room_id)){
        $total += $amount;
        if(isset($_POST['num_adults'][$room_id][$index]) && isset($_POST['num_children'][$room_id][$index])){
            $adults = $_POST['num_adults'][$room_id][$index];
            $children = $_POST['num_children'][$room_id][$index];
            if(($adults+$children) > 0 && $amount > 0){
                $num_adults += $adults;
                $num_children += $children;
                $num_rooms++;
            }
        }
    }
    $persons = $num_adults+$num_children;
    
    if($total > 0){
        $html .= '
        <big><i class="fas fa-fw fa-tags"></i> <b>'.$num_rooms.'</b></big> '.getAltText($texts['ROOM'], $texts['ROOMS'], $num_rooms).' 
        <big><i class="fas fa-fw fa-male"></i> <b>'.$persons.'</b></big> '.getAltText($texts['PERSON'], $texts['PERSONS'], $persons).'<i class="fas fa-fw fa-caret-right"></i>';
                
        if($num_adults > 0) $html .= ' <big><b>'.$num_adults.'</b></big> '.getAltText($texts['ADULT'], $texts['ADULTS'], $num_adults);
        if($num_children > 0) $html .= ' / <big><b>'.$num_children.'</b></big> '.getAltText($texts['CHILD'], $texts['CHILDREN'], $num_children);
        
        $html .= '
        <input type="hidden" name="adults" value="'.$num_adults.'">
        <input type="hidden" name="children" value="'.$num_children.'">
        <button name="book" id="btn-book" class="btn btn-success btn-lg btn-block mt5"><b>'.$texts['TOTAL'].' '.formatPrice($total*CURRENCY_RATE).'</b> <i class="fas fa-fw fa-hand-point-right"></i> '.$texts['BOOK'].'</button>';
    
    }elseif(ENABLE_BOOKING_REQUESTS == 1 && (isset($_POST['unavailable_rooms']) || !$available)){
        $html .= '
        <input type="hidden" name="adults" value="'.$_SESSION['num_adults'].'">
        <input type="hidden" name="children" value="'.$_SESSION['num_children'].'">
        <button name="request" class="btn btn-default btn-lg btn-block mt5"><i class="fas fa-fw fa-comment"></i> '.$texts['MAKE_A_REQUEST'].'</small></button>';
    }
    
    return $html;
}
