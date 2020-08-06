<?php
// Render iCal formatted bookings for external OTAs

require_once('../../common/lib.php');
require_once('../../common/define.php');
require_once('zapcallib.php');

$icalobj = new ZCiCal();

if(isset($db) && $db !== false){

	if(isset($_GET['room']) && is_numeric($_GET['room']) && isset($_GET['uid']) && is_numeric($_GET['uid'])){
		
		$user_date = substr($_GET['uid'], -10);
		$user_id = str_replace($user_date, '', $_GET['uid']);
		
		$id_room = $_GET['room'];
		$time = gmtime();
		
		$result_book = $db->query('
            SELECT r.title as room_title, stock, br.id_room as room_id, from_date, to_date
            FROM pm_booking as b, pm_booking_room as br, pm_room as r, pm_user as u
            WHERE
                lang = '.DEFAULT_LANG.'
                AND u.id IN (r.users)
                AND r.users REGEXP \'(^|,)'.$user_id.'(,|$)\'
                AND u.add_date = '.$user_date.'
                AND br.id_room = r.id
                AND br.id_booking = b.id
                AND (status = 4 OR (status = 1 AND (b.add_date > '.(time()-900).' OR payment_option IN(\'arrival\',\'check\'))))
                AND to_date >= '.$time.'
				AND id_room = '.$id_room.'
			GROUP BY br.id
		
			UNION ALL
			
			SELECT r.title as room_title, (r.stock-rc.stock)+1 as stock, r.id as room_id, from_date, to_date
			FROM pm_room as r, pm_room_closing as rc, pm_user as u
			WHERE
				lang = '.DEFAULT_LANG.'
                AND u.id IN (r.users)
                AND r.users REGEXP \'(^|,)'.$user_id.'(,|$)\'
                AND u.add_date = '.$user_date.'
				AND rc.id_room = r.id
				AND r.checked = 1
                AND to_date >= '.$time.'
				AND r.id = '.$id_room);
			
		if($result_book !== false){
			foreach($result_book as $row){

				$event_start = gmdate('Y-m-d H:i:s', $row['from_date']);
				$event_end = gmdate('Y-m-d H:i:s', $row['to_date']);

				$eventobj = new ZCiCalNode('VEVENT', $icalobj->curnode);

				$eventobj->addNode(new ZCiCalDataNode('SUMMARY:'.'Booking: '.$row['room_title'].' - '.SITE_TITLE));
				$eventobj->addNode(new ZCiCalDataNode('DTSTART:'.ZCiCal::fromSqlDateTime($event_start)));
				$eventobj->addNode(new ZCiCalDataNode('DTEND:'.ZCiCal::fromSqlDateTime($event_end)));
				$uid = date('Y-m-d-H-i-s').'@'.getUrl(true);
				$eventobj->addNode(new ZCiCalDataNode('UID:'.$uid));
				$eventobj->addNode(new ZCiCalDataNode('DTSTAMP:'.ZCiCal::fromSqlDateTime()));
				$eventobj->addNode(new ZCiCalDataNode('Description:'.ZCiCal::formatContent('')));
			}
		}
	}
}

header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: inline; filename=calendar.ics');
echo $icalobj->export();
