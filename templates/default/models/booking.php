<?php
if(isset($_POST['book']) || (ENABLE_BOOKING_REQUESTS == 1 && isset($_POST['request']))){
    
    if(isset($_SESSION['book'])) unset($_SESSION['book']);
    $num_nights = $_POST['nights'];
    
    $_SESSION['book']['hotel'] = $_POST['hotel'];
    $_SESSION['book']['hotel_id'] = $_POST['id_hotel'];
    $_SESSION['book']['from_date'] = $_POST['from_time'];
    $_SESSION['book']['to_date'] = $_POST['to_time'];
    $_SESSION['book']['nights'] = $num_nights;
    $_SESSION['book']['adults'] = $_POST['adults'];
    $_SESSION['book']['children'] = $_POST['children'];
    $_SESSION['book']['extra_services'] = array();
    $_SESSION['book']['activities'] = array();
    $_SESSION['book']['rooms'] = array();
    
    $_SESSION['book']['total'] = 0;
    
    if(isset($_POST['book'])){
		
		$_SESSION['book']['adults'] = 0;
		$_SESSION['book']['children'] = 0;
        
        $_SESSION['book']['amount_rooms'] = 0;
        $_SESSION['book']['amount_activities'] = 0;
        $_SESSION['book']['amount_services'] = 0;
        
        $_SESSION['book']['duty_free_rooms'] = 0;
        $_SESSION['book']['duty_free_activities'] = 0;
        $_SESSION['book']['duty_free_services'] = 0;
       
        $_SESSION['book']['tax_rooms_amount'] = 0;
        $_SESSION['book']['tax_activities_amount'] = 0;
        $_SESSION['book']['tax_services_amount'] = 0;
        
        $_SESSION['book']['discount'] = 0;
        $_SESSION['book']['discount_type'] = '';
        $_SESSION['book']['discount_amount'] = 0;
        
        $_SESSION['book']['taxes'] = array();
        
        $_SESSION['book']['sessid'] = uniqid();
        
        $num_rooms = 0;
        $num_adults = 0;
        $num_children = 0;
        
        if(isset($_POST['amount']) && is_array($_POST['amount'])){
            foreach($_POST['amount'] as $id_room => $values){
                foreach($values as $i => $value){
                    
                    if(isset($_POST['num_adults'][$id_room][$i]) && isset($_POST['num_children'][$id_room][$i]) && isset($_POST['room_'.$id_room])){
                            
                        $room_title = $_POST['room_'.$id_room];
                        $adults = $_POST['num_adults'][$id_room][$i];
                        $children = $_POST['num_children'][$id_room][$i];
                        $duty_free = $_POST['duty_free'][$id_room][$i];
                        
                        if(is_numeric($adults) && is_numeric($children) && ($adults+$children) > 0 && $value > 0){
                            $num_adults += $adults;
                            $num_rooms++;
                            
                            $data = array();
                            $data['id'] = null;
                            $data['id_room'] = $id_room;
                            $data['from_date'] = $_POST['from_time'];
                            $data['to_date'] = $_POST['to_time'];
                            $data['add_date'] = time();
                            $data['sessid'] = $_SESSION['book']['sessid'];
                            
                            $result_room_lock = db_prepareInsert($db, 'pm_room_lock', $data);
                            $result_room_lock->execute();
                            
                            $_SESSION['book']['rooms'][$id_room][$i]['title'] = $room_title;
                            $_SESSION['book']['rooms'][$id_room][$i]['adults'] = $adults;
                            $_SESSION['book']['rooms'][$id_room][$i]['children'] = $children;
                            $_SESSION['book']['rooms'][$id_room][$i]['amount'] = $value;
                            $_SESSION['book']['rooms'][$id_room][$i]['duty_free'] = $duty_free;
                            $_SESSION['book']['rooms'][$id_room][$i]['tax_rate'] = 0;
                            $_SESSION['book']['rooms'][$id_room][$i]['child_age'] = array();
                            
                            if(isset($_POST['child_age'][$id_room][$i])){
                                foreach($_POST['child_age'][$id_room][$i] as $age){
                                    if($age != '')
                                        $_SESSION['book']['rooms'][$id_room][$i]['child_age'][] = $age;
                                }
                                $children = count($_SESSION['book']['rooms'][$id_room][$i]['child_age']);
                            }
                            $num_children += $children;
                            $_SESSION['book']['rooms'][$id_room][$i]['children'] = $children;
                            
                            $_SESSION['book']['adults'] += $adults;
                            $_SESSION['book']['children'] += $children;
                            
                            $_SESSION['book']['taxes'] = array();
                            
                            if(isset($_POST['taxes'][$id_room][$i])){
                                $taxes = $_POST['taxes'][$id_room][$i];
                                if(is_array($taxes)){
                                    foreach($taxes as $tax_id => $tax_amount){
                                        $_SESSION['book']['tax_rooms_amount'] += $tax_amount;
                                        if(!isset($_SESSION['book']['taxes'][$tax_id]['rooms'])) $_SESSION['book']['taxes'][$tax_id]['rooms'] = 0;
                                        $_SESSION['book']['taxes'][$tax_id]['rooms'] += $tax_amount;
                                    }
                                }
                            }
                            $_SESSION['book']['amount_rooms'] += $value;
                            $_SESSION['book']['duty_free_rooms'] += $duty_free;
                        }
                    }
                }
            }
            $_SESSION['book']['num_rooms'] = $num_rooms;
        }
        
        $tourist_tax = (TOURIST_TAX_TYPE == 'fixed') ? $_SESSION['book']['adults']*$num_nights*TOURIST_TAX : $_SESSION['book']['amount_rooms']*TOURIST_TAX/100;
        
        $_SESSION['book']['tourist_tax'] = (ENABLE_TOURIST_TAX == 1) ? $tourist_tax : 0;
        
        $_SESSION['book']['total'] = $_SESSION['book']['duty_free_rooms']+$_SESSION['book']['tax_rooms_amount']+$_SESSION['book']['tourist_tax'];
        $_SESSION['book']['down_payment'] = (ENABLE_DOWN_PAYMENT == 1 && DOWN_PAYMENT_RATE > 0 && $_SESSION['book']['total'] >= DOWN_PAYMENT_AMOUNT) ? $_SESSION['book']['total']*DOWN_PAYMENT_RATE/100 : 0;
    }
    
    if(isset($_SESSION['book']['id'])) unset($_SESSION['book']['id']);

    $result_activity = $db->query('SELECT * FROM pm_activity WHERE hotels REGEXP \'(^|,)'.$_SESSION['book']['hotel_id'].'(,|$)\' AND checked = 1 AND lang = '.LANG_ID);
    if(isset($_SESSION['book']['activities'])) unset($_SESSION['book']['activities']);
    
    if($result_activity !== false && $db->last_row_count() > 0){
        $_SESSION['book']['activities'] = array();
        header('Location: '.DOCBASE.$sys_pages['booking-activities']['alias']);
    }else
        header('Location: '.DOCBASE.$sys_pages['details']['alias']);
    
    exit();
}

