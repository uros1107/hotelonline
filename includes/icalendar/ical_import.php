<?php
/**
 * Script called (Ajax) on customer update
 * imports bookings from external OTAs iCal calendars
 */
session_start();

$mode = (isset($_POST['ical_sync_mode'])) ? $_POST['ical_sync_mode'] : null;

if($mode == 'manual') define('ADMIN', true);
require_once('../../common/lib.php');
require_once('../../common/define.php');

$response = array('html' => '', 'notices' => array(), 'error' => '', 'success' => '', 'extraHtml' => '');

if(isset($db) && $db !== false && ENABLE_ICAL){
				
	$handle_sync = false;
	$sync_time = null;
	$interval = null;
	$condition = '';
	$time = gmtime();
		
	if($mode == 'manual'){
		if(isset($_SESSION['user']) && $_SESSION['user']['type'] == 'administrator')
			$handle_sync = true;
			
	}elseif($mode == 'auto' && ENABLE_AUTO_ICAL_SYNC){
		
		$condition = ' AND latest_sync IS NULL OR latest_sync = 0';
		
		switch(ICAL_SYNC_INTERVAL){
			case 'daily':
				$sync_time = gmmktime(ICAL_SYNC_CLOCK, 0, 0, gmdate('n', $time), gmdate('d', $time), gmdate('Y', $time));
				if(!empty($sync_time) && $sync_time < $time){
					$condition .= ' AND ('.(gmdate('H', $time) >= ICAL_SYNC_CLOCK).' = 1 OR latest_sync <= '.($time-86400).')';
					$handle_sync = true;
				}
			break;
			case 'hourly':
				$condition .= ' AND latest_sync <= '.($time-3600);
				$handle_sync = true;
			break;
		}
	}
	
	if($handle_sync){

		require_once('zapcallib.php');

		$id_room = (isset($_POST['room']) && is_numeric($_POST['room'])) ? $_POST['room'] : null;
		$all = (isset($_POST['ical_sync_all']) && is_numeric($_POST['ical_sync_all'])) ? $_POST['ical_sync_all'] : null;
		if(empty($all) && $mode == 'auto' && ENABLE_AUTO_ICAL_SYNC) $all = 1;
		
		if(!empty($id_room) && $all != 1) $condition .= 'id_room = '.$_POST['room'];
		
		if($mode == 'manual'){
			$query_delete = 'DELETE FROM pm_ical_event';
			if(!empty($id_room)) $query_delete .= ' WHERE id_room = '.$id_room;
			$db->query($query_delete);
		}
		
		$total = 0;
		
		$result_calendar = $db->query('SELECT * FROM pm_room_calendar WHERE url != \'\''.$condition);
		if($result_calendar !== false && $db->last_row_count() > 0){
			
			if($mode == 'auto') $db->query('DELETE FROM pm_ical_event');
			
			foreach($result_calendar as $row){
				$icalfile = $row['url'];
				$ical_id = $row['id'];
				$ical_title = $row['title'];
				$id_room = $row['id_room'];
				
				$icalfeed = file_get_contents($icalfile);
				
				if($icalfeed !== false){

					// create the ical object
					$icalobj = new ZCiCal($icalfeed) or die();

					//echo "Number of events found: " . $icalobj->countEvents() . "\n";

					$ecount = 0;

					// read back icalendar data that was just parsed
					if(isset($icalobj->tree->child)){
						foreach($icalobj->tree->child as $node){
							if($node->getName() == 'VEVENT'){
								$ecount++;
								
								//echo "Event $ecount:\n";
								
								$data = array();
								$data['id'] = null;
								$data['title'] = $ical_title;
								$data['sync_date'] = $time;
								$data['id_room'] = $id_room;
								
								foreach($node->data as $key => $value){
									if($key == 'DTSTART' || $key == 'DTEND'){
										$date = $value->getValues().' 00:00:00';
										
										$date = substr_replace($date, '-', 4, 0);
										$date = substr_replace($date, '-', 7, 0);
										$date = gm_strtotime($date);
										
										if($key == 'DTSTART') $data['from_date'] = $date;
										if($key == 'DTEND') $data['to_date'] = $date;
									}
								}
								$result_insert = db_prepareInsert($db, 'pm_ical_event', $data);
								$result_insert->execute();
							}
						}
					}
					$total += $ecount;
				}
			}
			$db->query('UPDATE pm_room_calendar SET latest_sync = '.$time.' WHERE id = '.$ical_id);
		}
		if($mode == 'manual') $response['success'] .= $texts['NUM_IMPORTED_EVENTS'].' '.$total;
	}
}
echo json_encode($response);
