<?php
require_once(SYSBASE."includes/pdftotext/pdfToText.php");

$msg_error = "";
$msg_success = "";
$field_notice = array();

function truepath($path)
{
    $unipath = strlen($path) == 0 || $path{0} != "/";
    if(strpos($path,":")===false && $unipath)
        $path = getcwd()."/".$path;
    $path = str_replace(array("/", "\\"), "/", $path);
    $parts = array_filter(explode("/", $path), "strlen");
    $absolutes = array();
    foreach($parts as $part){
        if($part == "..")
            array_pop($absolutes);
        elseif($part != ".")
            $absolutes[] = $part;
    }
    $path = implode("/", $absolutes);
    $path =! $unipath ? "/".$path : $path;
    return $path;
}

function curl_exec_follow($ch, &$maxredirect = null)
{
    $user_agent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0";
    curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);

    $mr = $maxredirect === null ? 5 : intval($maxredirect);

    $open_basedir = ini_get("open_basedir");
    $safe_mode = ini_get("safe_mode");

    if(empty($open_basedir) && filter_var($safe_mode, FILTER_VALIDATE_BOOLEAN) === false){

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $mr > 0);
        curl_setopt($ch, CURLOPT_MAXREDIRS, $mr);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    }else{
        
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

        if($mr > 0){
            $original_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            $newurl = $original_url;
            
            $rch = curl_copy_handle($ch);
            
            curl_setopt($rch, CURLOPT_HEADER, true);
            curl_setopt($rch, CURLOPT_NOBODY, true);
            curl_setopt($rch, CURLOPT_FORBID_REUSE, false);
            
            do{
                curl_setopt($rch, CURLOPT_URL, $newurl);
                $header = curl_exec($rch);
                if(curl_errno($rch))
                    $code = 0;
                else{
                    $code = curl_getinfo($rch, CURLINFO_HTTP_CODE);
                    if($code == 301 || $code == 302){
                        preg_match("/Location:(.*?)\n/i", $header, $matches);
                        $newurl = trim(array_pop($matches));
                        
                        if(!preg_match("/^https?:/i", $newurl)) $newurl = $original_url . $newurl; 
                    }else
                        $code = 0;
                }
            }while($code && --$mr);
            
            curl_close($rch);
            
            if(!$mr){
                if($maxredirect === null)
                    ;//trigger_error("Too many redirects...", E_USER_WARNING);
                else
                    $maxredirect = 0;
                
                return false;
            }
            curl_setopt($ch, CURLOPT_URL, $newurl);
        }
    }
    return curl_exec($ch);
}

$mypdf = new PdfToText();

function getSearchResults($records, $q)
{
    global $mypdf;
    $results = array();
    $result_title = "";
    $result_descr = "";

    foreach($records['indexes'] as $url){
        $found = false;
        
        $isPdf = (substr($url, -4) === ".pdf");
    
        if(!$isPdf){
            
            if(isset($records['contents'][$url]) && $records['contents'][$url] != ""){
                $html = $records['contents'][$url];
                //$tags = @get_meta_tags($url);
                //if(isset($tags['description'])) $result_descr = $tags['description'];
               // preg_match("/<meta\s[^>]*name\s*=\s*[\"']description[\"'][^>]*>/siU", $html, $matches);
            
                preg_match("/<title>([^>]*)<\/title>/si", $html, $match);
                if(!empty($match)){
                    $title_origin = $match[1];
                    $title = wrapSentence($title_origin, $q, 20, 2);
                    if($title !== false && $title !== ""){
                        $result_title = $title;
                        $found = true;
                    }else $result_title = strtrunc($title_origin, 80, false);
                }
                preg_match("/<body.*\/body>/si", $html, $match);
                if(isset($match[0])){
                    $html = $match[0];
                    $descr_origin = strip_tags($html);
                    $descr = wrapSentence($descr_origin, $q, 6, 3);
                    if($descr !== false && $descr !== ""){
                        $result_descr = $descr;
                        $found = true;
                    }else $result_descr = strtrunc($descr_origin, 180, false);
                }
            }
        }else{
            $descr_origin = $mypdf->parseFile($url);
            $descr = wrapSentence($descr_origin, $q, 6, 3);
            if($descr !== false && $descr !== ""){
                $result_descr = $descr;
                $found = true;
            }else $result_descr = strtrunc($descr_origin, 180, false);
            
            $result_title = substr($url, strrpos($url, "/")+1);
        }
        if($found) $results[] = array("url" => $url, "title" => $result_title, "descr" => $result_descr);
    }
    return $results;
}
    
