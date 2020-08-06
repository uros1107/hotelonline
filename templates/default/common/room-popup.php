<?php
require_once('../../../common/lib.php');
require_once('../../../common/define.php');

if(isset($_POST['room'])){
    $id_room = (int)$_POST['room'];
    if(is_numeric($id_room)){
        
        $result_room = $db->query('SELECT * FROM pm_room WHERE id = '.$id_room.' AND checked = 1 AND lang = '.LANG_ID);
        if($result_room !== false && $db->last_row_count() > 0){
            
            $row = $result_room->fetch(PDO::FETCH_ASSOC);
            
            $room_title = $row['title'];
            $room_subtitle = $row['subtitle'];
            $room_descr = $row['descr'];
            $room_price = $row['price'];
            $room_stock = $row['stock'];
            $max_adults = $row['max_adults'];
            $max_children = $row['max_children'];
            $max_people = $row['max_people'];
            $min_people = $row['min_people'];
            $room_facilities = $row['facilities']; ?>
        
            <div id="room-<?php echo $id_room; ?>" class="white-popup-block">
                <div class="fluid-container">
                    <div class="row">
                        <div class="col-xs-12 mb20">
                            <div class="owl-carousel" data-items="1" data-autoplay="true" data-dots="true" data-nav="false" data-rtl="<?php echo (RTL_DIR) ? 'true' : 'false'; ?>">
                                <?php
                                $result_room_file = $db->query('SELECT * FROM pm_room_file WHERE id_item = '.$id_room.' AND checked = 1 AND lang = '.LANG_ID.' AND type = \'image\' AND file != \'\' ORDER BY rank');
                                if($result_room_file !== false){
                                    foreach($result_room_file as $i => $row){
                
                                        $file_id = $row['id'];
                                        $filename = $row['file'];
                                        $label = $row['label'];
                                        
                                        $realpath = SYSBASE.'medias/room/big/'.$file_id.'/'.$filename;
                                        $thumbpath = DOCBASE.'medias/room/big/'.$file_id.'/'.$filename;
                                        
                                        if(is_file($realpath)){ ?>
                                            <div><img alt="<?php echo $label; ?>" src="<?php echo $thumbpath; ?>" class="img-responsive" style="max-height:600px;"></div>
                                            <?php
                                        }
                                    }
                                } ?>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <h3 class="mb0"><?php echo $room_title; ?></h3>
                            <h4 class="mb0"><?php echo $room_subtitle; ?></h4>
                        </div>
                        <div class="col-sm-4 text-right">
                            <?php
                            $min_price = $room_price;
                            $result_room_rate = $db->query('SELECT MIN(price) as min_price FROM pm_rate WHERE id_room = '.$id_room);
                            if($result_room_rate !== false && $db->last_row_count() > 0){
                                $row = $result_room_rate->fetch();
                                $price = $row['min_price'];
                                if($price > 0) $min_price = $price;
                            } ?>
                            <div class="price text-primary">
                                <?php echo $texts['FROM_PRICE']; ?>
                                <span itemprop="priceRange">
                                    <?php echo formatPrice($min_price*CURRENCY_RATE); ?>
                                </span>
                                / <?php echo $texts['NIGHT']; ?>
                            </div>
                            <p>
                                <?php echo $texts['CAPACITY']; ?> : <i class="fa fa-male"></i>x<?php echo $max_people; ?>
                            </p>
                        </div>
                        <div class="col-xs-12">
                            <div class="clearfix mb5">
                                <?php
                                $id_facility = 0;
                                $result_facility_file = $db->prepare('SELECT * FROM pm_facility_file WHERE id_item = :id_facility AND checked = 1 AND lang = '.DEFAULT_LANG.' AND type = \'image\' AND file != \'\' ORDER BY rank LIMIT 1');
                                $result_facility_file->bindParam(':id_facility', $id_facility);
            
                                $result_room_facilities = $db->query('SELECT * FROM pm_facility WHERE lang = '.LANG_ID.' AND FIND_IN_SET(id, '.$room_facilities.') ORDER BY rank LIMIT 18');
                                if($result_room_facilities !== false && $db->last_row_count() > 0){
                                    foreach($result_room_facilities as $row){
                                        $id_facility = $row['id'];
                                        $facility_name = $row['name'];
                                        
                                        $result_facility_file->execute();
                                        if($result_facility_file !== false && $db->last_row_count() > 0){
                                            $row = $result_facility_file->fetch();
                                            
                                            $file_id = $row['id'];
                                            $filename = $row['file'];
                                            $label = $row['label'];
                                            
                                            $realpath = SYSBASE.'medias/facility/big/'.$file_id.'/'.$filename;
                                            $thumbpath = DOCBASE.'medias/facility/big/'.$file_id.'/'.$filename;
                                                
                                            if(is_file($realpath)){ ?>
                                                <span class="facility-icon">
                                                    <img alt="<?php echo $facility_name; ?>" title="<?php echo $facility_name; ?>" src="<?php echo $thumbpath; ?>" class="tips">
                                                </span>
                                                <?php
                                            }
                                        }
                                    }
                                } ?>
                            </div>
                            <?php echo $room_descr; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    }
} ?>
