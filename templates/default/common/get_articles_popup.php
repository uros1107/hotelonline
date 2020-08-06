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
    && isset($_POST['limit']) && is_numeric($_POST['limit'])
    && isset($_POST['page_id']) && is_numeric($_POST['page_id'])
    && isset($_POST['page_alias'])){
        $page_id = $_POST['page_id'];
        $lz_offset = $_POST['offset'];
        $lz_limit =	$_POST['limit'];
        $page_alias = $_POST['page_alias'];
    }
}
if(isset($db) && $db !== false){
    
    if(isset($page_id) && isset($pages[$page_id]['alias'])) $page_alias = $pages[$page_id]['alias'];

    $result_article = $db->query('SELECT *
								FROM pm_article
								WHERE id_page = '.$page_id.'
									AND checked = 1
									AND (publish_date IS NULL || publish_date <= '.time().')
									AND (unpublish_date IS NULL || unpublish_date > '.time().')
									AND lang = '.LANG_ID.'
									AND (show_langs IS NULL || show_langs = \'\' || show_langs REGEXP \'(^|,)'.LANG_ID.'(,|$)\')
									AND (hide_langs IS NULL || hide_langs = \'\' || hide_langs NOT REGEXP \'(^|,)'.LANG_ID.'(,|$)\')
								ORDER BY rank
								LIMIT '.($lz_offset-1)*$lz_limit.', '.$lz_limit);
	$article_id = 0;
    $result_article_file = $db->prepare('SELECT * FROM pm_article_file WHERE id_item = :article_id AND checked = 1 AND lang = '.DEFAULT_LANG.' AND type = \'image\' AND file != \'\' ORDER BY rank LIMIT 1');
    $result_article_file->bindParam(':article_id', $article_id);
    
    $result_article_all_files = $db->prepare('SELECT * FROM pm_article_file WHERE id_item = :article_id AND checked = 1 AND lang = '.DEFAULT_LANG.' AND type = \'image\' AND file != \'\' ORDER BY rank');
    $result_article_all_files->bindParam(':article_id', $article_id);

    foreach($result_article as $i => $row){
                                
        $article_id = $row['id'];
        $article_title = $row['title'];
        $article_alias = $row['alias'];
        $article_text = $row['text'];
        $article_tags = $row['tags'];
        $article_comment = $row['comment'];
        $article_rating = $row['rating'];
        $article_add_date = $row['add_date'];
        
        if($article_tags != '') $article_tags = ' tag'.str_replace(',',' tag',$article_tags);
        
        $article_alias = DOCBASE.$page_alias.'/'.text_format($article_alias);
        
        $html .= '
        <article class="col-sm-4 isotopeItem'.$article_tags.'" itemscope itemtype="http://schema.org/Article">
            <div class="isotopeInner">
                <a itemprop="url" class="popup-modal" href="#article-popup-'.$article_id.'">';
                    
                    if($result_article_file->execute() !== false && $db->last_row_count() == 1){
                        $row = $result_article_file->fetch(PDO::FETCH_ASSOC);
                        
                        $file_id = $row['id'];
                        $filename = $row['file'];
                        $label = $row['label'];
                        
                        $realpath = SYSBASE.'medias/article/medium/'.$file_id.'/'.$filename;
                        $thumbpath = DOCBASE.'medias/article/medium/'.$file_id.'/'.$filename;
                        $zoompath = DOCBASE.'medias/article/big/'.$file_id.'/'.$filename;
                        
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
                        <div class="text-overflow">
                        <h3 itemprop="name">'.$article_title.'</h3>';
                        if($article_text != '') $html .= '<p>'.strtrunc(strip_tags($article_text),170).'</p>';
                        $html .= '
                        <div class="more-btn">
                            <span class="btn btn-primary">'.$texts['READMORE'].'</span>
                        </div>
                    </div>
                </a>
            </div>
        </article>
        <div id="article-popup-'.$article_id.'" class="white-popup-block mfp-hide">
            <div class="fluid-container">
                <div class="row">
                    <div class="col-xs-12 mb20">
                        <div class="owl-carousel" data-items="1" data-autoplay="true" data-dots="true" data-nav="false" data-rtl="'.((RTL_DIR) ? 'true' : 'false').'">';
                            
                            if($result_article_all_files->execute() !== false){
                                
                                foreach($result_article_all_files as $i => $row){
                                
                                    $file_id = $row['id'];
                                    $filename = $row['file'];
                                    $label = $row['label'];
                                    
                                    $realpath = SYSBASE.'medias/article/big/'.$file_id.'/'.$filename;
                                    $thumbpath = DOCBASE.'medias/article/big/'.$file_id.'/'.$filename;
                                    
                                    if(is_file($realpath)){
                                        $html .= '<img alt="'.$label.'" src="'.$thumbpath.'" class="img-responsive" style="max-height:600px;">';
                                    }
                                }
                            }
                        $html .= '
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <h1>'.$article_title.'</h1>';
                        
                        $nb_comments = 0;
                        $item_type = 'article';
                        $item_id = $article_id;
                        $allow_comment = $article_comment;
                        $allow_rating = $article_rating;
                        if($allow_comment == 1){
                            $result_comment = $db->query('SELECT * FROM pm_comment WHERE id_item = '.$item_id.' AND item_type = ''.$item_type.'' AND checked = 1 ORDER BY add_date DESC');
                            if($result_comment !== false)
                                $nb_comments = $db->last_row_count();
                        }
                        $html .= '<div class="mb10 mt10 labels" dir="ltr">
                            <span class="label label-default"><i class="fa fa-thumb-tack"></i> '.((!RTL_DIR) ? strftime(DATE_FORMAT, $article_add_date) : strftime('%F', $article_add_date)).'</span>
                            <span class="label label-default"><i class="fa fa-comment"></i>  '.$nb_comments.' '.mb_strtolower($texts['COMMENTS'], 'UTF-8').'</span>';
                            
                            $result_tag = $db->query('SELECT * FROM pm_tag WHERE id IN ('.$article_tags.') AND checked = 1 AND lang = '.LANG_ID.' ORDER BY rank');
                            if($result_tag !== false){
                                $nb_tags = $db->last_row_count();
                                
                                if($nb_tags > 0){
                                    $html .= '
                                    <span class="label label-default"><i class="fa fa-tags"></i>';
                                        
                                        foreach($result_tag as $i => $row){
                                            $tag_id = $row['id'];
                                            $tag_value = $row['value'];

                                            echo $tag_value;
                                            if($i+1 < $nb_tags) echo ', ';
                                        }
                                    $html .= '
                                    </span>';
                                }
                            }
                        $html .= '
                        </div>
                        '.$article_text;
                        
                        $short_text = strtrunc(strip_tags($article_text), 100);
                        $site_url = getUrl();
                       
                        $html .= '
                        <div id="twitter" data-url="'.$site_url.'" data-text="'.$short_text.'" data-title="Tweet"></div>
                        <div id="facebook" data-url="'.$site_url.'" data-text="'.$short_text.'" data-title="Like"></div>
                        <div id="googleplus" data-url="'.$site_url.'" data-curl="'.DOCBASE.'js/plugins/sharrre/sharrre.php" data-text="'.$short_text.'" data-title="+1"></div>
                    </div>
                </div>
            </div>
        </div>';
    }
    if(isset($_POST['ajax']) && $_POST['ajax'] == 1)
        echo json_encode(array('html' => $html));
    else
        echo $html;
}
