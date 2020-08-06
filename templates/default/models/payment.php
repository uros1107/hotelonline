<?php
if(!isset($_SESSION['book']) || count($_SESSION['book']) == 0){
    header('Location: '.DOCBASE.$sys_pages['booking']['alias']);
    exit();
}else
    $_SESSION['book']['step'] = 'payment';

$msg_error = '';
$msg_success = '';
$field_notice = array();

$paypal_email = '';
if(ENABLE_MULTI_VENDORS == 1){
    $result_hotel = $db->query('SELECT paypal_email FROM pm_hotel WHERE id = '.$_SESSION['book']['hotel_id']);
    if($result_hotel !== false && $db->last_row_count() > 0){
        $row = $result_hotel->fetch();
        $paypal_email = $row['paypal_email'];
    }
}

$payment_arr = array_map('trim', explode(',', PAYMENT_TYPE));
if(ENABLE_MULTI_VENDORS == 1 && $paypal_email != ''){
    $payment_type = 'paypal';
    $handle = true;
}elseif(count($payment_arr) == 1){
    $payment_type = PAYMENT_TYPE;
    $handle = true;
}elseif(isset($_POST['payment_type'])){
    $payment_type = $_POST['payment_type'];
    $handle = true;
}else{
    $payment_type = PAYMENT_TYPE;
    $handle = false;
}

if(isset($_SESSION['book']['id'])){
    $result_booking = $db->query('SELECT * FROM pm_booking WHERE id = '.$_SESSION['book']['id'].' AND status != 1 AND trans != \'\'');
    if($result_booking !== false && $db->last_row_count() > 0){
        unset($_SESSION['book']);
        header('Location: '.DOCBASE.$sys_pages['booking']['alias']);
        exit();
    }
}

$total = $_SESSION['book']['total'];
$payed_amount = (ENABLE_DOWN_PAYMENT == 1 && $_SESSION['book']['down_payment'] > 0) ? $_SESSION['book']['down_payment'] : $total;
    
$users = '';
$result_owner = $db->query('SELECT users FROM pm_hotel WHERE id = '.$_SESSION['book']['hotel_id']);
if($result_owner !== false && $db->last_row_count() > 0){
	$row = $result_owner->fetch();
	$users = $row['users'];
}
$hotel_owners = array();
$result_owner = $db->query('SELECT * FROM pm_user WHERE id IN ('.$users.')');
if($result_owner !== false && $db->last_row_count() > 0)
	$hotel_owners = $result_owner->fetchAll();

