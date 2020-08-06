<?php
/**
 * Script called (Ajax) on scroll or click
 * loads more content with Lazy Loader
 */
$html = '';
if(!isset($lz_offset)) $lz_offset = 1;
if(!isset($lz_limit)) $lz_limit = 30;
if(isset($_POST['ajax']) && $_POST['ajax'] == 1){
    
    require_once('../../../common/lib.php');
    require_once('../../../common/define.php');

    if(isset($_POST['offset']) && is_numeric($_POST['offset'])
    && isset($_POST['limit']) && is_numeric($_POST['limit'])){
        $lz_offset = $_POST['offset'];
        $lz_limit =	$_POST['limit'];
    }
    if(isset($_POST['destination']) && is_numeric($_POST['destination'])) $destination_id = $_POST['destination'];
}
if(isset($db) && $db !== false){
    
    $my_page_alias = $sys_pages['hotels']['alias'];

    $query_hotel = 'SELECT * FROM pm_hotel WHERE lang = '.LANG_ID.' AND checked = 1';
    if(isset($destination_id)) $query_hotel .= ' AND id_destination = '.$db->quote($destination_id);
    $query_hotel .= ' ORDER BY rank LIMIT '.($lz_offset-1)*$lz_limit.', '.$lz_limit;
    $result_hotel = $db->query($query_hotel);

    $id_hotel = 0;

    $result_hotel_file = $db->prepare('SELECT * FROM pm_hotel_file WHERE id_item = :id_hotel AND checked = 1 AND lang = '.LANG_ID.' AND type = \'image\' AND file != \'\' ORDER BY rank LIMIT 1');
    $result_hotel_file->bindParam(':id_hotel', $id_hotel);

    $result_rate = $db->prepare('SELECT MIN(price) as min_price FROM pm_rate WHERE id_hotel = :id_hotel');
    $result_rate->bindParam(':id_hotel', $id_hotel);

    foreach($result_hotel as $i => $row){
                                
        $id_hotel = $row['id'];
        $hotel_title = $row['title'];
        $hotel_subtitle = $row['subtitle'];
        $hotel_alias = $row['alias'];
        
        $hotel_alias = DOCBASE.$my_page_alias.'/'.text_format($hotel_alias);
        
        $html .= '
        <article class="col-sm-4 isotopeItem" itemscope itemtype="http://schema.org/LodgingBusiness">
            <div class="isotopeInner">
                <a itemprop="url" href="'.$hotel_alias.'">';
                    
                    if($result_hotel_file->execute() !== false && $db->last_row_count() > 0){
                        $row = $result_hotel_file->fetch(PDO::FETCH_ASSOC);
                        
                        $file_id = $row['id'];
                        $filename = $row['file'];
                        $label = $row['label'];
                        
                        $realpath = SYSBASE.'medias/hotel/medium/'.$file_id.'/'.$filename;
                        $thumbpath = DOCBASE.'medias/hotel/medium/'.$file_id.'/'.$filename;
                        $zoompath = DOCBASE.'medias/hotel/big/'.$file_id.'/'.$filename;
                        
                        if(is_file($realpath)){
                            $html .= '
                            <figure class="more-link img-container md">
                                <img alt="'.$label.'" src="'.$thumbpath.'">
                                <span class="more-action">
                                    <span class="more-icon"><i class="fa fa-link"></i></span>
                                </span>
                            </figure>';
                        }
                    }
                    $html .= '
                    <div class="isotopeContent">
                        <h3 itemprop="name">'.$hotel_title.'</h3>
                        <h4>'.$hotel_subtitle.'</h4>';
                        $min_price = 0;
                        if($result_rate->execute() !== false && $db->last_row_count() > 0){
                            $row = $result_rate->fetch();
                            $price = $row['min_price'];
                            if($price > 0) $min_price = $price;
                        }
                        $html .= '
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="price text-primary">
                                    '.$texts['FROM_PRICE'].'
                                    <span itemprop="priceRange">
                                        '.formatPrice($min_price*CURRENCY_RATE).'
                                    </span>
                                </div>
                                <div class="text-muted">'.$texts['PRICE'].' / '.$texts['NIGHT'].'</div>
                            </div>
                            <div class="col-xs-6">
                                <span class="btn btn-primary mt5 pull-right">'.$texts['MORE_DETAILS'].'</span>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </a>
            </div>
        </article>';
    }
    if(isset($_POST['ajax']) && $_POST['ajax'] == 1)
        echo json_encode(array('html' => $html));
    else
        echo $html;
}
