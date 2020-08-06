<?php
/**
 * Common file for Pandao CMS
 * gets the configuration values and defines the environment
 */
if(!is_session_started()) session_start();

if(!defined('ADMIN')) define('ADMIN', false);

require_once('setenv.php');

$default_lang = 2;
$default_lang_tag = 'en';
$lang_alias = '';
$locale = 'en_GB';
$default_currency_code = 'USD';
$default_currency_sign = '$';
$default_currency_rate = 1;
$rtl_dir = false;
$db = false;

if(is_file(SYSBASE.'common/config.php')){
    require_once(SYSBASE.'common/config.php');
    
    $admin_lang_file = SYSBASE.ADMIN_FOLDER.'/includes/langs/'.ADMIN_LANG_FILE;
    
    if(ADMIN && is_file($admin_lang_file)){
        $texts = @parse_ini_file($admin_lang_file);
        if(is_null($texts))
            $texts = @parse_ini_string(file_get_contents($admin_lang_file));
    }
    
    try{
        $db = new db('mysql:host='.DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
        $db->exec('SET NAMES \'utf8\'');
    }catch(PDOException $e){
        if(ADMIN) $_SESSION['msg_error'][] = $texts['DATABASE_ERROR'];
        else die('Unable to connect to the database. Please contact the webmaster or retry later.');
    }
}

if(!defined('ADMIN_FOLDER')) define('ADMIN_FOLDER', 'admin');

if(($db !== false && db_table_exists($db, 'pm_%') === false) || !is_file(SYSBASE.'common/config.php')){
    header('Location: '.DOCBASE.ADMIN_FOLDER.'/setup.php');
    exit();
}else{
    if($db === false) die('Unable to connect to the database. Please contact the webmaster or retry later.');
}

if(!ADMIN){
    $request_uri = (DOCBASE != '/') ? substr($_SERVER['REQUEST_URI'], strlen(DOCBASE)) : $_SERVER['REQUEST_URI'];
    $request_uri = trim($request_uri, '/');
    $pos = strpos($request_uri, '?');
    if($pos !== false) $request_uri = substr($request_uri, 0, $pos);
    
    define('REQUEST_URI', $request_uri);
}
    
if(isset($_SESSION['user']['id'])){
    $result_user = $db->query('SELECT * FROM pm_user WHERE id = '.$db->quote($_SESSION['user']['id']).' AND checked = 1');
    if($result_user !== false && $db->last_row_count() > 0){
        $row = $result_user->fetch();
        $_SESSION['user']['id'] = $row['id'];
        $_SESSION['user']['login'] = $row['login'];
        $_SESSION['user']['email'] = $row['email'];
        $_SESSION['user']['type'] = $row['type'];
    }else
        unset($_SESSION['user']);
}

$result_currency = $db->query('SELECT * FROM pm_currency');
if($result_currency !== false){
    foreach($result_currency as $i => $row){
        $currency_code = $row['code'];
        $currency_sign = $row['sign'];
        if($row['main'] == 1){
            $default_currency_code = $currency_code;
            $default_currency_sign = $currency_sign;
        }
        $currencies[$currency_code] = $row;
    }
}
    
$result_lang = $db->query('SELECT l.id AS lang_id, lf.id AS file_id, title, tag, file, locale, rtl, main FROM pm_lang as l, pm_lang_file as lf WHERE id_item = l.id AND l.checked = 1 AND file != \'\' ORDER BY l.rank');
if($result_lang !== false){
    foreach($result_lang as $i => $row){
        $lang_tag = $row['tag'];
        if($row['main'] == 1){
            $default_lang = $row['lang_id'];
            $default_lang_tag = $lang_tag;
            $rtl_dir = $row['rtl'];
            $locale = $row['locale'];
        }
        $row['file'] = DOCBASE.'medias/lang/big/'.$row['file_id'].'/'.$row['file'];
        $langs[$lang_tag] = $row;
    }
}
$id_lang = $default_lang;
$lang_tag = $default_lang_tag;

if(!ADMIN && (MAINTENANCE_MODE == 0  || (isset($_SESSION['user']) && ($_SESSION['user']['type'] == 'administrator' || $_SESSION['user']['type'] == 'manager')))){
    if(LANG_ENABLED == 1){
        
        $uri = explode('/', REQUEST_URI);
        $lang_tag = $uri[0];
        
        if(!isset($langs[$lang_tag])){
            
            if(preg_match('/$(index.php)?^/', str_replace(DOCBASE, '', $_SERVER['REQUEST_URI']))){
                
                if($lang_tag == ''){
                    if(isset($_COOKIE['LANG_TAG']) && isset($langs[$_COOKIE['LANG_TAG']])){
                        header('HTTP/1.0 404 Not Found');
                        header('Location: '.DOCBASE.$_COOKIE['LANG_TAG']);
                        exit();
                    }else{
                        header('HTTP/1.0 404 Not Found');
                        header('Location: '.DOCBASE.$default_lang_tag);
                        exit();
                    }
                }else err404(DOCBASE.'404.html');
                
            }elseif(isset($_SESSION['LANG_TAG']))
                $lang_tag = $_SESSION['LANG_TAG'];
            else
                $lang_tag = $default_lang_tag;
        }else{
            setcookie('LANG_TAG', $lang_tag, time()+25200);
            
            $_SESSION['LANG_TAG'] = $lang_tag;
        }
        if(isset($langs[$lang_tag])){
            $id_lang = $langs[$lang_tag]['lang_id'];
            $locale = $langs[$lang_tag]['locale'];
            $rtl_dir = $langs[$lang_tag]['rtl'];
        }
        $sublocale = substr($locale, 0, 2);
        if($sublocale == 'tr' || $sublocale == 'az') $locale = 'en_GB';
        $lang_alias = $lang_tag.'/';
    }
    
    $texts = array();
    $result_text = $db->query('SELECT * FROM pm_text WHERE lang = '.$id_lang.' GROUP BY id');
    foreach($result_text as $row)
        $texts[$row['name']] = $row['value'];
            
    $widgets = array();
    $result_widget = $db->query('SELECT * FROM pm_widget WHERE checked = 1 AND lang = '.$id_lang.' GROUP BY id ORDER BY rank');
    foreach($result_widget as $row)
        $widgets[$row['pos']][] = $row;
        
    $pages = array();
    $sys_pages = array();
    $parents = array();
    $result_page = $db->query('SELECT *
							FROM pm_page
							WHERE (checked = 1 OR checked = 0)
								AND lang = '.$id_lang.'
								AND (show_langs IS NULL || show_langs = \'\' || show_langs REGEXP \'(^|,)'.$id_lang.'(,|$)\')
								AND (hide_langs IS NULL || hide_langs = \'\' || hide_langs NOT REGEXP \'(^|,)'.$id_lang.'(,|$)\')
							ORDER BY rank');
    if($result_page !== false){
        foreach($result_page as $i => $row){

            $alias = $row['alias'];
            
            if($row['home'] != 1){
                $alias = text_format($alias);
                $currequest = $alias;
            }else{
                $alias = "";
                $currequest = "";
            }
            
            $alias = trim($lang_alias.$alias, "/\\");
            $currequest = trim($lang_alias.$currequest, "/\\");
            
            $row['alias'] = $alias;
            $row['currequest'] = $currequest;
            if($row['system'] == 1) $sys_pages[$row['page_model']] = $row;
            
            if($row['home'] == 1) $homepage = $row;
            
            $row['articles'] = array();
            
            $pages[$row['id']] = $row;
            $parents[$row['id_parent']][] = $row['id'];
        }
    }
    
    define('URL_404', DOCBASE.$sys_pages['404']['alias']);
    
    $articles = array();
    $result_article = $db->query('SELECT *
								FROM pm_article
								WHERE id_page IN('.implode(',', array_keys($pages)).')
									AND (checked = 1 OR checked = 3)
									AND (publish_date IS NULL || publish_date <= '.time().')
									AND (unpublish_date IS NULL || unpublish_date > '.time().')
									AND lang = '.$id_lang.'
									AND (show_langs IS NULL || show_langs = \'\' || show_langs REGEXP \'(^|,)'.$id_lang.'(,|$)\')
									AND (hide_langs IS NULL || hide_langs = \'\' || hide_langs NOT REGEXP \'(^|,)'.$id_lang.'(,|$)\')
								ORDER BY CASE WHEN publish_date IS NOT NULL THEN publish_date ELSE add_date END DESC');
    if($result_article !== false){
        foreach($result_article as $i => $row){
            
            $alias = $row['alias'];
            
            $full_alias = $pages[$row['id_page']]['alias'].'/'.text_format($alias);
            $row['alias'] = $full_alias;
            $articles[$row['id']] = $row;
            
            $pages[$row['id_page']]['articles'][$row['id']] = $row;
        }
    }
    
    $menus['main'] = array();
    $menus['footer'] = array();
    $result_menu = $db->query('SELECT * FROM pm_menu WHERE checked = 1 AND lang = '.$id_lang.' ORDER BY rank');
    if($result_menu !== false){
        foreach($result_menu as $row){
            
            if(($row['item_type'] == 'page' && isset($pages[$row['id_item']]) && $pages[$row['id_item']]['checked'] == 1)
            || ($row['item_type'] == 'article' && isset($articles[$row['id_item']]))
            || $row['item_type'] == 'url'
            || $row['item_type'] == 'none'){
                
                $href = get_nav_url($row);
                $row['href'] = $href;
                
                $target = (strpos($href, 'http') !== false) ? '_blank' : '_self';
                if(strpos($href, getUrl(true)) !== false) $target = '_self';
                $row['target'] = $target;
            
                if($row['main'] == 1) $menus['main'][$row['id']] = $row;
                if($row['footer'] == 1) $menus['footer'][$row['id']] = $row;
            }
        }
    }
}

$currency_code = (isset($_SESSION['currency']['code'])) ? $_SESSION['currency']['code'] : $default_currency_code;
$currency_sign = (isset($_SESSION['currency']['sign'])) ? $_SESSION['currency']['sign'] : $default_currency_sign;
$currency_rate = (isset($_SESSION['currency']['rate'])) ? $_SESSION['currency']['rate'] : $default_currency_rate;

date_default_timezone_set(TIME_ZONE);

if(setlocale(LC_ALL, $locale.'.UTF-8', $locale) === false){
    $locale = 'en_GB';
    setlocale(LC_ALL, $locale.'.UTF-8', $locale);
}

define('DEFAULT_CURRENCY_CODE', $default_currency_code);
define('DEFAULT_CURRENCY_SIGN', $default_currency_sign);
define('CURRENCY_CODE', $currency_code);
define('CURRENCY_SIGN', $currency_sign);
define('CURRENCY_RATE', $currency_rate);
define('DEFAULT_LANG', $default_lang);
define('LOCALE', $locale);
define('LANG_ID', $id_lang);
define('LANG_TAG', $lang_tag);
define('LANG_ALIAS', $lang_alias);
define('RTL_DIR', $rtl_dir);

$allowable_file_exts = array(
    'pdf' => 'pdf.png',
    'doc' => 'doc.png',
    'docx' => 'doc.png',
    'odt' => 'doc.png',
    'xls' => 'xls.png',
    'xlsx' => 'xls.png',
    'ods' => 'xls.png',
    'ppt' => 'ppt.png',
    'pptx' => 'ppt.png',
    'odp' => 'ppt.png',
    'txt' => 'txt.png',
    'csv' => 'txt.png',
    'jpg' => 'img.png',
    'jpeg' => 'img.png',
    'png' => 'img.png',
    'gif' => 'img.png',
    'swf' => 'swf.png'
);
