<?php
require_once('../../../common/lib.php');
require_once('../../../common/define.php');
$response = array('html' => '', 'notices' => array(), 'error' => '', 'success' => '');

if(isset($db) && $db !== false){
        
    if(isset($_POST['activity']) && is_numeric($_POST['activity'])){
        
        $activity_id = $_POST['activity'];
        
        if($activity_id > 0 && isset($_POST['adults_'.$activity_id]) && isset($_POST['children_'.$activity_id]) && isset($_POST['date_'.$activity_id])
        && is_numeric($_POST['adults_'.$activity_id]) && is_numeric($_POST['children_'.$activity_id]) && is_numeric($_POST['date_'.$activity_id])){

            $adults = $_POST['adults_'.$activity_id];
            $children = $_POST['children_'.$activity_id];
            $date = $_POST['date_'.$activity_id];
            $day = gmdate('j', $date);
            $month = gmdate('n', $date);
            $year = gmdate('Y', $date);
            $n = ((gmdate('w', $date)+6)%7)+1;
            
            $date = gm_strtotime($year.'-'.$month.'-'.$day.' 00:00:00');
            
            $people = $adults+$children;
            
            $amount = 0;
            $duty_free = 0;
            $full_price = 0;
            $taxes = array();

            $bookings = array();
            $sessions = array();

            $result_session = $db->query('
                            SELECT DISTINCT id_tax, taxes, discount, discount_type, s.price, price_child, start_date, end_date, days, id_activity_session, start_h, start_m, max(max_people) as people
                            FROM pm_activity as a, pm_activity_session as s, pm_activity_session_hour as h, pm_lang as l
                            WHERE
                                a.lang = l.id
                                AND l.checked = 1
                                AND id_activity = a.id
                                AND a.checked = 1
                                AND start_date <= '.$date.' AND end_date >= '.$date.'
                                AND id_activity_session = s.id
                                AND id_activity = '.$activity_id.'
                            GROUP BY h.id');
            if($result_session !== false){
                
                $tax_id = 0;
                $result_incl_tax = $db->prepare('SELECT * FROM pm_tax WHERE id = :tax_id AND checked = 1 AND value > 0 GROUP BY id ORDER BY rank LIMIT 1');
                $result_incl_tax->bindParam(':tax_id', $tax_id);
                
                $taxes_id = '';
                $result_tax = $db->prepare('SELECT * FROM pm_tax WHERE (FIND_IN_SET(id, :taxes_id) OR id = :tax_id) AND checked = 1 AND value > 0 GROUP BY id ORDER BY rank');
                $result_tax->bindParam(':taxes_id', $taxes_id);
                $result_tax->bindParam(':tax_id', $tax_id);
        
                foreach($result_session as $i => $row){
                    $start_h = $row['start_h'];
                    $start_m = $row['start_m'];
                    $max_people = $row['people'];
                    $price_adult = $row['price'];
                    $price_child = $row['price_child'];
                    $discount = $row['discount'];
                    $discount_type = $row['discount_type'];
                    $tax_id = $row['id_tax'];
                    $taxes_id = $row['taxes'];
                    $opening_days = explode(',', $row['days']);
                    
                    if($amount == 0){
                        $amount = ($adults*$price_adult)+($children*$price_child);
                        $full_price = $amount;
                        if($discount > 0){
                            if($discount_type == 'fixed') $amount = $amount-$discount;
                            elseif($discount_type == 'rate') $amount = $amount-($amount*$discount/100);  
                        }
                        
                        $duty_free = $amount;
                        if($result_incl_tax->execute() !== false && $db->last_row_count() > 0){
                            $incl_tax = $result_incl_tax->fetch();
                            $duty_free = $amount/($incl_tax['value']/100+1);
                        }
                        
                        if($result_tax->execute() !== false){
                            foreach($result_tax as $tax)
                                $taxes[$tax['id']] = $duty_free*($tax['value']/100);
                        }
                    }
                    
                    $time = gm_strtotime($year.'-'.$month.'-'.$day.' '.$start_h.':'.$start_m.':00');
                    
                    if($people <= $max_people){
                        if(in_array($n, $opening_days) && $time > time()+86400) $sessions[$time] = gmstrftime(TIME_FORMAT, $time);
                    }
                }
            }
            
            ksort($sessions);
            
            $result_book = $db->query('
                            SELECT date, max_people, ba.adults, ba.children, id_activity, from_date, to_date
                            FROM pm_booking as b, pm_booking_activity as ba, pm_activity as a
                            WHERE
                                lang = '.DEFAULT_LANG.'
                                AND id_booking = b.id
                                AND id_activity = a.id
                                AND (status = 4 OR (status = 1 AND (add_date > '.(time()-900).' OR payment_option IN(\'arrival\',\'check\'))))
                                AND date IN('.implode(',', $sessions).')
                                AND id_activity = '.$activity_id.'
                            GROUP BY ba.id');
            if($result_book !== false){
                foreach($result_book as $i => $row){
                    $date = $row['date'];
                    $max_people = $row['max_people'];
                    $num_adults = $row['adults'];
                    $num_children = $row['children'];
                    
                    $num_people = $num_adults+$num_children;
                    
                    $bookings[$date] = isset($bookings[$date]) ? $bookings[$date]+$num_people : $num_people;
                    
                    if($bookings[$date]+$people > $max_people && array_key_exists($date, $sessions)) unset($sessions[$date]);
                }
            }
            if(!empty($sessions) && $amount > 0){
                $response['html'] .= '
                <div class="form-group">
                    <div class="input-group input-group-sm">
                        <div class="input-group-addon"><i class="fas fa-fw fa-clock"></i> '.$texts['TIMESLOT'].'</div>
                            <select name="session_date_'.$activity_id.'" class="form-control selectpicker">';
                                foreach($sessions as $date => $hour)
                                    $response['html'] .= '<option value="'.$date.'">'.$hour.'</option>';
                
                                $response['html'] .= '
                            </select>
                        </div>
                    </div>
                </div>
                <div class="price">
                    <span>'.formatPrice($amount*CURRENCY_RATE).'</span>';
                    if($full_price > 0 && $full_price > $amount)
                        $response['html'] .= '<br><s class="text-warning">'.formatPrice($full_price*CURRENCY_RATE).'</s>';
                    $response['html'] .= '
                </div>
                <span class="mb10 text-muted">'.$texts['PRICE'].' / '.$people.' '.$texts['PERSONS'].'</span>
                <input type="hidden" name="amount_'.$activity_id.'" value="'.number_format($amount, 2, '.', '').'">
                <input type="hidden" name="duty_free_'.$activity_id.'" value="'.number_format($duty_free, 2, '.', '').'">';
                foreach($taxes as $tax_id => $tax_amount)
                    $response['html'] .= '<input type="hidden" name="taxes_'.$activity_id.'['.$tax_id.']" value="'.number_format($tax_amount, 2, '.', '').'">';
            }
        }
    }
}
echo json_encode($response);