$field_notice = array();
$msg_error = '';
$msg_success = '';
$room_stock = 1;
$max_people = 30;
$search_limit = 8;
$search_offset = (isset($_GET['offset']) && is_numeric($_GET['offset'])) ? $_GET['offset'] : 0;

/*********** Num adults ************/
if(isset($_POST['num_adults']) && is_numeric($_POST['num_adults'])) $_SESSION['num_adults'] = $_POST['num_adults'];
elseif(isset($_GET['adults']) && is_numeric($_GET['adults'])) $_SESSION['num_adults'] = $_GET['adults'];
elseif(isset($_SESSION['book']['adults'])) $_SESSION['num_adults'] = $_SESSION['book']['adults'];
elseif(!isset($_SESSION['num_adults'])) $_SESSION['num_adults'] = 1;

/********** Num children ***********/
if(isset($_POST['num_children']) && is_numeric($_POST['num_children'])) $_SESSION['num_children'] = $_POST['num_children'];
elseif(isset($_GET['children']) && is_numeric($_GET['children'])) $_SESSION['num_children'] = $_GET['children'];
elseif(isset($_SESSION['book']['children'])) $_SESSION['num_children'] = $_SESSION['book']['children'];
elseif(!isset($_SESSION['num_children'])) $_SESSION['num_children'] = 0;

/****** Check in / out date ********/
if(isset($_SESSION['book']['from_date'])) $from_time = $_SESSION['book']['from_date'];
else $from_time = gmtime();

if(isset($_SESSION['book']['to_date'])) $to_time = $_SESSION['book']['to_date'];
else $to_time = gmtime()+86400;

if(isset($_POST['from_date']) && !empty($_POST['from_date'])) $_SESSION['from_date'] = htmlentities($_POST['from_date'], ENT_QUOTES, 'UTF-8');
elseif(isset($_GET['from'])) $_SESSION['from_date'] = gmdate('d/m/Y', gm_strtotime(htmlentities($_GET['from'], ENT_QUOTES, 'UTF-8')));
elseif(!isset($_SESSION['from_date'])) $_SESSION['from_date'] = gmdate('d/m/Y', $from_time);

if(isset($_POST['to_date']) && !empty($_POST['to_date'])) $_SESSION['to_date'] = htmlentities($_POST['to_date'], ENT_QUOTES, 'UTF-8');
elseif(isset($_GET['to'])) $_SESSION['to_date'] = gmdate('d/m/Y', gm_strtotime(htmlentities($_GET['to'], ENT_QUOTES, 'UTF-8')));
elseif(!isset($_SESSION['to_date'])) $_SESSION['to_date'] = gmdate('d/m/Y', $to_time);

/********** Searched hotel *********/
if(isset($_POST['hotel_id']) && is_numeric($_POST['hotel_id'])) $_SESSION['hotel_id'] = $_POST['hotel_id'];
elseif(isset($_GET['hotel']) && is_numeric($_GET['hotel'])) $_SESSION['hotel_id'] = $_GET['hotel'];
elseif(isset($_SESSION['hotel_id']) && is_numeric($_SESSION['hotel_id'])) $_SESSION['hotel_id'] = $_SESSION['hotel_id'];
elseif(!isset($_SESSION['hotel_id'])) $_SESSION['hotel_id'] = 0;

/******* Price range (/night) ******/
$price_min = null;
$price_max = null;
if(isset($_POST['price_range'])) $_SESSION['price_range'] = $_POST['price_range'];
elseif(!isset($_SESSION['price_range'])) $_SESSION['price_range'] = '0-0';

$price_range = explode(' - ', $_SESSION['price_range']);
if(count($price_range) == 2){
    $price_min = number_format($price_range[0]/CURRENCY_RATE, 2, '.', '');
    $price_max = number_format($price_range[1]/CURRENCY_RATE, 2, '.', '');
}

/******* Class range (stars) *******/
$class_min = null;
$class_max = null;
if(isset($_POST['class_range'])) $_SESSION['class_range'] = $_POST['class_range'];
elseif(!isset($_SESSION['class_range'])) $_SESSION['class_range'] = '0-5';

$class_range = explode(' - ', $_SESSION['class_range']);
if(count($class_range) == 2){
    $class_min = number_format($class_range[0], 2, '.', '');
    $class_max = number_format($class_range[1], 2, '.', '');
}

/****** Searched destinatation *****/
if(isset($_POST['destination_id']) && is_numeric($_POST['destination_id'])){
    $_SESSION['destination_id'] = $_POST['destination_id'];
    $destination_name = db_getFieldValue($db, 'pm_destination', 'name', $_SESSION['destination_id']);
}elseif(!isset($_SESSION['destination_id'])){
    $_SESSION['destination_id'] = 0;
    $destination_name = '';
}

