<?php
/**
 * Display the right model of the template according to the url
 */
require('common/lib.php');
require('common/define.php');

if(MAINTENANCE_MODE == 0 || (isset($_SESSION['user']) && ($_SESSION['user']['type'] == 'administrator' || $_SESSION['user']['type'] == 'manager'))){

    $uri = preg_split('#[\\\\/]#', REQUEST_URI);
    $err404 = false;
    $ishome = false;
    $page = null;
    $article = null;
    $page_id = 0;
    $article_id = 0;
    $page_alias = '';
    $article_alias = '';

    $count_uri = count($uri);

    if((LANG_ENABLED && $count_uri == 1) || (!LANG_ENABLED && $uri[0] == '')) $ishome = true;
    else{
        $i = (LANG_ENABLED) ? 1 : 0;
        $page_alias = trim(LANG_ALIAS.$uri[$i], '/\\');
        if($count_uri > $i+2) err404();
        if(isset($uri[$i+1])) $article_alias = $uri[$i+1];
    }
    
    foreach($articles as $id => $row){
        //current article
        if($article_alias != '' && $article_alias == substr($row['alias'], strrpos($row['alias'], '/')+1)){
            $article_id = $row['id'];
            $article = $row;
        }
    }

    $found = false;
    if(!empty($pages)){
        foreach($pages as $row){
            //current page
            if(($ishome && $row['home'] == 1) XOR ($row['alias'] != '' && $page_alias == $row['alias'])){
                $page_id = $row['id'];
                if($article_alias == '' && $row['currequest'] != REQUEST_URI) err404();
                else{
                    $page = $row;
                    $found = true;
                }
            }
        }
    }
    
    if($found === false) err404();

    $title_tag = $page['title_tag'];

    if($article_alias != '' && $page['article_model'] == '') err404();
    if($article_alias == '' && $page['page_model'] == '') err404();

    if($article_alias != '') $page_model = $page['article_model'];
    else $page_model = $page['page_model'];
                        
    $breadcrumbs = array();
    $id_parent = $page['id_parent'];
    while(isset($parents[$id_parent])){
        if($id_parent > 0 && $id_parent != $homepage['id']){
            $breadcrumbs[] = $id_parent;
            $id_parent = $pages[$id_parent]['id_parent'];
        }else break;
    }

    $breadcrumbs = array_reverse($breadcrumbs);

    $page_model = SYSBASE.'templates/'.TEMPLATE.'/models/'.str_replace('_','/',$page_model).'.php';
    
    if(is_file($page_model)) include($page_model);

    require(SYSBASE.'templates/'.TEMPLATE.'/common/footer.php');
}else{
    header('HTTP/1.1 503 Service Temporarily Unavailable');
    if(DOCBASE.REQUEST_URI != DOCBASE) header('Location: '.DOCBASE);
    require(SYSBASE.'templates/'.TEMPLATE.'/maintenance.php');
}

if(ob_get_level() > 0) ob_flush();
