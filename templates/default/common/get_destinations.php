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
}
    
if(isset($db) && $db !== false){
    
    $my_page_alias = $sys_pages['booking']['alias'];

    $query_destination = 'SELECT * FROM pm_destination WHERE lang = '.LANG_ID.' AND checked = 1';
    $query_destination .= ' ORDER BY rank LIMIT '.($lz_offset-1)*$lz_limit.', '.$lz_limit;
    $result_destination = $db->query($query_destination);

    $destination_id = 0;

    $result_destination_file = $db->prepare('SELECT * FROM pm_destination_file WHERE id_item = :destination_id AND checked = 1 AND lang = '.LANG_ID.' AND type = \'image\' AND file != \'\' ORDER BY rank LIMIT 1');
    $result_destination_file->bindParam(':destination_id', $destination_id);
    
    $result_rate = $db->prepare('
        SELECT MIN(ra.price) as min_price
        FROM pm_rate as ra, pm_hotel as h, pm_destination as d
        WHERE id_hotel = h.id
            AND id_destination = d.id
            AND id_destination = :destination_id');
    $result_rate->bindParam(':destination_id', $destination_id);

    foreach($result_destination as $i => $row){
                                
        $destination_id = $row['id'];
        $destination_name = $row['name'];
        $destination_title = $row['title'];
        $destination_alias = $row['alias'];
        
        $destination_alias = DOCBASE.$my_page_alias.'/'.text_format($destination_alias);
        
        $html .= '
        <article class="col-sm-4 isotopeItem" itemscope itemtype="http://schema.org/Place">
            <div class="isotopeInner">
                <a itemprop="url" href="'.$destination_alias.'">';
                    
                    if($result_destination_file->execute() !== false && $db->last_row_count() > 0){
                        $row = $result_destination_file->fetch(PDO::FETCH_ASSOC);
                        
                        $file_id = $row['id'];
                        $filename = $row['file'];
                        $label = $row['label'];
                        
                        $realpath = SYSBASE.'medias/destination/medium/'.$file_id.'/'.$filename;
                        $thumbpath = DOCBASE.'medias/destination/medium/'.$file_id.'/'.$filename;
                        $zoompath = DOCBASE.'medias/destination/big/'.$file_id.'/'.$filename;
                        
                        if(is_file($realpath)){
                            $html .= '
                            <figure class="more-link">
                                <img alt="'.$label.'" src="'.$thumbpath.'" class="img-responsive">
                                <span class="more-action">
                                    <span class="more-icon"><i class="fa fa-link"></i></span>
                                </span>
                            </figure>';
                        }
                    }
                    $html .= '
                    <div class="isotopeContent">
                        <h3 itemprop="name">'.$destination_name.'</h3>
                        <h4>'.$destination_title.'</h4>';
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
