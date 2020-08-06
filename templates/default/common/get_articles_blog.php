<?php
/**
 * Script called (Ajax) on scroll or click
 * loads more content with Lazy Loader
 */
$html = "";
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
    if(isset($_POST['month']) && is_numeric($_POST['month']) && isset($_POST['year']) && is_numeric($_POST['year'])){
        $start_month = mktime(0, 0, 0, $_POST['month'], 1, $_POST['year']);
        $end_month = mktime(0, 0, 0, $_POST['month'], date('t', $start_month), $_POST['year']);
    }else{
        $start_month = null;
        $end_month = null;
    }
    $tag = (isset($_POST['tag']) && is_numeric($_POST['tag'])) ? $_POST['tag'] : 0;
}
if(isset($db) && $db !== false){
    
    if(isset($page_id) && isset($pages[$page_id]['alias'])) $page_alias = $pages[$page_id]['alias'];

    $query_article = 'SELECT * FROM pm_article WHERE id_page = '.$page_id.' AND checked = 1';
    
    if(!is_null($start_month) && !is_null($end_month)){
        $query_article .= '
        AND CASE WHEN publish_date IS NOT NULL THEN publish_date ELSE add_date END <= '.$end_month.'
        AND CASE WHEN publish_date IS NOT NULL THEN publish_date ELSE add_date END >= '.$start_month;
    }
    
    if($tag > 0) $query_article .= ' AND tags REGEXP \'(^|,)'.$tag.'(,|$)\'';
    
    $query_article .= '
		AND (publish_date IS NULL || publish_date <= '.time().')
		AND (unpublish_date IS NULL || unpublish_date > '.time().')
		AND lang = '.LANG_ID.' 
		AND (show_langs IS NULL || show_langs = \'\' || show_langs REGEXP \'(^|,)'.LANG_ID.'(,|$)\')
		AND (hide_langs IS NULL || hide_langs = \'\' || hide_langs NOT REGEXP \'(^|,)'.LANG_ID.'(,|$)\')
    ORDER BY CASE WHEN publish_date IS NOT NULL THEN publish_date ELSE add_date END DESC LIMIT '.($lz_offset-1)*$lz_limit.', '.$lz_limit;
    
    $result_article = $db->query($query_article);
    
    $article_users = '';
    $result_users = $db->prepare('SELECT * FROM pm_user WHERE FIND_IN_SET(id, :users)');
    $result_users->bindParam(':users', $article_users);
    
    $id_article = 0;
    $result_article_file = $db->prepare('SELECT * FROM pm_article_file WHERE id_item = :article_id AND checked = 1 AND lang = '.DEFAULT_LANG.' AND type = \'image\' AND file != \'\' ORDER BY rank LIMIT 1');
    $result_article_file->bindParam(':article_id', $id_article);
    
    $result_comment = $db->prepare('SELECT * FROM pm_comment WHERE id_item = :article_id AND item_type = \'article\' AND checked = 1 ORDER BY add_date DESC');
    $result_comment->bindParam(':article_id', $id_article);

    foreach($result_article as $i => $row){
                                
        $id_article = $row['id'];
        $article_title = $row['title'];
        $article_alias = $row['alias'];
        $article_text = strtrunc(strip_tags($row['text']),170);
        $article_tags = $row['tags'];
        $add_date = $row['add_date'];
        $article_users = $row['users'];
        $allow_comment = $row['comment'];
        $publish_date = $row['publish_date'];
        $edit_date = $row['edit_date'];
        
        if(is_null($publish_date)) $publish_date = $row['add_date'];
        if(is_null($edit_date)) $edit_date = $publish_date;
        
        $tags = '';
        if($article_tags != '') $tags = ' tag'.str_replace(',', ' tag', $article_tags);
        
        $article_alias = DOCBASE.$page_alias.'/'.text_format($article_alias);
        
        $nb_comments = 0;
        if($allow_comment == 1 && $result_comment->execute() !== false)
            $nb_comments = $db->last_row_count();
        
        $html .= '
        <article class="blog-article boxed col-sm-12 mb20".$tags."" itemprop="blogPosts" itemscope itemtype="http://schema.org/BlogPosting">
            <link itemprop="mainEntityOfPage" href="'.getUrl(true).$article_alias.'">';
            if($result_article_file->execute() !== false && $db->last_row_count() > 0){
                $row = $result_article_file->fetch(PDO::FETCH_ASSOC);
                
                $file_id = $row['id'];
                $filename = $row['file'];
                $label = $row['label'];
                
                $realpath = SYSBASE.'medias/article/big/'.$file_id.'/'.$filename;
                $thumbpath = DOCBASE.'medias/article/big/'.$file_id.'/'.$filename;
                $zoompath = DOCBASE.'medias/article/big/'.$file_id.'/'.$filename;
                
                if(is_file($realpath)){
                    $size = getimagesize($realpath);
                    $w = $size[0];
                    $h = $size[1];
                    $html .= '
                    <a itemprop="url" href="'.$article_alias.'">
                        <figure class="more-link" itemprop="image" itemscope itemtype="http://schema.org/ImageObject">
                            <img alt="'.$label.'" src="'.$thumbpath.'" class="img-responsive" itemprop="url" width="'.$w.'" height="'.$h.'">
                            <meta itemprop="height" content="'.$h.'">
                            <meta itemprop="width" content="'.$w.'">
                            <span class="more-action">
                                <span class="more-icon"><i class="fa fa-link"></i></span>
                            </span>
                        </figure>
                    </a>';
                }
            }
            $html .= '
            <a href="'.$article_alias.'"><h3 itemprop="name headline" class="mt10">'.$article_title.'</h3></a>';
            if($article_text != '') $html .= '<p>'.$article_text.'</p>';
            $html .= '
            <div>
                <a itemprop="url" href="'.$article_alias.'" class="btn btn-primary">'.$texts['READMORE'].'</a>
            </div>
            <div class="mt10 labels" dir="ltr">
                <span class="label label-default mb5"><i class="fa fa-thumbtack"></i> 
                    <time itemprop="dateCreated datePublished dateModified" datetime="'.date('c', $publish_date).'">';
                    $html .= (!RTL_DIR) ? strftime(DATE_FORMAT, $publish_date) : strftime('%F', $publish_date);
                    $html .= '</time>
                </span>';
                
                $html .= '<span class="label label-default mb5"><i class="fa fa-comment"></i> '.$nb_comments.' '.mb_strtolower($texts['COMMENTS'], 'UTF-8').'</span>';
                
                if($result_users->execute() != false){
                    foreach($result_users as $user_article)
                        $html .= '<span class="label label-default mb5"><i class="fa fa-user"></i> <span itemprop="creator author publisher">'.$user_article['login'].'</span></span>';
                }
                $result_tag = $db->query('SELECT * FROM pm_tag WHERE id IN ('.$article_tags.') AND checked = 1 AND lang = '.LANG_ID.' ORDER BY rank');
                if($result_tag !== false){
                    $nb_tags = $db->last_row_count();
                    
                    if($nb_tags > 0){
                        foreach($result_tag as $i => $row){
                            $tag_id = $row['id'];
                            $tag_value = $row['value'];

                            $html .= '<span class="label label-default mb5"><i class="fa fa-tag"></i> '.$tag_value.'</span>';
                        }
                    }
                }
                $html .= '
            </div>
        </article>';
    }
    if(isset($_POST['ajax']) && $_POST['ajax'] == 1)
        echo json_encode(array('html' => $html));
    else
        echo $html;
}