if($handle){
    if(!isset($_SESSION['book']['id']) || is_null($_SESSION['book']['id'])){
                               
        $data = array();
        $data['id'] = null;
        $data['id_user'] = $_SESSION['book']['id_user'];
        $data['firstname'] = $_SESSION['book']['firstname'];
        $data['lastname'] = $_SESSION['book']['lastname'];
        $data['email'] = $_SESSION['book']['email'];
        $data['company'] = $_SESSION['book']['company'];
        $data['address'] = $_SESSION['book']['address'];
        $data['postcode'] = $_SESSION['book']['postcode'];
        $data['city'] = $_SESSION['book']['city'];
        $data['phone'] = $_SESSION['book']['phone'];
        $data['mobile'] = $_SESSION['book']['mobile'];
        $data['country'] = $_SESSION['book']['country'];
        $data['comments'] = $_SESSION['book']['comments'];
        $data['id_hotel'] = $_SESSION['book']['hotel_id'];
        $data['from_date'] = $_SESSION['book']['from_date'];
        $data['to_date'] = $_SESSION['book']['to_date'];
        $data['nights'] = $_SESSION['book']['nights'];
        $data['adults'] = $_SESSION['book']['adults'];
        $data['children'] = $_SESSION['book']['children'];
        $data['amount'] = number_format($_SESSION['book']['amount_rooms'], 2, '.', '');
        //$data['tourist_tax'] = number_format($_SESSION['book']['tourist_tax'], 2, '.', '');
        $data['total'] = number_format($total, 2, '.', '');
        if($payment_type != 'arrival') $data['down_payment'] = number_format($_SESSION['book']['down_payment'], 2, '.', '');
        $data['add_date'] = time();
        $data['edit_date'] = null;
        $data['status'] = 1;
        $data['discount'] = number_format($_SESSION['book']['discount_amount'], 2, ".", "");
		$data['payment_option'] = $payment_type;
		$data['id_coupon'] = (isset($_SESSION['book']['id_coupon'])) ? $_SESSION['book']['id_coupon'] : null;
        $data['users'] = $users;
        
		$tax_amount = $_SESSION['book']['tax_rooms_amount']+$_SESSION['book']['tax_activities_amount']+$_SESSION['book']['tax_services_amount'];
        $data['tax_amount'] = number_format($tax_amount, 2, '.', '');
        $data['ex_tax'] = number_format($total-$tax_amount, 2, '.', '');
        
        $result_booking = db_prepareInsert($db, 'pm_booking', $data);
        if($result_booking->execute() !== false){
			
            $_SESSION['book']['id'] = $db->lastInsertId();

			if(isset($_SESSION['book']['sessid']))
				$db->query('DELETE FROM pm_room_lock WHERE sessid = '.$db->quote($_SESSION['book']['sessid']));
            
            if(isset($_SESSION['book']['rooms']) && count($_SESSION['book']['rooms']) > 0){
                foreach($_SESSION['book']['rooms'] as $id_room => $rooms){
                    foreach($rooms as $index => $room){
                        $data = array();
                        $data['id'] = null;
                        $data['id_booking'] = $_SESSION['book']['id'];
                        $data['id_room'] = $id_room;
                        $data['id_hotel'] = $_SESSION['book']['hotel_id'];
                        $data['title'] = $_SESSION['book']['hotel'].' - '.$room['title'];
                        $data['adults'] = $room['adults'];
                        $data['children'] = $room['children'];
                        $data['amount'] = number_format($room['amount'], 2, '.', '');
                        if(isset($room['duty_free'])) $data['ex_tax'] = number_format($room['duty_free'], 2, '.', '');
                        if(isset($room['tax_rate'])) $data['tax_rate'] = $room['tax_rate'];
                        
                        $result = db_prepareInsert($db, 'pm_booking_room', $data);
                        $result->execute();
                    }
                }
            }
            if(isset($_SESSION['book']['activities']) && count($_SESSION['book']['activities']) > 0){
                foreach($_SESSION['book']['activities'] as $id_activity => $activity){
                    $data = array();
                    $data['id'] = null;
                    $data['id_booking'] = $_SESSION['book']['id'];
                    $data['id_activity'] = $id_activity;
                    $data['title'] = $activity['title'];
                    $data['adults'] = $activity['adults'];
                    $data['children'] = $activity['children'];
                    $data['duration'] = $activity['duration'];
                    $data['amount'] = number_format($activity['amount'], 2, '.', '');
                    $data['date'] = $activity['session_date'];
					if(isset($activity['duty_free'])) $data['ex_tax'] = number_format($activity['duty_free'], 2, '.', '');
					if(isset($activity['tax_rate'])) $data['tax_rate'] = $activity['tax_rate'];
                    
                    $result = db_prepareInsert($db, 'pm_booking_activity', $data);
                    $result->execute();
                }
            }
            if(isset($_SESSION['book']['extra_services']) && count($_SESSION['book']['extra_services']) > 0){
                foreach($_SESSION['book']['extra_services'] as $id_service => $service){
                    $data = array();
                    $data['id'] = null;
                    $data['id_booking'] = $_SESSION['book']['id'];
                    $data['id_service'] = $id_service;
                    $data['title'] = $service['title'];
                    $data['qty'] = $service['qty'];
                    $data['amount'] = number_format($service['amount'], 2, '.', '');
					if(isset($service['duty_free'])) $data['ex_tax'] = number_format($service['duty_free'], 2, '.', '');
					if(isset($service['tax_rate'])) $data['tax_rate'] = $service['tax_rate'];
                    
                    $result = db_prepareInsert($db, 'pm_booking_service', $data);
                    $result->execute();
                }
            }
            if(isset($_SESSION['book']['taxes']) && count($_SESSION['book']['taxes']) > 0){
                $tax_id = 0;
                $result_tax = $db->prepare('SELECT * FROM pm_tax WHERE id = :tax_id AND checked = 1 AND value > 0 AND lang = '.LANG_ID.' ORDER BY rank');
                $result_tax->bindParam(':tax_id', $tax_id);
                foreach($_SESSION['book']['taxes'] as $tax_id => $taxes){
                    $tax_amount = 0;
                    foreach($taxes as $amount) $tax_amount += $amount;
                    if($tax_amount > 0){
                        if($result_tax->execute() !== false && $db->last_row_count() > 0){
                            $row = $result_tax->fetch();
                            $data = array();
                            $data['id'] = null;
                            $data['id_booking'] = $_SESSION['book']['id'];
                            $data['id_tax'] = $tax_id;
                            $data['name'] = $row['name'];
                            $data['amount'] = number_format($tax_amount, 2, '.', '');
                            
                            $result = db_prepareInsert($db, 'pm_booking_tax', $data);
                            $result->execute();
                        }
                    }
                }
            }
            $_SESSION['tmp_book'] = $_SESSION['book'];
        }
    }
        
    if(isset($_SESSION['book']['id']) && $_SESSION['book']['id'] > 0){
        $data = array();
        $data['id'] = $_SESSION['book']['id'];
		$data['payment_option'] = $payment_type;
        
        $result_booking = db_prepareUpdate($db, 'pm_booking', $data);
        $result_booking->execute();
    }
        
    if($payment_type == 'check' || $payment_type == 'arrival'){
        
        $room_content = '';
        if(isset($_SESSION['book']['rooms']) && count($_SESSION['book']['rooms']) > 0){
            foreach($_SESSION['book']['rooms'] as $id_room => $rooms){
                foreach($rooms as $index => $room){
                    $room_content .= '<p><b>'.$_SESSION['book']['hotel'].' - '.$room['title'].'</b><br>
                    '.($room['adults']+$room['children']).' '.getAltText($texts['PERSON'], $texts['PERSONS'], ($room['adults']+$room['children'])).': ';
                    if($room['adults'] > 0) $room_content .= $room['adults'].' '.getAltText($texts['ADULT'], $texts['ADULTS'], $room['adults']).' ';
                    if($room['children'] > 0){
                        $room_content .= $room['children'].' '.getAltText($texts['CHILD'], $texts['CHILDREN'], $room['children']).' ';
                        if(isset($room['child_age'])){
                            $room_content .= '('.implode(' '.$texts['YO'].', ', $room['child_age']).' '.$texts['YO'].')';
                        }
                    }
                    $room_content .= '<br>'.$texts['PRICE'].' : '.formatPrice($room['amount']*CURRENCY_RATE).'</p>';
                }
            }
        }
        
        $service_content = '';
        if(isset($_SESSION['book']['extra_services']) && count($_SESSION['book']['extra_services']) > 0){
            foreach($_SESSION['book']['extra_services'] as $id_service => $service)
                $service_content .= $service['title'].' x '.$service['qty'].' : '.formatPrice($service['amount']*CURRENCY_RATE).' '.$texts['INCL_VAT'].'<br>';
        }
        
        $activity_content = '';
        if(isset($_SESSION['book']['activities']) && count($_SESSION['book']['activities']) > 0){
            foreach($_SESSION['book']['activities'] as $id_activity => $activity){
                $activity_content .= '<p><b>'.$activity['title'].'</b> - '.$activity['duration'].' - '.gmstrftime(DATE_FORMAT.' '.TIME_FORMAT, $activity['session_date']).'<br>
                '.($activity['adults']+$activity['children']).' '.getAltText($texts['PERSON'], $texts['PERSONS'], ($activity['adults']+$activity['children'])).': ';
                if($activity['adults'] > 0) $activity_content .= $activity['adults'].' '.getAltText($texts['ADULT'], $texts['ADULTS'], $activity['adults']).' ';
                if($activity['children'] > 0) $activity_content .= $activity['children'].' '.getAltText($texts['CHILD'], $texts['CHILDREN'], $activity['children']).' ';
                $activity_content .= $texts['PRICE'].' : '.formatPrice($activity['amount']*CURRENCY_RATE).'</p>';
            }
        }
        
        $tax_id = 0;
        $tax_content = '';
        $result_tax = $db->prepare('SELECT * FROM pm_tax WHERE id = :tax_id AND checked = 1 AND value > 0 AND lang = '.LANG_ID.' ORDER BY rank');
        $result_tax->bindParam(':tax_id', $tax_id);
        foreach($_SESSION['book']['taxes'] as $tax_id => $taxes){
            $tax_amount = 0;
            foreach($taxes as $amount) $tax_amount += $amount;
            if($tax_amount > 0){
                if($result_tax->execute() !== false && $db->last_row_count() > 0){
                    $row = $result_tax->fetch();
                    $tax_content .= $row['name'].': '.formatPrice($tax_amount*CURRENCY_RATE).'<br>';
                }
            }
        }
        
        $payment_notice = '';
        if($payment_type == 'check') $payment_notice .= str_replace('{amount}', '<b>'.formatPrice($payed_amount*CURRENCY_RATE).' '.$texts['INCL_VAT'].'</b>', $texts['PAYMENT_CHECK_NOTICE']);
        if($payment_type == 'arrival') $payment_notice .= str_replace('{amount}', '<b>'.formatPrice($total).' '.$texts['INCL_VAT'].'</b>', $texts['PAYMENT_ARRIVAL_NOTICE']);
        
        $mail = getMail($db, 'BOOKING_CONFIRMATION', array(
            '{firstname}' => $_SESSION['book']['firstname'],
            '{lastname}' => $_SESSION['book']['lastname'],
            '{company}' => $_SESSION['book']['company'],
            '{address}' => $_SESSION['book']['address'],
            '{postcode}' => $_SESSION['book']['postcode'],
            '{city}' => $_SESSION['book']['city'],
            '{country}' => $_SESSION['book']['country'],
            '{phone}' => $_SESSION['book']['phone'],
            '{mobile}' => $_SESSION['book']['mobile'],
            '{email}' => $_SESSION['book']['email'],
            '{Check_in}' => isset($_SESSION['book']['from_date']) ? gmstrftime(DATE_FORMAT, $_SESSION['book']['from_date']) : '-',
            '{Check_out}' => isset($_SESSION['book']['from_date']) ? gmstrftime(DATE_FORMAT, $_SESSION['book']['to_date']) : '-',
            '{num_nights}' => isset($_SESSION['book']['nights']) ? $_SESSION['book']['nights'] : '-',
            '{num_guests}' => (isset($_SESSION['book']['adults']) || isset($_SESSION['book']['children'])) ? ($_SESSION['book']['adults']+$_SESSION['book']['children']) : '-',
            '{num_adults}' => isset($_SESSION['book']['adults']) ? $_SESSION['book']['adults'] : '-',
            '{num_children}' => isset($_SESSION['book']['children']) ? $_SESSION['book']['children'] : '-',
            '{rooms}' => $room_content,
            '{extra_services}' => $service_content,
            '{activities}' => $activity_content,
            '{comments}' => nl2br($_SESSION['book']['comments']),
            //'{tourist_tax}' => formatPrice($_SESSION['book']['tourist_tax']*CURRENCY_RATE),
            '{discount}' => '- '.formatPrice($_SESSION['book']['discount_amount']*CURRENCY_RATE),
            '{taxes}' => $tax_content,
            '{down_payment}' => formatPrice($_SESSION['book']['down_payment']*CURRENCY_RATE),
            '{total}' => formatPrice($total*CURRENCY_RATE),
            '{payment_notice}' => $payment_notice
        ));
        
        if($mail !== false){
            foreach($hotel_owners as $owner){
                if($owner['email'] != EMAIL)
                    sendMail($owner['email'], $owner['firstname'], $mail['subject'], $mail['content'], $_SESSION['book']['email'], $_SESSION['book']['firstname'].' '.$_SESSION['book']['lastname']);
            }
            sendMail(EMAIL, OWNER, $mail['subject'], $mail['content'], $_SESSION['book']['email'], $_SESSION['book']['firstname'].' '.$_SESSION['book']['lastname']);
            sendMail($_SESSION['book']['email'], $_SESSION['book']['firstname'].' '.$_SESSION['book']['lastname'], $mail['subject'], $mail['content']);
        }
        unset($_SESSION['book']);
    }
}