function crawl($myurl, $records = array("indexes" => array(), "matches" => array()))
{
    $isPdf = (substr($myurl, -4) === ".pdf");
    $tags = @get_meta_tags($myurl);
    $index = true;
    $follow = true;
    if(count($records['indexes']) < 600 &&
      ($isPdf || (!$isPdf && !isset($tags['robots']) || preg_match("/noindex/i", $tags['robots']) != 1))){

        if(!$isPdf){
            $ch = curl_init($myurl);
            if($ch !== false){
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $html = curl_exec_follow($ch);
                $effective_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
                curl_close($ch);
                // if redirect url exists
                if($effective_url != "" && $effective_url != $myurl){
                    $myurl = $effective_url;
                    $tags = @get_meta_tags($myurl);
                    if(isset($tags['robots']) && preg_match("/noindex/i", $tags['robots']) == 1){
                        $index = false;
                        $follow = false;
                    }
                }
            }
        }else{
            $index = true;
            $follow = false;
        }
            
        if(LANG_ENABLED == 1){
            
            $request_uri = parse_url($myurl, PHP_URL_PATH);
            $request_uri = (DOCBASE != "/") ? substr($request_uri, strlen(DOCBASE)) : $request_uri;
            $request_uri = trim($request_uri, "/");
            $pos = strpos($request_uri, "?");
            if($pos !== false) $request_uri = substr($request_uri, 0, $pos);
            $uri = explode("/", $request_uri);
            $lang_tag = $uri[0];
            
            if(!is_dir(SYSBASE.$lang_tag) && $lang_tag != LANG_TAG){
                $index = false;
                $follow = false;
            }
        }
        
        if($index){
            // no index if allready indexed
            if(!in_array($myurl, $records['indexes']) && !in_array(trim($myurl, "/"), $records['indexes'])){
                array_push($records['indexes'], $myurl);
            
                if($follow && $html !== false){
                    $html = preg_replace("/<script\b[^>]*>(.*?)<\/script>/is", "", $html);
                    $html = preg_replace("/<!--(?!<!)[\s\S]*?-->/si", "", $html);
                    $records['contents'][$myurl] = $html;
                    preg_match("/<body.*\/body>/si", $html, $match);
                    preg_match_all("/<a\s[^>]*href\s*=\s*[\"']([^>\"'#@;\(\)]*?)[\"'][^>]*>(.*)<\/a>/siU", $html, $matches);

                    if(!empty($matches[1])){

                        $matches = array_diff(array_unique(array_filter($matches[1])), $records['indexes'], $records['matches']);
                        
                        if(!empty($matches)){
                            $current_host = parse_url($myurl, PHP_URL_HOST);
                            $current_scheme = parse_url($myurl, PHP_URL_SCHEME);
                            
                            $to_crawl = array();
                            foreach($matches as $j => $match){
                                $url = $match;
                                if(($url != "" && !preg_match("/\.(jpe?g|png|bmp|tiff)/i", substr($url, -4)))
                                && (strpos($url, "http") === false || (strpos($url, "http") === 0 && parse_url($url, PHP_URL_HOST) == $current_host))){
                                  
                                    if(strpos($url, "#") !== false) $url = substr($url, 0, strrpos($url, "#"));
                                    if(strpos($url, "http") === false){
                                        $host = $current_scheme."://".$current_host;
  
                                        if(substr($url, 0, 1) != "/"){
                                            $curr_path = str_replace($host, "", $myurl);
                                            $curr_path = substr($curr_path, 0, strrpos($curr_path, "/"));
                                            $url = truepath($curr_path."/".$url);
                                        }
                                        $url = $host.$url;
                                    }
                                    $to_crawl[] = $url;
                                }
                            }
                            if(!empty($to_crawl)){

                                $records['matches'] = array_unique(array_merge($records['matches'], $matches, $to_crawl, $records['indexes']));
                                
                                $to_crawl = array_diff(array_unique($to_crawl), $records['indexes']);
                                
                                foreach($to_crawl as $url){
                                    if(!in_array($url, $records['indexes']) && !in_array(trim($url, "/"), $records['indexes']))
                                        $records = crawl($url, $records);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    return $records;
}

$referer = (checkReferer()) ? $_SERVER['HTTP_REFERER'] : "";
$search_limit = 10;
$max_search = 30;
$results = array();
$search_offset = (isset($_GET['search_offset']) && is_numeric($_GET['search_offset'])) ? $_GET['search_offset'] : 0;
if(!isset($_SESSION['search_count'])) $_SESSION['search_count'] = 0;
if(!isset($_SESSION['search_time'])) $_SESSION['search_time'] = time();
if(!isset($_SESSION['search_results'])) $_SESSION['search_results'] = array();
if(!isset($_SESSION['q_search'])) $_SESSION['q_search'] = "";

$start_time = microtime(true);

if(isset($_POST['global-search']) && check_token($referer, "search", "post")){
    
    $_SESSION['search_count']++;
    if($_SESSION['search_count'] <= ((((time()-$_SESSION['search_time'])/60)+1)*$max_search)){
        $_SESSION['q_search'] = $_POST['global-search'];
        $records = crawl(getUrl(true).DOCBASE.LANG_ALIAS);
        $results = getSearchResults($records, $_SESSION['q_search']);
            
        if(!empty($results))
            $_SESSION['search_results'] = $results;
        else{
            unset($_SESSION['search_results']);
            $msg_error .= $texts['NO_SEARCH_RESULT']."<br>";
        }
    }else{
        unset($_SESSION['search_results']);
        $msg_error .= $texts['SEARCH_EXCEEDED']."<br>";
    }
}
require(getFromTemplate("common/header.php", false)); ?>

<section id="page">
    
    <?php include(getFromTemplate("common/page_header.php", false)); ?>
    
    <div id="content" class="pt30 pb30">
        
        <div class="container" itemprop="text">
            
            <?php
            if($page['text'] != ""){ ?>
                <div class="clearfix mb20"><?php echo $page['text']; ?></div>
                <?php
            } ?>

            <div class="alert alert-success" style="display:none;"></div>
            <div class="alert alert-danger" style="display:none;"></div>
            
            <?php
            if(isset($_SESSION['search_results']) && !empty($_SESSION['search_results'])){
                $length = count($_SESSION['search_results']);
                $results = array();
                $max = (($search_offset + $search_limit) >= $length) ? $length : $search_offset + $search_limit;
                if(isset($_POST['global-search'])) $_SESSION['time_left'] = round(microtime(true)-$start_time, 2); ?>
                
                <h2><?php echo $texts['RESULTS']." ".($search_offset+1)." - ".$max." ".$texts['FOR_A_TOTAL_OF']." ".$length; ?> (<?php echo $_SESSION['time_left']." ".$texts['SECONDS']; ?>)</h2>
                
                <?php
                if($search_limit > 0){
                    $count = ceil($length/$search_limit);
                    if($count > 1){ ?>
                        <ul class="pagination">
                            <?php
                            for($i = 1; $i <= $count; $i++){
                                $offset2 = ($i-1)*$search_limit;
                                if($offset2 == $search_offset)
                                    echo "<li class=\"active\"><a href=\"#\">".$i."</a></li>";
                                else
                                    echo "<li><a href=\"?search_offset=".$offset2."\">".$i."</a></li>";
                            } ?>
                        </ul>
                        <?php
                    }
                }
                
                for($i = $search_offset; $i < $max; $i++){
                    
                    $result = $_SESSION['search_results'][$i];

                    echo "<p>
                            <big><a href=\"".$result['url']."\">".$result['title']."</a></big>
                            <br>".$result['descr']."<br>
                            <small class=\"text-muted\">".$result['url']."</small>
                        </p>";
                }
                
                if($search_limit > 0){
                    $count = ceil($length/$search_limit);
                    if($count > 1){ ?>
                        <ul class="pagination">
                            <?php
                            for($i = 1; $i <= $count; $i++){
                                $offset2 = ($i-1)*$search_limit;
                                if($offset2 == $search_offset)
                                    echo "<li class=\"active\"><a href=\"#\">".$i."</a></li>";
                                else
                                    echo "<li><a href=\"?search_offset=".$offset2."\">".$i."</a></li>";
                            } ?>
                        </ul>
                        <?php
                    }
                }
            } ?>
        </div>
    </div>
</section>