/******** Destinatation URL ********/
if($article_alias != ''){
    $result_destination = $db->query('SELECT * FROM pm_destination WHERE checked = 1 AND lang = '.LANG_ID.' AND alias = '.$db->quote($article_alias));
    if($result_destination !== false && $db->last_row_count() > 0){
        $destination = $result_destination->fetch(PDO::FETCH_ASSOC);
        $destination_id = $destination['id'];
        $article_id = $destination_id;
        $destination_name = $destination['name'];
        $title_tag = $destination['title_tag'];
        $page_title = $destination['title'];
        $page_subtitle = $destination['subtitle'];
        $page_alias = $page['alias'].'/'.text_format($destination['alias']);
        $_SESSION['destination_id'] = $destination_id;
    }else err404();
}else{
    if(isset($_SESSION['destination_id'])){
        $result_destination = $db->query('SELECT * FROM pm_destination WHERE checked = 1 AND lang = '.LANG_ID.' AND alias != \'\' AND id = '.$_SESSION['destination_id']);
        if($result_destination !== false && $db->last_row_count() > 0){
            $destination = $result_destination->fetch(PDO::FETCH_ASSOC);
            $page_alias = $page['alias'].'/'.text_format($destination['alias']);
            if($search_offset > 0) $page_alias .= '?offset='.$search_offset;
            header('Location:'.DOCBASE.$page_alias);
            exit();
        }
    }
}

$num_people = $_SESSION['num_adults']+$_SESSION['num_children'];

if(!is_numeric($_SESSION['num_adults'])) $field_notice['num_adults'] = $texts['REQUIRED_FIELD'];
if(!is_numeric($_SESSION['num_children'])) $field_notice['num_children'] = $texts['REQUIRED_FIELD'];

if($_SESSION['from_date'] == '') $field_notice['dates'] = $texts['REQUIRED_FIELD'];
else{
    $time = explode('/', $_SESSION['from_date']);
    if(count($time) == 3) $time = gm_strtotime($time[2].'-'.$time[1].'-'.$time[0].' 00:00:00');
    if(!is_numeric($time)) $field_notice['dates'] = $texts['REQUIRED_FIELD'];
    else $from_time = $time;
}
if($_SESSION['to_date'] == '') $field_notice['dates'] = $texts['REQUIRED_FIELD'];
else{
    $time = explode('/', $_SESSION['to_date']);
    if(count($time) == 3) $time = gm_strtotime($time[2].'-'.$time[1].'-'.$time[0].' 00:00:00');
    if(!is_numeric($time)) $field_notice['dates'] = $texts['REQUIRED_FIELD'];
    else $to_time = $time;
}

$today = gm_strtotime(gmdate('Y').'-'.gmdate('n').'-'.gmdate('j').' 00:00:00');

if($from_time < $today || $to_time < $today || $to_time <= $from_time){
    $from_time = $today;
    $to_time = $today+86400;
    $_SESSION['from_date'] = gmdate('d/m/Y', $from_time);
    $_SESSION['to_date'] = gmdate('d/m/Y', $to_time);
}

if(is_numeric($from_time) && is_numeric($to_time)){
    $num_nights = ($to_time-$from_time)/86400;
}else
    $num_nights = 0;

$hotel_ids = array();
$room_ids = array();

if(count($field_notice) == 0){

    if($num_nights <= 0) $msg_error .= $texts['NO_AVAILABILITY'];
    else{
        require_once(getFromTemplate('common/functions.php', false));
        $res_hotel = getRoomsResult($from_time, $to_time, $_SESSION['num_adults'], $_SESSION['num_children']);

        if(empty($res_hotel)) $msg_error .= $texts['NO_AVAILABILITY'];
        else $_SESSION['res_hotel'] = $res_hotel;
    }
}

$id_room = 0;

$result_room_rate = $db->prepare('SELECT MIN(price) as min_price FROM pm_rate WHERE id_room = :id_room');
$result_room_rate->bindParam(':id_room', $id_room);

$id_hotel = 0;

$result_budget_room = $db->prepare('SELECT * FROM pm_room WHERE id_hotel = :id_hotel AND checked = 1 AND lang = '.LANG_ID);
$result_budget_room->bindParam(':id_hotel', $id_hotel);

$hidden_hotels = array();
$hidden_rooms = array();
$room_prices = array();
$hotel_prices = array();
$result_budget_hotel = $db->query('SELECT * FROM pm_hotel WHERE checked = 1 AND lang = '.LANG_ID);
if($result_budget_hotel !== false){
    foreach($result_budget_hotel as $i => $row){
        $id_hotel = $row['id'];
        $hotel_min_price = 0;
        $hotel_max_price = 0;
        $result_budget_room->execute();
        if($result_budget_room !== false){
            foreach($result_budget_room as $row){
                
                $id_room = $row['id'];
                $room_price = $row['price'];
                $max_people = $row['max_people'];
                $min_people = $row['min_people'];
                $max_adults = $row['max_adults'];
                $max_children = $row['max_children'];
                
                if(!isset($res_hotel[$id_hotel][$id_room])
                || isset($res_hotel[$id_hotel][$id_room]['error'])
                || ($_SESSION['num_adults']+$_SESSION['num_children'] > $max_people)
                || ($_SESSION['num_adults']+$_SESSION['num_children'] < $min_people)
                || ($_SESSION['num_adults'] > $max_adults)
                || ($_SESSION['num_children'] > $max_children)){
                    $amount = $room_price;
                    $result_room_rate->execute();
                    if($result_room_rate !== false && $db->last_row_count() > 0){
                        $row = $result_room_rate->fetch();
                        if($row['min_price'] > 0) $amount = $row['min_price'];
                    }
                    $full_price = 0;
                    $type = $texts['NIGHT'];
                    $price_night = $amount;
                }else{
                    $amount = $res_hotel[$id_hotel][$id_room]['amount']+$res_hotel[$id_hotel][$id_room]['fixed_sup'];
                    $full_price = $res_hotel[$id_hotel][$id_room]['full_price']+$res_hotel[$id_hotel][$id_room]['fixed_sup'];
                    $type = $num_nights.' '.$texts['NIGHTS'];
                    $price_night = $amount/$num_nights;
                }
                
                if((!empty($price_min) && $price_night < $price_min) || (!empty($price_max) && $price_night > $price_max)) $hidden_rooms[] = $id_room;
                else{
                    $room_prices[$id_room]['amount'] = $amount;
                    $room_prices[$id_room]['full_price'] = $full_price;
                    $room_prices[$id_room]['type'] = $type;
                }
                if(empty($hotel_min_price) || $price_night < $hotel_min_price) $hotel_min_price = $price_night;
                if(empty($hotel_max_price) || $price_night > $hotel_max_price) $hotel_max_price = $price_night;
            }
        } 
        if((!empty($price_min) && $hotel_max_price < $price_min) || (!empty($price_max) && $hotel_min_price > $price_max)) $hidden_hotels[] = $id_hotel;
        $hotel_prices[$id_hotel] = $hotel_min_price;
    }
}