/* ==============================================
 * CSS AND JAVASCRIPT USED IN THIS MODEL
 * ==============================================
 */
if($payment_type == 'cards')
    $javascripts[] = 'https://www.2checkout.com/static/checkout/javascript/direct.min.js';

require(getFromTemplate('common/header.php', false)); ?>

<section id="page">
    
    <?php include(getFromTemplate('common/page_header.php', false)); ?>
    
    <div id="content" class="pt30 pb30">
        <div class="container">

            <div class="alert alert-success" style="display:none;"></div>
            <div class="alert alert-danger" style="display:none;"></div>
            
            <div class="row mb30" id="booking-breadcrumb">
                <div class="col-sm-2 col-sm-offset-<?php echo (isset($_SESSION['tmp_book']['activities']) || isset($_SESSION['book']['activities'])) ? '1' : '2'; ?>">
                    <a href="<?php echo DOCBASE.$sys_pages['booking']['alias']; ?>">
                        <div class="breadcrumb-item done">
                            <i class="fas fa-fw fa-calendar"></i>
                            <span><?php echo $sys_pages['booking']['name']; ?></span>
                        </div>
                    </a>
                </div>
                <?php
                if(isset($_SESSION['tmp_book']['activities']) || isset($_SESSION['book']['activities'])){ ?>
                    <div class="col-sm-2">
                        <a href="<?php echo DOCBASE.$sys_pages['booking-activities']['alias']; ?>">
                            <div class="breadcrumb-item done">
                                <i class="fas fa-fw fa-ticket-alt"></i>
                                <span><?php echo $sys_pages['booking-activities']['name']; ?></span>
                            </div>
                        </a>
                    </div>
                    <?php
                } ?>
                <div class="col-sm-2">
                    <a href="<?php echo DOCBASE.$sys_pages['details']['alias']; ?>">
                        <div class="breadcrumb-item done">
                            <i class="fas fa-fw fa-info-circle"></i>
                            <span><?php echo $sys_pages['details']['name']; ?></span>
                        </div>
                    </a>
                </div>
                <div class="col-sm-2">
                    <a href="<?php echo DOCBASE.$sys_pages['summary']['alias']; ?>">
                        <div class="breadcrumb-item done">
                            <i class="fas fa-fw fa-list"></i>
                            <span><?php echo $sys_pages['summary']['name']; ?></span>
                        </div>
                    </a>
                </div>
                <div class="col-sm-2">
                    <div class="breadcrumb-item active">
                        <i class="fas fa-fw fa-credit-card"></i>
                        <span><?php echo $sys_pages['payment']['name']; ?></span>
                    </div>
                </div>
            </div>
            
            <?php echo $page['text']; ?>
            
            <?php
            if($payment_type == 'paypal'){ ?>
                <div class="text-center">
                    <?php echo $texts['PAYMENT_PAYPAL_NOTICE']; ?><br>
                    <img src="<?php echo getFromTemplate('images/paypal-cards.png'); ?>" alt="PayPal" class="img-responsive mt10 mb30">
                    <form action="https://www.<?php if(PAYMENT_TEST_MODE == 1) echo 'sandbox.'; ?>paypal.com/cgi-bin/webscr" method="post">
                        <input type='hidden' value="<?php echo number_format(str_replace(',', '.', $payed_amount), 2, '.', ''); ?>" name="amount">
                        <input name="currency_code" type="hidden" value="<?php echo DEFAULT_CURRENCY_CODE; ?>">
                        <input name="shipping" type="hidden" value="0.00">
                        <input name="tax" type="hidden" value="0.00">
                        <input name="return" type="hidden" value="<?php echo getUrl(true).DOCBASE.$sys_pages['booking']['alias'].'?action=confirm'; ?>">
                        <input name="cancel_return" type="hidden" value="<?php echo getUrl(true).DOCBASE.$sys_pages['booking']['alias'].'?action=cancel'; ?>">
                        <input name="notify_url" type="hidden" value="<?php echo getUrl(true).DOCBASE.'includes/payments/paypal_notify.php'; ?>">
                        <input name="cmd" type="hidden" value="_xclick">
                        <input name="business" type="hidden" value="<?php echo (ENABLE_MULTI_VENDORS == 1 && $paypal_email != '') ? $paypal_email : PAYPAL_EMAIL; ?>">
                        <input name="item_name" type="hidden" value="<?php echo addslashes($_SESSION['tmp_book']['hotel'].' - '.gmstrftime(DATE_FORMAT, $_SESSION['tmp_book']['from_date']).' > '.gmstrftime(DATE_FORMAT, $_SESSION['tmp_book']['to_date']).' - '.$_SESSION['tmp_book']['nights'].' '.$texts['NIGHTS'].' - '.($_SESSION['tmp_book']['adults']+$_SESSION['tmp_book']['children']).' '.$texts['PERSONS']); ?>">
                        <input name="no_note" type="hidden" value="1">
                        <input name="lc" type="hidden" value="<?php echo strtoupper(LANG_TAG); ?>">
                        <input name="bn" type="hidden" value="PP-BuyNowBF">
                        <input name="custom" type="hidden" value="<?php echo $_SESSION['tmp_book']['id']; ?>">
                        
                        <button type="submit" name="submit" class="btn btn-primary btn-lg"><i class="fab fa-fw fa-paypal"></i> <?php echo $texts['PAY']; ?></button>
                    </form>
                </div>
                <?php
            }elseif($payment_type == '2checkout'){ ?>
                <div class="text-center">
                    <?php echo $texts['PAYMENT_2CHECKOUT_NOTICE']; ?><br>
                    <img src="<?php echo getFromTemplate('images/2checkout-cards.png'); ?>" alt="2Checkout.com" class="img-responsive mt10 mb30">
                    <form action="https://<?php if(PAYMENT_TEST_MODE == 1) echo 'sandbox'; else echo 'www'; ?>.2checkout.com/checkout/purchase" method="post">
                        <input type="hidden" name="sid" value="<?php echo VENDOR_ID; ?>">
                        <input type="hidden" name="currency_code" value="<?php echo DEFAULT_CURRENCY_CODE; ?>">
                        <input type="hidden" name="lang" value="<?php echo LANG_TAG; ?>">
                        <input type="hidden" name="mode" value="2CO">
                        <input type="hidden" name="merchant_order_id" value="<?php echo $_SESSION['tmp_book']['id']; ?>">
                        <input type="hidden" name="li_0_type" value="product">
                        <input type="hidden" name="li_0_name" value="<?php echo addslashes($_SESSION['tmp_book']['hotel'].' - '.gmstrftime(DATE_FORMAT, $_SESSION['tmp_book']['from_date']).' > '.gmstrftime(DATE_FORMAT, $_SESSION['tmp_book']['to_date']).' - '.$_SESSION['tmp_book']['nights'].' '.$texts['NIGHTS'].' - '.($_SESSION['tmp_book']['adults']+$_SESSION['tmp_book']['children']).' '.$texts['PERSONS']); ?>">
                        <input type="hidden" name="li_0_price" value="<?php echo number_format(str_replace(',', '.', $payed_amount), 2, '.', ''); ?>">
                        <input type="hidden" name="card_holder_name" value="<?php echo $_SESSION['book']['firstname'].' '.$_SESSION['book']['lastname']; ?>">
                        <input type="hidden" name="street_address" value="<?php echo $_SESSION['book']['address']; ?>">
                        <input type="hidden" name="street_address2" value="">
                        <input type="hidden" name="city" value="<?php echo $_SESSION['book']['city']; ?>">
                        <input type="hidden" name="state" value="">
                        <input type="hidden" name="zip" value="<?php echo $_SESSION['book']['postcode']; ?>">
                        <input type="hidden" name="country" value="<?php echo $_SESSION['book']['country']; ?>">
                        <input type="hidden" name="email" value="<?php echo $_SESSION['book']['email']; ?>">
                        <input type="hidden" name="phone" value="<?php echo $_SESSION['book']['phone']; ?>">
                        <input type="hidden" name="x_receipt_link_url" value="<?php echo getUrl(true).DOCBASE.'includes/payments/2checkout_notify.php'; ?>">
                        
                        <button type="submit" name="submit" class="btn btn-primary btn-lg"><i class="fas fa-fw fa-credit-card"></i> <?php echo $texts['PAY']; ?></button>
                    </form>
                </div>
                <?php
            }elseif($payment_type == 'braintree'){ ?>
                <div class="text-center">
                    <?php echo $texts['PAYMENT_BRAINTREE_NOTICE']; ?><br>
                    <img src="<?php echo getFromTemplate('images/braintree-cards.jpg'); ?>" alt="Braintree" class="img-responsive mt10 mb30">
                    <form action="<?php echo DOCBASE; ?>includes/payments/braintree/checkout.php" method="post">
						<div id="dropin"></div>
						<input type="hidden" name="amount" value="<?php echo number_format(str_replace(',', '.', $payed_amount), 2, '.', ''); ?>">
						<input type="hidden" name="id_booking" value="<?php echo $_SESSION['tmp_book']['id']; ?>">
						<button type="submit" class="btn btn-primary btn-lg" id="braintree_btn" style="display: none;"><i class="fas fa-fw fa-credit-card"></i> <?php echo $texts['PAY']; ?></button>
					</form>
                </div>
                <?php
            }elseif($payment_type == 'razorpay'){ ?>
                <div class="text-center">
                    <?php echo $texts['PAYMENT_RAZORPAY_NOTICE']; ?><br>
                    <img src="<?php echo getFromTemplate('images/razorpay-cards.jpg'); ?>" alt="Razorpay" class="img-responsive mt10 mb30">
                    <form action="<?php echo DOCBASE; ?>includes/payments/razorpay_notify.php" method="post">
						<script
							src="https://checkout.razorpay.com/v1/checkout.js"
							data-key="<?php echo RAZORPAY_KEY_ID; ?>"
							data-amount="<?php echo round($payed_amount*100, 0); ?>"
							data-currency="INR"
							data-buttontext="<?php echo $texts['PAY']; ?>"
							data-name="<?php echo SITE_TITLE; ?>"
							data-description=""
							data-image="<?php echo getUrl(true).getFromTemplate('images/logo.png'); ?>"
							data-prefill.name="<?php echo $_SESSION['book']['firstname'].' '.$_SESSION['book']['lastname']; ?>"
							data-prefill.email="<?php echo $_SESSION['book']['email']; ?>">
						</script>
						<input type="hidden" name="order_id" value="<?php echo $_SESSION['tmp_book']['id']; ?>">
						<input type="hidden" name="amount" value="<?php echo number_format(str_replace(',', '.', $payed_amount), 2, '.', ''); ?>">
					</form>
                </div>
                <?php
            }else{ ?>
            
                <div class="text-center lead pt20 pb20">

                    <form method="post" action="<?php echo DOCBASE.$sys_pages['payment']['alias']; ?>">
                        <?php
                        if(!isset($_POST['payment_type'])){
                            $payments = array_map('trim', explode(',', PAYMENT_TYPE));
                            if(count($payments) > 1){ ?>
                                <div class="mb10">
                                    <?php echo $texts['CHOOSE_PAYMENT']; ?>
                                </div>
                                <?php
                                foreach($payments as $payment){ ?>
                                    <button type="submit" name="payment_type" class="btn btn-default" value="<?php echo $payment; ?>">
                                        <?php
                                        switch($payment){
                                            case 'razorpay': ?>
                                                <i class="fas fa-fw fa-credit-card"></i><br>Razorpay
                                                <?php
                                            break;
                                            case '2checkout': ?>
                                                <i class="fas fa-fw fa-credit-card"></i><br>2Checkout.com
                                                <?php
                                            break;
                                            case 'braintree': ?>
                                                <i class="fas fa-fw fa-credit-card"></i><br>Braintree
                                                <?php
                                            break;
                                            case 'paypal': ?>
                                                <i class="fab fa-fw fa-paypal"></i><br>PayPal
                                                <?php
                                            break;
                                            case 'check': ?>
                                                <i class="fas fa-fw fa-envelope"></i><br><?php echo $texts['PAYMENT_CHECK']; ?>
                                                <?php
                                            break;
                                            case 'arrival': ?>
                                                <i class="fas fa-fw fa-building"></i><br><?php echo $texts['PAYMENT_ARRIVAL']; ?>
                                                <?php
                                            break;
                                        } ?>
                                    </button>
                                    <?php
                                }
                            }
                        }else{ ?>
                            <input type="hidden" name="payment_type" value="<?php echo $payment_type; ?>">
                            <?php
                        } ?>
                    </form>
                    
                    <?php
                    if($payment_type == 'check') echo str_replace('{amount}', '<b>'.formatPrice($payed_amount, DEFAULT_CURRENCY_SIGN).' '.$texts['INCL_VAT'].'</b>', $texts['PAYMENT_CHECK_NOTICE']);
                    
                    if($payment_type == 'arrival') echo str_replace('{amount}', '<b>'.formatPrice($payed_amount*CURRENCY_RATE).' '.$texts['INCL_VAT'].'</b>', $texts['PAYMENT_ARRIVAL_NOTICE']); ?>
                </div>
                    
                <div class="clearfix"></div>
                <a class="btn btn-default btn-lg pull-left" href="<?php echo DOCBASE.$sys_pages['summary']['alias']; ?>"><i class="fas fa-fw fa-angle-left"></i> <?php echo $texts['PREVIOUS_STEP']; ?></a>
                
                <?php
            } ?>
        </div>
    </div>
</section>
<?php
if($payment_type == 'braintree'){ ?>
	<script src="https://js.braintreegateway.com/v2/braintree.js"></script>
	<script>
		$(function() {
			$.ajax({
				dataType: 'text',
				type: 'POST',
				data:  { action: 'generateclienttoken' },
				url: '<?php echo DOCBASE; ?>includes/payments/braintree/checkout.php',
				success: function (req) {
					braintree.setup(
						req,
						'dropin', {
							container: 'dropin',
							onReady:function(){
								$('#braintree_btn').show();
							},
							onError: function(error) {
							}
					});
				},
				error: function() {
				}
			});
		});
	</script>
	<?php
} ?>
