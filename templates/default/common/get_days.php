<?php
require_once('../../../common/lib.php');
require_once('../../../common/define.php');

if(isset($db) && $db !== false){
    
    if(isset($_POST['currentMonth']) && is_numeric($_POST['currentMonth'])
    && isset($_POST['currentYear']) && is_numeric($_POST['currentYear'])){

        $currentMonth = $_POST['currentMonth'];
        $currentYear = $_POST['currentYear'];
        
        if(isset($_POST['room']) && is_numeric($_POST['room'])) $room_id = $_POST['room'];
        else $room_id = 0;

        $bookings = array();
        $days = array('booked' => array(), 'free' => array());
        
        $start_month = gm_strtotime($currentYear.'-'.$currentMonth.'-1 00:00:00');
        $nb_days = gmdate('t', $start_month);
        $end_month = gm_strtotime($currentYear.'-'.$currentMonth.'-'.$nb_days.' 00:00:00');
        
        $query_rate = '
            SELECT start_date, end_date, stock, days
            FROM pm_rate, pm_room as r, pm_package as p
            WHERE id_room = r.id
                AND id_package = p.id
                AND start_date <= '.$end_month.'
                AND end_date >= '.$start_month;
        if($room_id != 0) $query_rate .= ' AND id_room = '.$room_id;
        $result_rate = $db->query($query_rate);
        if($result_rate !== false){
            foreach($result_rate as $i => $row){
                $start_date = $row['start_date'];
                $end_date = $row['end_date'];
                $stock = $row['stock'];
                $w_days = explode(',', $row['days']);

                $start = ($start_date < $start_month) ? $start_month : $start_date;
                $end = ($end_date > $end_month) ? $end_month : $end_date;
                
                $d = (int)gmdate('j', $start);
                
                for($date = $start; $date <= $end; $date += 86400){
                    $wd = (int)gmdate('N', $date);
                    
                    if($stock > 0 && in_array($wd, $w_days) && !in_array($d, $days['free'])) $days['free'][] = $d;
                    $d++;
                }
            }
        }
        
        $query_book = '
            SELECT stock, br.id_room as room_id, from_date, to_date
            FROM pm_booking as b, pm_booking_room as br, pm_room as r
            WHERE
                lang = '.DEFAULT_LANG.'
                AND br.id_room = r.id
                AND br.id_booking = b.id
                AND (status = 4 OR (status = 1 AND (add_date > '.(time()-900).' OR payment_option IN(\'arrival\',\'check\'))))
                AND from_date <= '.$end_month.'
                AND to_date >= '.$start_month;
				if($room_id != 0) $query_book .= '
				AND id_room = '.$room_id;
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
                AND from_date <= '.$end_month.'
                AND to_date >= '.$start_month;
				if(!empty($room_id)) $query_book .= '
				AND r.id = '.$room_id;
				
				$query_book .= '
			
			UNION ALL
			
			SELECT stock, r.id as room_id, from_date, to_date
			FROM pm_room as r, pm_ical_event as ev
			WHERE
				lang = '.DEFAULT_LANG.'
				AND ev.id_room = r.id
				AND r.checked = 1
				AND from_date <= '.$end_month.'
				AND to_date >= '.$start_month;
				if(!empty($room_id)) $query_book .= '
				AND r.id = '.$room_id;
        
        $result_book = $db->query($query_book);
        if($result_book !== false){
            foreach($result_book as $i => $row){
                $start_date = $row['from_date'];
                $end_date = $row['to_date'];
                $stock = $row['stock'];

                $start = ($start_date < $start_month) ? $start_month : $start_date;
                $end = ($end_date > $end_month) ? $end_month : $end_date;
                
                $d = (int)gmdate('j', $start);
                
                for($date = $start; $date < $end; $date += 86400){
                    
                    $bookings[$d] = isset($bookings[$d]) ? $bookings[$d]+1 : 1;
                    if($bookings[$d] >= $stock && !in_array($d, $days['booked'])) $days['booked'][] = $d;
                    $d++;
                }
            }
        }
        echo json_encode($days);
    }
}
