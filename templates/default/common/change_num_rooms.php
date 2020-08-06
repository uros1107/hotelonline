<?php
require_once('../../../common/lib.php');
require_once('../../../common/define.php');
require_once('functions.php');
$response = array('html' => '', 'notices' => array(), 'error' => '', 'success' => '', 'extraHtml' => '');

$response['extraHtml'] = getBookingSummary();

if(isset($db) && $db !== false){
    
    if(isset($_POST['room']) && is_numeric($_POST['room'])){
        
        $room_id = $_POST['room'];
        
        if(isset($_POST['num_rooms'][$room_id])){
                    
            $result_room = $db->query('SELECT * FROM pm_room WHERE id = '.$room_id.' AND checked = 1 AND lang = '.LANG_ID);
            if($result_room !== false && $db->last_row_count() > 0){
                $row = $result_room->fetch();
                
                $max_adults = $row['max_adults'];
                $max_children = $row['max_children'];
                $hotel_id = $row['id_hotel'];
                        
                $num_rooms = $_POST['num_rooms'][$room_id];
                
                for($i = 0; $i < $num_rooms; $i++){
                    $response['html'] .= '
                    <div class="mb5 mt5 bg-success">
                        <div class="col-md-3 pt10 text-center"><b>'.$texts['ROOM'].' #'.($i+1).'</b></div>
                        <div class="col-md-3 col-lg-2 pt5 pb5">
                            <div class="input-group input-group-sm">
                                <div class="input-group-addon"><i class="fas fa-fw fa-male"></i> '.$texts['ADULTS'].'</div>
                                <select name="num_adults['.$room_id.']['.$i.']" class="form-control sendAjaxForm selectpicker" data-extratarget="#booking-amount_'.$hotel_id.'" data-action="'.getFromTemplate('common/change_num_people.php').'?index='.$i.'&id_room='.$room_id.'&id_hotel='.$hotel_id.'" data-target="#room-result-'.$room_id.'-'.$i.'">
                                    <option value="0">-</option>';
                                    for($j = 1; $j <= $max_adults; $j++){
                                        $response['html'] .= '<option value="'.$j.'">'.$j.'</option>';
                                    }
                                    $response['html'] .= '
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-2 pt5 pb5">
                            <div class="input-group input-group-sm">
                                <div class="input-group-addon"><i class="fas fa-fw fa-male"></i> '.$texts['CHILDREN'].'</div>
                                <select name="num_children['.$room_id.']['.$i.']" class="form-control sendAjaxForm selectpicker" data-extratarget="#booking-amount_'.$hotel_id.'" data-action="'.getFromTemplate('common/change_num_people.php').'?index='.$i.'&id_room='.$room_id.'&id_hotel='.$hotel_id.'" data-target="#room-result-'.$room_id.'-'.$i.'">
                                    <option value="0">-</option>';
                                    for($j = 1; $j <= $max_children; $j++){
                                        $response['html'] .= '<option value="'.$j.'">'.$j.'</option>';
                                    }
                                    $response['html'] .= '
                                </select>
                            </div>
                            <div id="children-options-'.$room_id.'-'.$i.'"></div>
                        </div>
                        <div class="col-md-3 col-lg-5 pt5 pb5" id="room-result-'.$room_id.'-'.$i.'"></div>
                        <di class="clearfix"></di>
                    </div>';
                }
            }
        }
    }
}
echo json_encode($response);