$result_rating = $db->prepare('SELECT AVG(rating) as avg_rating FROM pm_comment WHERE item_type = \'hotel\' AND id_item = :id_hotel AND checked = 1 AND rating > 0 AND rating <= 5');
$result_rating->bindParam(':id_hotel', $id_hotel);
                
$id_facility = 0;
$result_facility_file = $db->prepare('SELECT * FROM pm_facility_file WHERE id_item = :id_facility AND checked = 1 AND lang = '.DEFAULT_LANG.' AND type = \'image\' AND file != \'\' ORDER BY rank LIMIT 1');
$result_facility_file->bindParam(':id_facility', $id_facility);

$room_facilities = '0';
$result_room_facilities = $db->prepare('SELECT * FROM pm_facility WHERE lang = '.LANG_ID.' AND FIND_IN_SET(id, :room_facilities) ORDER BY rank LIMIT 18');
$result_room_facilities->bindParam(':room_facilities', $room_facilities);

$hotel_facilities = '0';
$result_hotel_facilities = $db->prepare('SELECT * FROM pm_facility WHERE lang = '.LANG_ID.' AND FIND_IN_SET(id, :hotel_facilities) ORDER BY rank LIMIT 8');
$result_hotel_facilities->bindParam(':hotel_facilities', $hotel_facilities);

$query_room = 'SELECT * FROM pm_room WHERE id_hotel = :id_hotel AND checked = 1 AND lang = '.LANG_ID;
if(!empty($hidden_rooms)) $query_room .= ' AND id NOT IN('.implode(',', $hidden_rooms).')';
$query_room .= ' ORDER BY';
if(!empty($room_ids)) $query_room .= ' CASE WHEN id IN('.implode(',', $room_ids).') THEN 3 ELSE 4 END,';
$query_room .= ' rank';
$result_room = $db->prepare($query_room);
$result_room->bindParam(':id_hotel', $id_hotel);

$result_room_file = $db->prepare('SELECT * FROM pm_room_file WHERE id_item = :id_room AND checked = 1 AND lang = '.LANG_ID.' AND type = \'image\' AND file != \'\' ORDER BY rank');
$result_room_file->bindParam(':id_room', $id_room);

$result_hotel_file = $db->prepare('SELECT * FROM pm_hotel_file WHERE id_item = :id_hotel AND checked = 1 AND lang = '.LANG_ID.' AND type = \'image\' AND file != \'\' ORDER BY rank LIMIT 1');
$result_hotel_file->bindParam(':id_hotel', $id_hotel);

$query_hotel = 'SELECT * FROM pm_hotel WHERE checked = 1 AND lang = '.LANG_ID;
if($_SESSION['destination_id'] > 0) $query_hotel .= ' AND id_destination = '.$_SESSION['destination_id'];
if(!empty($class_min)) $query_hotel .= ' AND class >= '.$class_min;
if(!empty($class_max)) $query_hotel .= ' AND class <= '.$class_max;
if(isset($_GET['hotel']) && is_numeric($_GET['hotel'])) $query_hotel .= ' AND id = '.$_GET['hotel'];
if(!empty($hidden_hotels)) $query_hotel .= ' AND id NOT IN('.implode(',', $hidden_hotels).')';
$query_hotel .= ' ORDER BY';
if($_SESSION['hotel_id'] != 0 && !isset($_GET['hotel'])) $query_hotel .= ' CASE WHEN id = '.$_SESSION['hotel_id'].' THEN 1 ELSE 4 END,';
if(!empty($hotel_ids)) $query_hotel .= ' CASE WHEN id IN('.implode(',', $hotel_ids).') THEN 3 ELSE 4 END,';
$query_hotel .= ' rank';

$num_results = 0;
$result_hotel = $db->query($query_hotel);
if($result_hotel !== false){
    $num_results = $db->last_row_count();
    
    $visible_hotels = $result_hotel->fetchAll(PDO::FETCH_COLUMN, 0);
    if(!empty($visible_hotels)){
        $visible_hotels = array_intersect_key($hotel_prices, array_flip($visible_hotels));
        $subtitle = str_replace('{min_price}', formatPrice(min($visible_hotels)*CURRENCY_RATE), $texts['BEST_RATES_SUBTITLE']);
        if($article_id > 0) $page_subtitle = $subtitle;
        else $page['subtitle'] = $subtitle;
    }
}

$query_hotel .= ' LIMIT '.$search_limit.' OFFSET '.$search_offset;

$result_hotel = $db->query($query_hotel);

if($result_hotel !== false && $db->last_row_count() == 0){
    $msg_error .= $texts['NO_HOTEL_FOUND'];
    if($destination_name != '') $msg_error .= ' '.$texts['FOR'].' <b>'.$destination_name.'</b>';
}

$query_destination = 'SELECT * FROM pm_destination WHERE';
if($_SESSION['destination_id'] > 0) $query_destination .= ' id != '.$_SESSION['destination_id'].' AND';
$query_destination .= ' checked = 1 AND lang = '.LANG_ID.' ORDER BY rand() LIMIT 5';

$nb_destinations = 0;
$result_destination = $db->query($query_destination, PDO::FETCH_ASSOC);
if($result_destination !== false)
    $nb_destinations = $db->last_row_count();

if(isset($_GET['action'])){
	if(isset($_SESSION['book'])) unset($_SESSION['book']);
    if($_GET['action'] == 'confirm')
        $msg_success .= $texts['PAYMENT_SUCCESS_NOTICE'];
    elseif($_GET['action'] == 'cancel')
        $msg_error .= $texts['PAYMENT_CANCEL_NOTICE'];
}

/* ==============================================
 * CSS AND JAVASCRIPT USED IN THIS MODEL
 * ==============================================
 */
