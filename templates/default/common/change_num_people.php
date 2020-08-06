<?php
require_once('../../../common/lib.php');
require_once('../../../common/define.php');
require_once('functions.php');
$response = array('html' => '', 'notices' => array(), 'error' => '', 'success' => '', 'extraHtml' => '');

if(isset($db) && $db !== false){
    
    $room_id = null;
    $index = null;
    $amount = 0;
    $available = true;
    
    if(isset($_POST['id_room']) && is_numeric($_POST['id_room'])
    && isset($_POST['id_hotel']) && is_numeric($_POST['id_hotel'])
    && isset($_POST['index']) && is_numeric($_POST['index'])
    && isset($_POST['from_time']) && is_numeric($_POST['from_time'])
    && isset($_POST['to_time']) && is_numeric($_POST['to_time'])){
        
        $hotel_id = $_POST['id_hotel'];
        $room_id = $_POST['id_room'];
        $index = $_POST['index'];
        $from_time = $_POST['from_time'];
        $to_time = $_POST['to_time'];
        $num_adults = (isset($_POST['num_adults'][$room_id][$index]) && is_numeric($_POST['num_adults'][$room_id][$index])) ? $_POST['num_adults'][$room_id][$index] : 0;
        //$num_children = (isset($_POST['num_children'][$room_id][$index]) && is_numeric($_POST['num_children'][$room_id][$index])) ? $_POST['num_children'][$room_id][$index] : 0;
        $children_age = (isset($_POST['child_age'][$room_id][$index]) && is_array($_POST['child_age'][$room_id][$index])) ? array_filter($_POST['child_age'][$room_id][$index]) : array();
        
        $num_nights = ($to_time-$from_time)/86400;
    
        $res_hotel = getRoomsResult($from_time, $to_time, $num_adults, $children_age, true, $room_id);
        
        if(!isset($res_hotel[$hotel_id][$room_id]) || (isset($res_hotel[$hotel_id][$room_id]) && isset($res_hotel[$hotel_id][$room_id]['error']) && !isset($res_hotel[$hotel_id][$room_id]['notice']))){
            $response['html'] .= '<span class="label label-danger"><i class="fa fa-exclamation-triangle"></i> '.$texts['NO_AVAILABILITY'].'</small></span>
            <input type="hidden" name="unavailable_rooms[]" value="'.$room_id.'">';
            $available = false;
        }elseif(isset($res_hotel[$hotel_id][$room_id]) && isset($res_hotel[$hotel_id][$room_id]['error']) && isset($res_hotel[$hotel_id][$room_id]['notice']))
            $response['html'] .= '<span class="label label-danger"><i class="fa fa-exclamation-triangle"></i> <small>'.$res_hotel[$hotel_id][$room_id]['notice'].'</small></span>';
        elseif(isset($res_hotel[$hotel_id][$room_id]) && !isset($res_hotel[$hotel_id][$room_id]['error'])){
            $amount = $res_hotel[$hotel_id][$room_id]['amount']+$res_hotel[$hotel_id][$room_id]['fixed_sup'];
            $full_price = $res_hotel[$hotel_id][$room_id]['full_price']+$res_hotel[$hotel_id][$room_id]['fixed_sup'];
            $type = $num_nights.' '.getAltText($texts['NIGHT'], $texts['NIGHTS'], $num_nights);
            $response['html'] .= '<span><strong>'.formatPrice($amount*CURRENCY_RATE).'</strong></span> ';
            if($full_price > 0 && $full_price > $amount)
                $response['html'] .= '<s class="text-warning">'.formatPrice($full_price*CURRENCY_RATE).'</s> ';
            
            $response['html'] .= ' - <span class="text-muted"> '.$type.'</span>
            <input type="hidden" name="amount['.$room_id.']['.$index.']" value="'.number_format($amount, 10, '.', '').'">
            <input type="hidden" name="duty_free['.$room_id.']['.$index.']" value="'.number_format($res_hotel[$hotel_id][$room_id]['duty_free']+$res_hotel[$hotel_id][$room_id]['duty_free_sup'], 10, '.', '').'">';
                            
            if(isset($res_hotel[$hotel_id][$room_id]['taxes'])){
                foreach($res_hotel[$hotel_id][$room_id]['taxes'] as $tax_id => $tax){
                    $tax_amount = $tax['amount'];
                    if(isset($tax['fixed_sup'])) $tax_amount += $tax['fixed_sup'];
                    $response['html'] .= '<input type="hidden" name="taxes['.$room_id.']['.$index.']['.$tax_id.']" value="'.number_format($tax_amount, 10, '.', '').'">';
                }
            }
            $response['html'] .= ' <i class="fa fa-check-circle text-success"></i> <a href="#btn-book_'.$hotel_id.'" class="btn btn-sm"><i class="fa fa-arrow-up"></i> '.$texts['BOOK'].' </a>';
        }
    }
    $response['extraHtml'] = getBookingSummary($room_id, $index, $amount, $available);
}

echo json_encode($response);
