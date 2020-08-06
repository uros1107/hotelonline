<html>
<?php
define("ADMIN", true);

define("SYSBASE", str_replace("\\", "/", realpath(dirname(__FILE__)."/../../../../")."/"));

require_once(SYSBASE."common/lib.php");
require_once(SYSBASE."common/define.php");

if(!isset($_SESSION['user'])) exit();
elseif($_SESSION['user']['type'] == "registered"){
    unset($_SESSION['user']);
    exit();
}

if(isset($_POST['date']) && isset($_SESSION['user']['id'])){
    $date = (int)$_POST['date'];
    if(is_numeric($date)){ ?>
        <head>
        <script>
            function printElem(elem){
                var popup = window.open('', 'print', 'height=800,width=600');
                popup.document.write('<html><head><title>'+document.title+'</title><link rel="stylesheet" href="<?php echo DOCBASE.ADMIN_FOLDER.'/css/print.css'; ?>"/></head><body>'+document.getElementById(elem).innerHTML+'</body></html>');
                setTimeout(function(){ 
                    popup.document.close();
                    popup.focus();
                    popup.print();
                    popup.close();    
                }, 600);
                return true;
            }
        </script>
        <style>
             .white-popup-block {
                 width:850px;
                 max-width:none;
            }
        </style>
        </head>
        <body>
        <div class="white-popup-block" id="popup-check-in-out-<?php echo $date; ?>">
            <?php
            $start_d = $date;
            $end_d = strtotime(date('Y', $date).'-'.date('m', $date).'-'.date('d', $date).' 23:59:59');

            $id_booking = 0;
            $result_booking_room = $db->prepare('SELECT * FROM pm_booking_room WHERE id_booking = :bookid');
            $result_booking_room->bindParam(':bookid', $id_booking);
                    
            $result_booking_in = $db->query('
                SELECT *
                FROM pm_booking
                WHERE
                    (status = 4 OR (status = 1 AND (add_date > '.(time()-900).' OR payment_option IN(\'arrival\',\'check\'))))
                    AND from_date >= '.$start_d.'
                    AND from_date <= '.$end_d);

            $result_booking_out = $db->query('
                SELECT b.id as bookid, br.title as room, firstname, lastname, down_payment, total, br.adults as adults, br.children as children
                FROM pm_booking as b, pm_booking_room as br
                WHERE
                    br.id_booking = b.id
                    AND (status = 4 OR (status = 1 AND (add_date > '.(time()-900).' OR payment_option IN(\'arrival\',\'check\'))))
                    AND to_date >= '.$start_d.'
                    AND to_date <= '.$end_d);

            $id_booking = 0;
            $result_service = $db->prepare('SELECT * FROM pm_booking_service WHERE id_booking = :id_booking');
            $result_service->bindParam(':id_booking', $id_booking);
            
            echo '
            <h2>'.$texts['CHECK_IN'].' / '.$texts['CHECK_OUT'].'<br><small><b>'.strftime(DATE_FORMAT, $date).'</b></small></h2>
            <a href="#" onclick="javascript:printElem(\'popup-check-in-out-'.$date.'\');return false;" class="pull-right print-btn"><i class="fa fa-print"></i></a>
            
            <h3 class="text-center">'.$texts['CHECK_IN'].'</h3>
            <div class="table-responsive">
                <table class="table table-stiped">
                    <tr>
                        <th>'.$texts['ROOMS'].'</th>
                        <th>'.$texts['CUSTOMER'].'</th>
                        <th>'.$texts['PERSONS'].'</th>
                        <th>'.$texts['TOTAL'].'</th>
                        <th>'.$texts['BALANCE'].'</th>
                        <th>'.$texts['SERVICES'].'</th>
                    </tr>';
                        
                    if($result_booking_in !== false){
                        foreach($result_booking_in as $i => $row){
                            $id_booking = $row['id'];
                            $customer = $row['firstname'].' '.$row['lastname'];
                            $down_payment = $row['down_payment'];
                            $total = $row['total'];
                            $status = $row['status'];
                            $balance = ($status == 4) ? $total-$down_payment : $total;
                            $people = $row['adults']+$row['children'];
                            
                            echo '
                            <tr>
                                <td class="text-left">';
                                    if($result_booking_room->execute() !== false){
                                        foreach($result_booking_room as $room){
                                            echo $room['title'].' | 
                                            '.($room['adults']+$room['children']).' '.getAltText($texts['PERSON'], $texts['PERSONS'], ($room['adults']+$room['children'])).' (';
                                            if($room['adults'] > 0) echo $room['adults'].' '.getAltText($texts['ADULT'], $texts['ADULTS'], $room['adults']).' ';
                                            if($room['children'] > 0) echo $room['children'].' '.getAltText($texts['CHILD'], $texts['CHILDREN'], $room['children']);
                                            echo ')<br>';
                                        }
                                    }
                                    echo '
                                </td>
                                <td class="text-left">'.$customer.'</td>
                                <td class="text-center">'.$people.'</td>
                                <td class="text-center">'.formatPrice($total).'</td>
                                <td class="text-center">'.formatPrice($balance).'</td>
                                <td class="text-center">';
                                    if($result_service->execute() !== false && $db->last_row_count() > 0){
                                        foreach($result_service as $service){
                                            echo $service['title'].' (x'.$service['qty'].')<br>';
                                        }
                                    }
                                    echo '
                                </td>
                            </tr>';
                        }
                    }else{
                        echo '
                        <tr>
                            <td colspan="6">-</td>
                        </tr>';
                    }
                    echo '
                </table>
            </div>
            <h3 class="text-center">'.$texts['CHECK_OUT'].'</h3>
            <div class="table-responsive">
                <table class="table table-stiped">
                    <tr>
                        <th>'.$texts['ROOMS'].'</th>
                        <th>'.$texts['CUSTOMER'].'</th>
                    </tr>';
                    
                    if($result_booking_out !== false){
                        foreach($result_booking_out as $i => $row){
                            $room = $row['room'];
                            $customer = $row['firstname'].' '.$row['lastname'];
                            
                            echo '
                            <tr>
                                <td class="text-left">'.$room.'</td>
                                <td class="text-left">'.$customer.'</td>
                            </tr>';
                        }
                    }else{
                        echo '
                        <tr>
                            <td colspan="2">-</td>
                        </tr>';
                    }
                    echo '
                </table>
            </div>'; ?>
        </div>
        </body>
        <?php
    }
} ?>
</html>