$javascripts[] = DOCBASE.'js/plugins/no-ui-slider/wNumb.js';
$javascripts[] = '//cdnjs.cloudflare.com/ajax/libs/noUiSlider/9.2.0/nouislider.min.js';
$stylesheets[] = array('file' => '//cdnjs.cloudflare.com/ajax/libs/noUiSlider/9.2.0/nouislider.min.css', 'media' => 'all');

if(SHOW_CALENDAR == 1){
	$javascripts[] = DOCBASE.'js/plugins/jquery.event.calendar/js/jquery.event.calendar.js';
	if(is_file(SYSBASE.'js/plugins/jquery.event.calendar/js/languages/jquery.event.calendar.'.LANG_TAG.'.js'))
		$javascripts[] = DOCBASE.'js/plugins/jquery.event.calendar/js/languages/jquery.event.calendar.'.LANG_TAG.'.js';
	else
		$javascripts[] = DOCBASE.'js/plugins/jquery.event.calendar/js/languages/jquery.event.calendar.en.js';
	$stylesheets[] = array('file' => DOCBASE.'js/plugins/jquery.event.calendar/css/jquery.event.calendar.css', 'media' => 'all');
}
    

$stylesheets[] = array('file' => '//cdnjs.cloudflare.com/ajax/libs/bootstrap-star-rating/3.5.5/css/star-rating.min.css', 'media' => 'all');
$javascripts[] = '//cdnjs.cloudflare.com/ajax/libs/bootstrap-star-rating/3.5.5/js/star-rating.min.js';

$stylesheets[] = array('file' => '//cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.0.0-beta.2.4/assets/owl.carousel.min.css', 'media' => 'all');
$stylesheets[] = array('file' => '//cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.0.0-beta.2.4/assets/owl.theme.default.min.css', 'media' => 'all');
$javascripts[] = '//cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.0.0-beta.2.4/owl.carousel.min.js';

$javascripts[] = DOCBASE.'js/plugins/live-search/jquery.liveSearch.js';

$stylesheets[] = array('file' => DOCBASE.'js/plugins/simpleweather/css/simpleweather.min.css', 'media' => 'all');
$javascripts[] = '//cdn.rawgit.com/monkeecreate/jquery.simpleWeather/master/jquery.simpleWeather.min.js';

require(getFromTemplate('common/header.php', false));

if(isset($_GET['action']) && $_GET['action'] == 'confirm'){ ?>
    <script>
        $(function(){
            setTimeout(function(){
                window.location.replace('<?php echo DOCBASE.LANG_ALIAS; ?>');
            }, 6000);
        });
    </script>
    <?php
} ?>

<section id="page">
    
    <?php include(getFromTemplate('common/page_header.php', false)); ?>
    
    <div id="content" class="pb30">
        
        <div id="search-page" class="mb20">
            <div class="container">
                <?php include(getFromTemplate('common/search.php', false)); ?>
            </div>
            <div class="clearfix"></div>
        </div>
        
        <div class="container">
            <div class="row">
                <div class="col-md-<?php echo (isset($destination) || $nb_destinations > 0) ? 9 : 12; ?>">
                
					<div class="alert alert-success text-center lead" style="display:none;"></div>
					<div class="alert alert-danger text-center lead" style="display:none;"></div>
                
                    <?php
                    if($page['text'] != ''){ ?>
                        <div class="mb20"><?php echo $page['text']; ?></div>
                        <?php
                    } ?>
                    
                    <div class="boxed mb20">
                        <?php echo $texts['BOOKING_NOTICE']; ?>
                    </div>
                    
                    <?php
                    if($result_hotel !== false){
                        foreach($result_hotel as $i => $row){
                            $id_hotel = $row['id'];
                            $hotel_title = $row['title'];
                            $hotel_alias = $row['alias'];
                            $hotel_class = $row['class'];
                            $hotel_subtitle = $row['subtitle'];
                            $hotel_descr = $row['descr'];
                            $hotel_facilities = $row['facilities']; ?>
                            
                            <form action="<?php echo DOCBASE.$sys_pages['booking']['alias']; ?>" method="post" class="ajax-form form-<?php echo $i; ?>">
                                <input type="hidden" name="from_time" value="<?php echo $from_time; ?>">
                                <input type="hidden" name="to_time" value="<?php echo $to_time; ?>">
                                <input type="hidden" name="nights" value="<?php echo $num_nights; ?>">
                                <input type="hidden" name="id_hotel" value="<?php echo $id_hotel; ?>">
                                <input type="hidden" name="hotel" value="<?php echo $hotel_title; ?>">

                                <div class="boxed mb20 booking-result">
                                    <div class="row">
                                        <div class="col-sm-4 col-md-<?php echo (isset($destination) || $nb_destinations > 0) ? 4 : 3; ?>">
                                            <?php
                                            $result_hotel_file->execute();
                                            if($result_hotel_file !== false && $db->last_row_count() > 0){
                                                $row = $result_hotel_file->fetch(PDO::FETCH_ASSOC);

                                                $file_id = $row['id'];
                                                $filename = $row['file'];
                                                $label = $row['label'];

                                                $realpath = SYSBASE.'medias/hotel/small/'.$file_id.'/'.$filename;
                                                $thumbpath = DOCBASE.'medias/hotel/small/'.$file_id.'/'.$filename;
                                                $zoompath = DOCBASE.'medias/hotel/big/'.$file_id.'/'.$filename;

                                                if(is_file($realpath)){
                                                    $s = getimagesize($realpath); ?>
                                                    <div class="img-container lazyload md">
                                                        <img alt="<?php echo $label; ?>" data-src="<?php echo $thumbpath; ?>" itemprop="photo" width="<?php echo $s[0]; ?>" height="<?php echo $s[1]; ?>">
                                                    </div>
                                                    <?php
                                                }
                                            } ?>
                                        </div>
                                        <div class="pt15 col-sm-4 col-md-<?php echo (isset($destination) || $nb_destinations > 0) ? 5 : 6; ?>">
                                            <h3>
                                                <?php echo $hotel_title; ?>
                                                <small>
                                                    <?php
                                                    if(!empty($hotel_class)){
                                                        for($j = 0; $j < $hotel_class; $j++) echo '<i class="fas fa-fw fa-star"></i>';
                                                    } ?>
                                                </small>
                                            </h3>
                                            <h4><?php echo $hotel_subtitle; ?></h4>
                                            <?php echo strtrunc(strip_tags($hotel_descr), 120); ?>
                                            <div class="clearfix mt10">
                                                <?php
                                                $result_hotel_facilities->execute();
                                                if($result_hotel_facilities !== false && $db->last_row_count() > 0){
                                                    foreach($result_hotel_facilities as $row){
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
                                                    } ?>
                                                    <span class="facility-icon">
                                                        <a href="<?php echo DOCBASE.$sys_pages['hotels']['alias'].'/'.text_format($hotel_alias); ?>" title="<?php echo $texts['READMORE']; ?>" class="tips">...</a>
                                                    </span>
                                                    <?php
                                                } ?>
                                            </div>
                                        </div>
                                        <div class="pt15 pb15 col-sm-4 col-md-3 text-center sep">
                                            <div class="price text-primary">
                                                <?php
                                                if(isset($hotel_prices[$id_hotel]) && $hotel_prices[$id_hotel] > 0){
                                                    echo $texts['FROM_PRICE']; ?>
                                                    <span itemprop="priceRange">
                                                        <?php echo formatPrice($hotel_prices[$id_hotel]*CURRENCY_RATE); ?>
                                                    </span>
                                                    / <?php echo $texts['NIGHT'];
                                                } ?>
                                            </div>
                                            <?php
                                            $result_rating->execute();
                                            if($result_rating !== false && $db->last_row_count() > 0){
                                                $row = $result_rating->fetch();
                                                $hotel_rating = $row['avg_rating'];
                                                
                                                if($hotel_rating > 0 && $hotel_rating <= 5){ ?>
                                                
                                                    <input type="hidden" class="rating" value="<?php echo $hotel_rating; ?>" data-rtl="<?php echo (RTL_DIR) ? true : false; ?>" data-size="xs" readonly="true" data-show-clear="false" data-show-caption="false">
                                                    <?php
                                                }
                                            } ?>
                                            <a class="btn btn-primary mt10 btn-block" href="<?php echo DOCBASE.$sys_pages['hotels']['alias'].'/'.text_format($hotel_alias); ?>">
                                                <i class="fas fa-fw fa-plus-circle"></i>
                                                <?php echo $texts['READMORE']; ?>
                                            </a>
                                            <a href="#" data-target="#btn-collapse-<?php echo $id_hotel; ?>" class="btn btn-success btn-block mt10 anchor-toggle">
                                                <?php echo $texts['BOOK']; ?>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 panel-collapse collapse<?php echo ((!isset($_POST['hotel_id']) || $_POST['hotel_id'] != $id_hotel) && $num_results != 1) ? ' collapse' : ' in'; ?>" id="collapse-<?php echo $id_hotel; ?>">
                                            
                                            <div class="row">
												<div class="col-md-12">
													<span id="btn-collapse-<?php echo $id_hotel; ?>" data-toggle="collapse" data-target="#collapse-<?php echo $id_hotel; ?>" class="btn-toggle collapsed">
														<i class="fas fa-fw fa-angle-up"></i>
													</span>
												</div>
											</div>
                                            <div class="row">
												<div class="col-md-12">
													<div class="boxed mt10 booking-summary">
														<p class="lead mb0"><?php echo '<big><i class="fas fa-fw fa-calendar"></i> <b>'.gmstrftime(DATE_FORMAT, $from_time).'</b></big> <big><i class="fas fa-fw fa-arrow-right"></i> <b>'.gmstrftime(DATE_FORMAT, $to_time).'</b></big>'; ?></p>
														<span id="booking-amount_<?php echo $id_hotel; ?>">
															<?php
															$room_stock = 0;
															$result_room->execute();
															if($result_room !== false){
																foreach($result_room as $row){
																	$id_room = $row['id'];
																	$room_stock += isset($res_hotel[$id_hotel][$id_room]['room_stock']) ? $res_hotel[$id_hotel][$id_room]['room_stock'] : $row['stock'];
																}
															}
															
															if(ENABLE_BOOKING_REQUESTS == 1 && ($num_nights <= 0 || (empty($res_hotel[$id_hotel]) && $room_stock > 0) || (!empty($res_hotel[$id_hotel]) && $room_stock <= 0))){
																echo '
																<input type="hidden" name="adults" value="'.$_SESSION['num_adults'].'">
																<input type="hidden" name="children" value="'.$_SESSION['num_children'].'">
																<button name="request" class="btn btn-default btn-lg btn-block mt5"><i class="fas fa-fw fa-comment"></i> '.$texts['MAKE_A_REQUEST'].'</small></button>';
															} ?>
														</span>
													</div>
												</div>
											</div>
                                            
                                            <div class="boxed">
                                                <?php
                                                $result_room->execute();
                                                if($result_room !== false){
													$nb_rooms = $db->last_row_count();
                                                    foreach($result_room as $row){
                                                        
                                                        $id_room = $row['id'];
                                                        $room_title = $row['title'];
                                                        $room_alias = $row['alias'];
                                                        $room_subtitle = $row['subtitle'];
                                                        $room_descr = $row['descr'];
                                                        $room_price = $row['price'];
                                                        $room_stock = $row['stock'];
                                                        $max_adults = $row['max_adults'];
                                                        $max_children = $row['max_children'];
                                                        $max_people = $row['max_people'];
                                                        $min_people = $row['min_people'];
                                                        $room_facilities = $row['facilities'];
                        
                                                        $room_stock = isset($res_hotel[$id_hotel][$id_room]['room_stock']) ? $res_hotel[$id_hotel][$id_room]['room_stock'] : $row['stock'];
                                                
                                                        $amount = $room_prices[$id_room]['amount'];
                                                        $full_price = $room_prices[$id_room]['full_price'];
                                                        $type = $room_prices[$id_room]['type']; ?>

                                                        <input type="hidden" name="rooms[]" value="<?php echo $id_room; ?>">
                                                        <input type="hidden" name="room_<?php echo $id_room; ?>" value="<?php echo $room_title; ?>">
                                                            
                                                        <div class="row room-result">
                                                            <div class="col-lg-3 hidden-sm hidden-xs">
                                                                <?php
                                                                $result_room_file->execute();
                                                                if($result_room_file !== false && $db->last_row_count() > 0){
                                                                    $row = $result_room_file->fetch(PDO::FETCH_ASSOC);

                                                                    $file_id = $row['id'];
                                                                    $filename = $row['file'];
                                                                    $label = $row['label'];

                                                                    $realpath = SYSBASE.'medias/room/small/'.$file_id.'/'.$filename;
                                                                    $thumbpath = DOCBASE.'medias/room/small/'.$file_id.'/'.$filename;
                                                                    $zoompath = DOCBASE.'medias/room/big/'.$file_id.'/'.$filename;

                                                                    if(is_file($realpath)){
                                                                        $s = getimagesize($realpath); ?>
                                                                        <div class="img-container lazyload md">
                                                                            <img alt="<?php echo $label; ?>" data-src="<?php echo $thumbpath; ?>" itemprop="photo" width="<?php echo $s[0]; ?>" height="<?php echo $s[1]; ?>">
                                                                        </div>
                                                                        <?php
                                                                    }
                                                                } ?>
                                                            </div>
                                                            <div class="<?php echo (SHOW_CALENDAR == 1) ? 'col-sm-4 col-md-5 col-lg-4' : 'col-sm-7 col-md-7 col-lg-6'; ?>">
                                                                <h4><?php echo $room_title; ?></h4>
                                                                <p><?php echo $room_subtitle; ?></p>
                                                                <?php echo strtrunc(strip_tags($room_descr), 100); ?>
                                                                <div class="clearfix mt10">
                                                                    <?php
                                                                    $result_room_facilities->execute();
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
                                                            </div>
                                                            <div class="<?php echo (SHOW_CALENDAR == 1) ? 'col-lg-2 col-md-3 col-sm-3' : 'col-lg-3 col-md-5 col-sm-5'; ?> text-center sep">
                                                                <div class="price">
                                                                    <span itemprop="priceRange"><?php echo formatPrice($amount*CURRENCY_RATE); ?></span>
                                                                    <?php
                                                                    if($full_price > 0 && $full_price > $amount){ ?>
                                                                        <br><s class="text-warning"><?php echo formatPrice($full_price*CURRENCY_RATE); ?></s>
                                                                        <?php
                                                                    } ?>
                                                                </div>
                                                                <div class="mb10 text-muted"><?php echo $texts['PRICE'].' / '.$type; ?></div>
                                                                <?php echo $texts['CAPACITY']; ?> : <i class="fas fa-fw fa-male"></i>x<?php echo $max_people; ?>
                                                                
                                                                <?php
                                                                if($room_stock > 0){ ?>
                                                                    <div class="pt10 form-inline">
                                                                        <i class="fas fa-fw fa-tags"></i> <?php echo $texts['NUM_ROOMS']; ?><br>
                                                                        <select name="num_rooms[<?php echo $id_room; ?>]" class="form-control btn-group-sm sendAjaxForm selectpicker" data-target="#room-options-<?php echo $id_room; ?>" data-extratarget="#booking-amount_<?php echo $id_hotel; ?>" data-action="<?php echo getFromTemplate('common/change_num_rooms.php'); ?>?room=<?php echo $id_room; ?>">
                                                                            <?php
                                                                            for($i = 0; $i <= $room_stock; $i++){ ?>
                                                                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                                                                <?php
                                                                            } ?>
                                                                        </select>
                                                                    </div>
                                                                    <?php
                                                                }else{ ?>
                                                                    <div class="mt10 btn btn-danger btn-block" disabled="disabled"><?php echo $texts['NO_AVAILABILITY']; ?></div>
                                                                    <?php
                                                                } ?>
                                                
                                                                <p class="lead">
                                                                    <span class="clearfix"></span>
                                                                    <a class="btn btn-primary mt10 btn-block ajax-popup-link btn-sm" href="<?php echo getFromTemplate('common/room-popup.php', true); ?>" data-params="room=<?php echo $id_room; ?>">
                                                                        <i class="fas fa-fw fa-plus-circle"></i>
                                                                        <?php echo $texts['READMORE']; ?>
                                                                    </a>
                                                                </p>
                                                            </div>
                                                            <?php
                                                            if(SHOW_CALENDAR == 1){ ?>
																<div class="col-lg-3 col-md-4 col-sm-5 sep">
																	<div class="hb-calendar" data-cur_month="<?php echo gmdate('n', $from_time); ?>" data-cur_year="<?php echo gmdate('Y', $from_time); ?>" data-custom_var="room=<?php echo $id_room; ?>" data-day_loader="<?php echo getFromTemplate('common/get_days.php'); ?>"></div>
																</div>
																<?php
															} ?>
                                                            <div class="clearfix"></div>
                                                            <div id="room-options-<?php echo $id_room; ?>" class="room-options"></div>
                                                        </div>
                                                        <?php
                                                        if($nb_rooms > 1) echo '<hr>';
                                                    }
                                                } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <?php
                        }
                        if($search_limit > 0){
                            $nb_pages = ceil($num_results/$search_limit);
                            if($nb_pages > 1){ ?>
                                <div class="container text-center">
                                    <div class="btn-group">
                                        <?php
                                        for($i = 1; $i <= $nb_pages; $i++){
                                            $offset = ($i-1)*$search_limit;
                                            
                                            if($offset == $search_offset)
                                                echo '<span class="btn btn-default disabled">'.$i.'</span>';
                                            else{
                                                $request = ($offset == 0) ? '' : '?offset='.$offset;
                                                echo '<a class="btn btn-default" href="'.DOCBASE.$sys_pages['booking']['alias'].$request.'">'.$i.'</a>';
                                            }
                                        } ?>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                    } ?>
                </div>
                <?php
                if(isset($destination) || $nb_destinations > 0){ ?>
                    <aside class="col-md-3">
                        <div class="boxed mb20">
                            <?php
                            if(isset($destination)){ ?>
								<div class="mb20">
									<h2><?php echo $destination['name']; ?></h2>
									<div class="owl-carousel owlWrapper" data-items="1" data-autoplay="false" data-dots="true" data-nav="false" data-rtl="<?php echo (RTL_DIR) ? 'true' : 'false'; ?>">
										<?php
										if(!empty($destination['video'])){ ?>
											<div class="video-container">
												<iframe src="<?php echo $destination['video']; ?>" frameborder="0" allowfullscreen></iframe>
											</div>
											<?php
										}
										$result_destination_file = $db->query('SELECT * FROM pm_destination_file WHERE id_item = '.$destination_id.' AND checked = 1 AND lang = '.DEFAULT_LANG.' AND type = \'image\' AND file != \'\' ORDER BY rank');
										if($result_destination_file !== false){
											
											foreach($result_destination_file as $i => $row){
											
												$file_id = $row['id'];
												$filename = $row['file'];
												$label = $row['label'];
												
												$realpath = SYSBASE.'medias/destination/big/'.$file_id.'/'.$filename;
												$thumbpath = DOCBASE.'medias/destination/big/'.$file_id.'/'.$filename;
												
												if(is_file($realpath)){ ?>
													<img alt="<?php echo $label; ?>" src="<?php echo $thumbpath; ?>" class="img-responsive" style="max-height:600px;"/>
													<?php
												}
											}
										} ?>
									</div>
									
									<div class="text-center"><span class="simple-weather" data-location="<?php echo $destination['name']; ?>" data-unit="c"></span></div>
									
									<script type="text/javascript">
										var locations = [
											['<?php echo $destination['name']; ?>', '', '<?php echo $destination['lat']; ?>', '<?php echo $destination['lng']; ?>']
										];
									</script>
									<div id="mapWrapper" class="mb10" data-marker="<?php echo getFromTemplate('images/marker.png'); ?>" data-api_key="<?php echo GMAPS_API_KEY; ?>"></div>
								
									<p class="lead"><?php echo $destination['subtitle']; ?></small>
									
									<?php
									echo $destination['text']; ?>
								</div>
								<?php
							}
                            
                            if($nb_destinations > 0){ ?>
                            
                                <h2 class="mt0 mb10"><?php echo $texts['TOP_DESTINATIONS']; ?></h2>
                                
                                <?php
                                $id_destination = 0;
                                $result_destination_file = $db->prepare('SELECT * FROM pm_destination_file WHERE id_item = :id_destination AND checked = 1 AND lang = '.LANG_ID.' AND type = \'image\' AND file != \'\' ORDER BY rank');
                                $result_destination_file->bindParam(':id_destination', $id_destination, PDO::PARAM_STR);

                                foreach($result_destination as $i => $row){
                                    $id_destination = $row['id'];
                                    $destination_name = $row['name'];
                                    $destination_subtitle = $row['subtitle'];
                                    $destination_alias = $row['alias']; ?>
                                    
                                    <a href="<?php echo DOCBASE.$page['alias'].'/'.text_format($destination_alias); ?>">
                                        <div class="row">
                                            <div class="col-xs-4 mb20">
                                                <?php
                                                $result_destination_file->execute();
                                                if($result_destination_file !== false && $db->last_row_count() > 0){
                                                    $row = $result_destination_file->fetch(PDO::FETCH_ASSOC);
                                                    
                                                    $file_id = $row['id'];
                                                    $filename = $row['file'];
                                                    $label = $row['label'];
                                                    
                                                    $realpath = SYSBASE.'medias/destination/small/'.$file_id.'/'.$filename;
                                                    $thumbpath = DOCBASE.'medias/destination/small/'.$file_id.'/'.$filename;
                                                        
                                                    if(is_file($realpath)){
                                                        $s = getimagesize($realpath); ?>
                                                        <div class="img-container lazyload sm">
                                                            <img alt="<?php echo $label; ?>" data-src="<?php echo $thumbpath; ?>" itemprop="photo" width="<?php echo $s[0]; ?>" height="<?php echo $s[1]; ?>">
                                                        </div>
                                                        <?php
                                                    }
                                                } ?>
                                            </div>
                                            <div class="col-xs-8">
                                                <h3 class="mb0"><?php echo $destination_name; ?></h3>
                                                <?php
                                                if($destination_subtitle != ''){ ?>
                                                    <h4 class="mb0"><?php echo $destination_subtitle; ?></h4>
                                                    <?php
                                                } ?>
                                            </div>
                                        </div>
                                    </a>
                                    <?php
                                }
                            } ?>
                        </div>
                    </aside>
                    <?php
                } ?>
            </div>
        </div>
    </div>
</section>
<script>
	$(function(){
		$('select[name^="num_rooms"]').on('change', function(){
			var obj = $(this);
			setTimeout(function(){
				if(obj.val() > 0){
					var attr = obj.attr('name').match(/\[(\d+)\]/);
					$('select[name^="num_adults['+attr[1]+']').each(function(i, t){
						setTimeout(function(){
							$(t).val('1').trigger('change');
						}, 100*i);
					});
					
				}
			}, 500);
		});
		$('.room-options').on('change', '[name^="num_children"]', function(){
			var extraTarget = $(this).parents('.booking-result').find('[id^="booking-amount_"]').attr('id');
			console.log(extraTarget);
			var attr = $(this).attr('name').match(/\[(\d+)\]\[(\d+)\]/);
			var target = $('#children-options-'+attr[1]+'-'+attr[2]);
			var num = $(this).val();
			var html = '<?php echo $texts['CHILDREN_AGE']; ?>:<br>';
			for(var i = 0; i < num; i++){
				html +=
				'<div class="input-group input-group-sm">'+
					'<div class="input-group-addon"><?php echo ucfirst($texts['CHILD']); ?> '+(i+1)+'</div>'+
						'<select name="child_age['+attr[1]+']['+attr[2]+']['+i+']" class="form-control sendAjaxForm selectpicker" data-extratarget="#'+extraTarget+'" data-action="<?php echo getFromTemplate('common/change_num_people.php'); ?>?index='+attr[2]+'&id_room='+attr[1]+'" data-target="#room-result-'+attr[1]+'-'+attr[2]+'" style="display: none;">'+
							'<option value="">-</option>';
							for(var j = 0; j < 18; j++) html += '<option value="'+j+'">'+j+'</option>';
							html +=
						'</select>'+
					'</div>'+
				'</div>';
			}
			target.html(html);
			$('.selectpicker').selectpicker('refresh');
		});
	});
</script>
