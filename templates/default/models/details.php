<?php
if(!isset($_SESSION['book']) || count($_SESSION['book']) == 0){
    header('Location: '.DOCBASE.$sys_pages['booking']['alias']);
    exit();
}else
    $_SESSION['book']['step'] = 'details';

$msg_error = '';
$msg_success = '';
$field_notice = array();

$user_id = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 0;

$result_user = $db->query('SELECT * FROM pm_user WHERE id = '.$db->quote($user_id).' AND checked = 1');
if($result_user !== false && $db->last_row_count() > 0){
    $row = $result_user->fetch();
    
    $firstname = $row['firstname'];
    $lastname = $row['lastname'];
    $login = $row['login'];
    $email = $row['email'];
    $address = $row['address'];
    $postcode = $row['postcode'];
    $city = $row['city'];
    $company = $row['company'];
    $country = $row['country'];
    $mobile = $row['mobile'];
    $phone = $row['phone'];
}elseif(isset($_SESSION['book']['firstname'])){
    $firstname = $_SESSION['book']['firstname'];
    $lastname = $_SESSION['book']['lastname'];
    $email = $_SESSION['book']['email'];
    $address = $_SESSION['book']['address'];
    $postcode = $_SESSION['book']['postcode'];
    $city = $_SESSION['book']['city'];
    $company = $_SESSION['book']['company'];
    $country = $_SESSION['book']['country'];
    $mobile = $_SESSION['book']['mobile'];
    $phone = $_SESSION['book']['phone'];
    $login = '';
}else{
    $firstname = '';
    $lastname = '';
    $login = '';
    $email = '';
    $address = '';
    $postcode = '';
    $city = '';
    $company = '';
    $country = '';
    $mobile = '';
    $phone = '';
}

$id = 0;
$privacy_agreement = false;
$comments = '';

if(isset($_POST['book']) || (ENABLE_BOOKING_REQUESTS == 1 && isset($_POST['request']))){
    
    $firstname = htmlentities($_POST['firstname'], ENT_COMPAT, 'UTF-8');
    $lastname = htmlentities($_POST['lastname'], ENT_COMPAT, 'UTF-8');
    $address = htmlentities($_POST['address'], ENT_COMPAT, 'UTF-8');
    $postcode = htmlentities($_POST['postcode'], ENT_COMPAT, 'UTF-8');
    $city = htmlentities($_POST['city'], ENT_COMPAT, 'UTF-8');
    $company = htmlentities($_POST['company'], ENT_COMPAT, 'UTF-8');
    $country = htmlentities($_POST['country'], ENT_COMPAT, 'UTF-8');
    $mobile = htmlentities($_POST['mobile'], ENT_COMPAT, 'UTF-8');
    $phone = htmlentities($_POST['phone'], ENT_COMPAT, 'UTF-8');
    $comments = htmlentities($_POST['comments'], ENT_COMPAT, 'UTF-8');
    $email = htmlentities($_POST['email'], ENT_COMPAT, 'UTF-8');
    $privacy_agreement = isset($_POST['privacy_agreement']) ? true : false;

    if(!$privacy_agreement) $field_notice['privacy_agreement'] = $texts['REQUIRED_FIELD'];
    if($firstname == '') $field_notice['firstname'] = $texts['REQUIRED_FIELD'];
    if($lastname == '') $field_notice['lastname'] = $texts['REQUIRED_FIELD'];
    if($address == '') $field_notice['address'] = $texts['REQUIRED_FIELD'];
    if($postcode == '') $field_notice['postcode'] = $texts['REQUIRED_FIELD'];
    if($city == '') $field_notice['city'] = $texts['REQUIRED_FIELD'];
    if($country == '' || $country == '0') $field_notice['country'] = $texts['REQUIRED_FIELD'];
    if($phone == '' || preg_match('/([0-9\-\s\+\(\)\.]+)/i', $phone) !== 1) $field_notice['phone'] = $texts['REQUIRED_FIELD'];
    if($email == '' || preg_match('/^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$/i', $email) !== 1) $field_notice['email'] = $texts['INVALID_EMAIL'];

    if(!empty($user_id)){
        $result_exists = $db->query('SELECT * FROM pm_user WHERE id != '.$db->quote($user_id).' AND email = '.$db->quote($email));
        if($result_exists !== false && $db->last_row_count() > 0)
            $field_notice['email'] = $texts['ACCOUNT_EXISTS'];
    }
            
    if(count($field_notice) == 0){
    
        if(!empty($user_id)){
            $data = array();
            $data['id'] = $user_id;
            $data['firstname'] = $firstname;
            $data['lastname'] = $lastname;
            $data['email'] = $email;
            $data['address'] = $address;
            $data['postcode'] = $postcode;
            $data['city'] = $city;
            $data['company'] = $company;
            $data['country'] = $country;
            $data['mobile'] = $mobile;
            $data['phone'] = $phone;
            $data['edit_date'] = time();

            $result_user = db_prepareUpdate($db, 'pm_user', $data);
            if($result_user->execute() !== false){
                if(isset($_SESSION['book']['id'])) unset($_SESSION['book']['id']);
            }else
                $msg_error .= $texts['ACCOUNT_EDIT_FAILURE'];
        }
    
        $_SESSION['book']['id_user'] = $user_id;
        $_SESSION['book']['firstname'] = $firstname;
        $_SESSION['book']['lastname'] = $lastname;
        $_SESSION['book']['email'] = $email;
        $_SESSION['book']['company'] = $company;
        $_SESSION['book']['address'] = $address;
        $_SESSION['book']['postcode'] = $postcode;
        $_SESSION['book']['city'] = $city;
        $_SESSION['book']['phone'] = $phone;
        $_SESSION['book']['mobile'] = $mobile;
        $_SESSION['book']['country'] = $country;
        $_SESSION['book']['comments'] = $comments;
        
        if(isset($_SESSION['book']['id'])) unset($_SESSION['book']['id']);
    
        if(isset($_POST['book'])){
            header('Location: '.DOCBASE.$sys_pages['summary']['alias']);
            exit();
        }elseif(ENABLE_BOOKING_REQUESTS == 1 && isset($_POST['request'])){
            
            $room_content = '';
            if(isset($_SESSION['book']['rooms']) && count($_SESSION['book']['rooms']) > 0){
                foreach($_SESSION['book']['rooms'] as $id_room => $rooms){
                    foreach($rooms as $index => $room){
                        $room_content .= '<p><b>'.$_SESSION['book']['hotel'].' - '.$room['title'].'</b><br>
                        '.($room['adults']+$room['children']).' '.$texts['PERSONS'].' - 
                        '.$texts['ADULTS'].': '.$room['adults'].' / 
                        '.$texts['CHILDREN'].': '.$room['children'].'<br>
                        '.$texts['PRICE'].' : '.formatPrice($room['amount']*CURRENCY_RATE).'</p>';
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
                    '.($activity['adults']+$activity['children']).' '.$texts['PERSONS'].' - 
                    '.$texts['ADULTS'].': '.$activity['adults'].' / 
                    '.$texts['CHILDREN'].': '.$activity['children'].'<br>
                    '.$texts['PRICE'].' : '.formatPrice($activity['amount']*CURRENCY_RATE).'</p>';
                }
            }
            
            $mail = getMail($db, 'BOOKING_REQUEST', array(
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
                '{Check_in}' => gmstrftime(DATE_FORMAT, $_SESSION['book']['from_date']),
                '{Check_out}' => gmstrftime(DATE_FORMAT, $_SESSION['book']['to_date']),
                '{num_nights}' => $_SESSION['book']['nights'],
                '{num_guests}' => ($_SESSION['book']['adults']+$_SESSION['book']['children']),
                '{num_adults}' => $_SESSION['book']['adults'],
                '{num_children}' => $_SESSION['book']['children'],
                '{rooms}' => $room_content,
                '{extra_services}' => $service_content,
                '{activities}' => $activity_content,
                '{comments}' => nl2br($_SESSION['book']['comments'])
            ));
            
            if($mail !== false){
                $users = '';
                $result_owner = $db->query('SELECT users FROM pm_hotel WHERE id = '.$_SESSION['book']['hotel_id']);
                if($result_owner !== false && $db->last_row_count() > 0){
                    $row = $result_owner->fetch();
                    $users = $row['users'];
                }
                $hotel_owners = array();
                $result_owner = $db->query('SELECT * FROM pm_user WHERE id IN ('.$users.')');
                if($result_owner !== false){
                    foreach($result_owner as $owner){
                        if($owner['email'] != EMAIL)
                            sendMail($owner['email'], $owner['firstname'], $mail['subject'], $mail['content'], $_SESSION['book']['email'], $_SESSION['book']['firstname'].' '.$_SESSION['book']['lastname']);
                    }
                }
                sendMail(EMAIL, OWNER, $mail['subject'], $mail['content'], $_SESSION['book']['email'], $_SESSION['book']['firstname'].' '.$_SESSION['book']['lastname']);
                
                $msg_success .= $texts['MAIL_DELIVERY_SUCCESS'];
                $lastname = '';
                $firstname = '';
                $email = '';
                $address = '';
                $postcode = '';
                $city = '';
                $company = '';
                $country = '';
                $mobile = '';
                $phone = '';
                $comments = '';
                $privacy_agreement = false;
            }else
                $msg_error .= $texts['MAIL_DELIVERY_FAILURE'];
        }
    }else
        $msg_error .= $texts['FORM_ERRORS'];
}

require(getFromTemplate('common/header.php', false)); ?>

<section id="page">
    
    <?php include(getFromTemplate('common/page_header.php', false)); ?>
    
    <div id="content" class="pt30 pb30">
        <div class="container">
            
            <div class="row mb30" id="booking-breadcrumb">
                <div class="col-sm-2 col-sm-offset-<?php echo isset($_SESSION['book']['activities']) ? '1' : '2'; ?>">
                    <a href="<?php echo DOCBASE.$sys_pages['booking']['alias']; ?>">
                        <div class="breadcrumb-item done">
                            <i class="fas fa-fw fa-calendar"></i>
                            <span><?php echo $sys_pages['booking']['name']; ?></span>
                        </div>
                    </a>
                </div>
                <?php
                if(isset($_SESSION['book']['activities'])){ ?>
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
                    <div class="breadcrumb-item active">
                        <i class="fas fa-fw fa-info-circle"></i>
                        <span><?php echo $sys_pages['details']['name']; ?></span>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="breadcrumb-item">
                        <i class="fas fa-fw fa-list"></i>
                        <span><?php echo $sys_pages['summary']['name']; ?></span>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="breadcrumb-item">
                        <i class="fas fa-fw fa-credit-card"></i>
                        <span><?php echo $sys_pages['payment']['name']; ?></span>
                    </div>
                </div>
            </div>
            
            <?php
            if($page['text'] != ""){ ?>
                <div class="clearfix mb20"><?php echo $page['text']; ?></div>
                <?php
            } ?>
            
            <form method="post" action="<?php echo DOCBASE.$page['alias']; ?>" role="form" class="ajax-form">
                
                <div class="alert alert-success" style="display:none;"></div>
                <div class="alert alert-danger" style="display:none;"></div>
            
                <div class="row">
                    <div class="col-md-6">
                        <?php
                        if($user_id == 0){ ?>
                            <fieldset>
                                <legend><?php echo $texts['ALREADY_HAVE_ACCOUNT']; ?></legend>
                                <div class="row form-group">
                                    <label class="col-lg-3 control-label"><?php echo $texts['USERNAME']; ?></label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" name="user" value="<?php echo $login; ?>"/>
                                        <div class="field-notice" rel="user"></div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label class="col-lg-3 control-label"><?php echo $texts['PASSWORD']; ?></label>
                                    <div class="col-lg-9">
                                        <input type="password" class="form-control" name="pass"/>
                                        <div class="field-notice" rel="pass" value=""></div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-7 col-lg-4 col-lg-offset-3 text-left">
                                        <a class="popup-modal open-pass-form" href="#user-popup"><?php echo $texts['FORGOTTEN_PASSWORD']; ?></a>
                                    </div>
                                    <div class="col-sm-5 text-right">
                                        <a href="#" class="btn btn-primary sendAjaxForm" data-action="<?php echo getFromTemplate('common/register/login.php'); ?>" data-refresh="true"><i class="fas fa-fw fa-power-off"></i> <?php echo $texts['LOG_IN']; ?></a>
                                    </div>
                                </div>
                            </fieldset>
                            <?php
                        } ?>
                        <fieldset>
                            <legend><?php echo ($user_id == 0) ? $texts['CONTACT_DETAILS'] : $texts['MY_ACCOUNT']; ?></legend>
                            <div class="row form-group">
                                <label class="col-lg-3 control-label"><?php echo $texts['FIRSTNAME']; ?> *</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="firstname" value="<?php echo $firstname; ?>"/>
                                    <div class="field-notice" rel="firstname"></div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="col-lg-3 control-label"><?php echo $texts['LASTNAME']; ?> *</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="lastname" value="<?php echo $lastname; ?>"/>
                                    <div class="field-notice" rel="lastname"></div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="col-lg-3 control-label"><?php echo $texts['EMAIL']; ?> *</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="email" value="<?php echo $email; ?>"/>
                                    <div class="field-notice" rel="email"></div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="col-lg-3 control-label"><?php echo $texts['ADDRESS']; ?> *</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="address" value="<?php echo $address; ?>"/>
                                    <div class="field-notice" rel="address"></div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="col-lg-3 control-label"><?php echo $texts['POSTCODE']; ?> *</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="postcode" value="<?php echo $postcode; ?>"/>
                                    <div class="field-notice" rel="postcode"></div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="col-lg-3 control-label"><?php echo $texts['CITY']; ?> *</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="city" value="<?php echo $city; ?>"/>
                                    <div class="field-notice" rel="city"></div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="col-lg-3 control-label"><?php echo $texts['COUNTRY']; ?> *</label>
                                <div class="col-lg-9">
                                    <select class="form-control" name="country">
                                        <option value="0">-</option>
                                        <?php
                                        $result_country = $db->query('SELECT * FROM pm_country');
                                        if($result_country !== false){
                                            foreach($result_country as $i => $row){
                                                $id_country = $row['id'];
                                                $country_name = $row['name'];
                                                $selected = ($country == $country_name) ? ' selected="selected"' : '';
                                                
                                                echo '<option value="'.$country_name.'"'.$selected.'>'.$country_name.'</option>';
                                            }
                                        } ?>
                                    </select>
                                    <div class="field-notice" rel="country"></div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="col-lg-3 control-label"><?php echo $texts['PHONE']; ?> *</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="phone" value="<?php echo $phone; ?>"/>
                                    <div class="field-notice" rel="phone"></div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="col-lg-3 control-label"><?php echo $texts['MOBILE']; ?></label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="mobile" value="<?php echo $mobile; ?>"/>
                                    <div class="field-notice" rel="mobile"></div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="col-lg-3 control-label"><?php echo $texts['COMPANY']; ?></label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="company" value="<?php echo $company; ?>"/>
                                    <div class="field-notice" rel="company"></div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="col-lg-3"></label>
                                <div class="col-lg-9">
                                    <input type="checkbox" name="privacy_agreement" value="1"<?php if($privacy_agreement) echo ' checked="checked"'; ?>> <?php echo $texts['PRIVACY_POLICY_AGREEMENT']; ?>
                                    <div class="field-notice" rel="privacy_agreement"></div>
                                </div>
                            </div>
                            <?php
                            if($user_id == 0){ ?>
                                <div class="form-group row">
                                    <div class="col-sm-12 text-right">
                                        <i class="text-muted"> * <?php echo $texts['REQUIRED_FIELD']; ?> </i><br>
                                        <button class="btn btn-primary" name="<?php echo isset($_SESSION['book']['amount_rooms']) ? 'book': 'request'; ?>"><?php echo $texts['CONTINUE_AS_GUEST']; ?></button>
                                    </div>
                                </div>
                                <?php
                            } ?>
                        </fieldset>
                        <?php
                        if($user_id == 0){ ?>
                            <fieldset>
                                <legend><?php echo $texts['I_SIGN_UP']; ?></legend>
                                <div class="row form-group">
                                    <label class="col-lg-3 control-label"><?php echo $texts['USERNAME']; ?></label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" name="username" value="<?php echo $login; ?>"/>
                                        <div class="field-notice" rel="username"></div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label class="col-lg-3 control-label"><?php echo ($user_id > 0) ? $texts['NEW_PASSWORD'] : $texts['PASSWORD']; ?></label>
                                    <div class="col-lg-9">
                                        <input type="password" class="form-control" name="password" value=""/>
                                        <div class="field-notice" rel="password"></div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label class="col-lg-3 control-label"><?php echo $texts['PASSWORD_CONFIRM']; ?></label>
                                    <div class="col-lg-9">
                                        <input type="password" class="form-control" name="password_confirm" value=""/>
                                        <div class="field-notice" rel="password_confirm"></div>
                                    </div>
                                </div>
                                <input type="hidden" name="signup_redirect" value="<?php echo getUrl(); ?>">
                                <div class="form-group row">
                                    <div class="col-sm-12 text-right">
                                        <a href="#" class="btn btn-primary sendAjaxForm" data-action="<?php echo getFromTemplate('common/register/signup.php'); ?>"><i class="fas fa-fw fa-power-off"></i> <?php echo $texts['SIGN_UP']; ?></a>
                                    </div>
                                </div>
                            </fieldset>
                            <?php
                        } ?>
                    </div>
                    <div class="col-md-6">
                        <?php
                        if(isset($_SESSION['book']['rooms']) && count($_SESSION['book']['rooms']) > 0){ ?>
							<fieldset class="mb20">
								<legend><?php echo $texts['BOOKING_DETAILS']; ?></legend>
								<div class="ctaBox">
									<div class="row">
										<div class="col-md-6">
											<p>
												<?php
												echo $texts['CHECK_IN'].' <strong>'.gmstrftime(DATE_FORMAT, $_SESSION['book']['from_date']).'</strong><br>
												'.$texts['CHECK_OUT'].' <strong>'.gmstrftime(DATE_FORMAT, $_SESSION['book']['to_date']).'</strong><br>
												<strong>'.$_SESSION['book']['nights'].'</strong> '.$texts['NIGHTS'].' -
												<strong>'.($_SESSION['book']['adults']+$_SESSION['book']['children']).'</strong> '.$texts['PERSONS']; ?>
											</p>
										</div>
									</div>
								</div>
							</fieldset>
                            <fieldset class="mb20">
                                <legend><?php echo ucfirst($texts['ROOMS']); ?></legend>
                                <?php
                                foreach($_SESSION['book']['rooms'] as $id_room => $rooms){
                                    foreach($rooms as $index => $room){ ?>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p>
                                                    <?php
                                                    echo '<strong>'.$_SESSION['book']['hotel'].' - '.$room['title'].'</strong><br>
                                                    '.($room['adults']+$room['children']).' '.$texts['PERSONS'].' - 
                                                    '.$texts['ADULTS'].': '.$room['adults'].' / 
                                                    '.$texts['CHILDREN'].': '.$room['children']; ?>
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <span class="pull-right">
                                                    <?php echo formatPrice($room['amount']*CURRENCY_RATE); ?><br/>
                                                </span>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                } ?>
                            </fieldset>
                            <?php
                        }
                        if(isset($_SESSION['book']['activities']) && count($_SESSION['book']['activities']) > 0){ ?>
                            <fieldset class="mb20">
                                <legend><?php echo $texts['ACTIVITIES']; ?></legend>
                                <?php
                                foreach($_SESSION['book']['activities'] as $id_activity => $activity){ ?>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p>
                                                <?php
                                                echo '<strong>'.$activity['title'].'</strong> - '.$activity['duration'].'<br>
                                                <strong>'.gmstrftime(DATE_FORMAT.' '.TIME_FORMAT, $activity['session_date']).'</strong> -
                                                <strong>'.($activity['adults']+$activity['children']).'</strong> '.$texts['PERSONS']; ?>
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <span class="pull-right">
                                                <?php echo formatPrice($activity['amount']*CURRENCY_RATE); ?><br/>
                                            </span>
                                        </div>
                                    </div>
                                    <?php
                                } ?>
                            </fieldset>
                            <?php
                        }
                        $rooms_ids = array_keys($_SESSION['book']['rooms']);
                        $query_service = 'SELECT * FROM pm_service WHERE';
                        if(isset($_SESSION['book']['amount_rooms'])) $query_service .= ' rooms REGEXP \'[[:<:]]'.implode('|', $rooms_ids).'[[:>:]]\' AND';
                        $query_service .= ' lang = '.LANG_ID.' AND checked = 1 ORDER BY rank';
                        $result_service = $db->query($query_service);
                        if($result_service !== false && $db->last_row_count() > 0){ ?>
                            <fieldset class="mb20">
                                <legend><?php echo $texts['EXTRA_SERVICES']; ?></legend>
                                <?php
                                foreach($result_service as $i => $row){
                                    $id_service = $row['id'];
                                    $service_title = $row['title'];
                                    $service_descr = $row['descr'];
                                    $service_long_descr = $row['long_descr'];
                                    $service_price = $row['price'];
                                    $service_type = $row['type'];
                                    $service_rooms = explode(',', $row['rooms']);
                                    $mandatory = $row['mandatory'];
                                    
                                    $nb_rooms = count(array_intersect($service_rooms, $rooms_ids));
                                    if($nb_rooms == 0) $nb_rooms = 1;

                                    $service_qty = 1;
                                    if($service_type == 'person') $service_qty = $_SESSION['book']['adults']+$_SESSION['book']['children'];
                                    if($service_type == 'adult') $service_qty = $_SESSION['book']['adults'];
                                    if($service_type == 'child') $service_qty = $_SESSION['book']['children'];
                                    if($service_type == 'person-night' || $service_type == 'qty-person-night') $service_qty = ($_SESSION['book']['adults']+$_SESSION['book']['children'])*$_SESSION['book']['nights'];
                                    if($service_type == 'adult-night' || $service_type == 'qty-adult-night') $service_qty = $_SESSION['book']['adults']*$_SESSION['book']['nights'];
                                    if($service_type == 'child-night' || $service_type == 'qty-child-night') $service_qty = $_SESSION['book']['children']*$_SESSION['book']['nights'];
                                    if($service_type == 'qty-night' || $service_type == 'night') $service_qty = $_SESSION['book']['nights'];
                                    if($service_type == 'night') $service_qty = $nb_rooms;
                                    
                                    $service_price *= $service_qty;

                                    $service_selected = array_key_exists($id_service, $_SESSION['book']['extra_services']);
                                    
                                    if($mandatory == 1 && !$service_selected) $service_selected = true;

                                    $checked = $service_selected ? ' checked="checked"' : ''; ?>

                                    <div class="row form-group">
                                        <label class="col-sm-<?php echo (strpos($service_type, 'qty') !== false) ? 7 : 10; ?> col-xs-9">
                                            <input type="checkbox" name="extra_services[]" value="<?php echo $id_service; ?>" class="sendAjaxForm"<?php if($mandatory) echo ' disabled="disabled" data-sendOnload="1"'; ?> data-action="<?php echo getFromTemplate('common/update_booking.php'); ?>" data-target="#total_booking"<?php echo $checked;?>>
                                            <?php
                                            if($mandatory){ ?>
                                                <input type="hidden" name="extra_services[]" value="<?php echo $id_service; ?>">
                                                <?php
                                            }
                                            echo $service_title;
                                            if($service_descr != ''){ ?>
                                                <br><small><?php echo $service_descr; ?></small>
                                                <?php
                                            }
                                            if($service_long_descr != ''){ ?>
                                                <br><small><a href="#service_<?php echo $id_service; ?>" class="popup-modal"><?php echo $texts['READMORE']; ?></a></small>
                                                <div id="service_<?php echo $id_service; ?>" class="white-popup-block mfp-hide">
                                                    <?php echo $service_long_descr; ?>
                                                </div>
                                                <?php
                                            } ?>
                                        </label>
                                        <?php
                                        if(strpos($service_type, 'qty') !== false){
                                            $qty = isset($_SESSION['book']['extra_services'][$id_service]['qty']) ? $_SESSION['book']['extra_services'][$id_service]['qty'] : 1; ?>
                                            <div class="col-sm-3 col-xs-9">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-btn">
                                                        <button class="btn btn-default btn-number" data-field="qty_service_<?php echo $id_service; ?>" data-type="minus" disabled="disabled" type="button">
                                                            <i class="fas fa-fw fa-minus"></i>
                                                        </button>
                                                    </span>
                                                    <input class="form-control input-number sendAjaxForm" type="text" max="20" min="1" value="<?php echo $qty; ?>" name="qty_service_<?php echo $id_service; ?>" data-action="<?php echo getFromTemplate('common/update_booking.php'); ?>" data-target="#total_booking">
                                                    <span class="input-group-btn">
                                                        <button class="btn btn-default btn-number" data-field="qty_service_<?php echo $id_service; ?>" data-type="plus" type="button">
                                                            <i class="fas fa-fw fa-plus"></i>
                                                        </button>
                                                    </span>
                                                </div>
                                            </div>
                                            <?php
                                        } ?>
                                        <div class="col-sm-2 col-xs-3 text-right">
                                            <?php
                                            if(strpos($service_type, 'qty') !== false) echo 'x ';
                                            echo formatPrice($service_price*CURRENCY_RATE); ?>
                                        </div>
                                    </div>
                                    <?php
                                } ?>
                            </fieldset>
                            <?php
                        }
                        if(isset($_SESSION['book']['amount_rooms'])){ ?>
                            <fieldset class="mb20">
                                <legend><?php echo $texts['DO_YOU_HAVE_A_COUPON']; ?></legend>
                                <div class="form-group form-inline">
                                    <input class="form-control" type="text" value="" name="coupon_code">
                                    <a href="#" class="btn btn-primary sendAjaxForm" data-action="<?php echo getFromTemplate('common/update_booking.php'); ?>" data-target="#total_booking"><i class="fas fa-fw fa-check"></i></a>
                                </div>
                            </fieldset>
                            <hr>
                            <?php
                            /*if(ENABLE_TOURIST_TAX == 1 && isset($_SESSION['book']['tourist_tax'])){ ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <p>
                                            <strong><?php echo $texts['TOURIST_TAX']; ?></strong>
                                            <span class="pull-right"><?php echo formatPrice($_SESSION['book']['tourist_tax']*CURRENCY_RATE); ?></span>
                                        </p>
                                    </div>
                                </div>
                                <?php
                            }*/ ?>
                            <div id="total_booking" class="mb15">
                                <?php
                                if(isset($_SESSION['book']['discount_amount']) && $_SESSION['book']['discount_amount'] > 0){ ?>
                                    <div class="row">
                                        <div class="col-xs-6 lead"><?php echo $texts['DISCOUNT']; ?></div>
                                        <div class="col-xs-6 lead text-right"><?php echo '- '.formatPrice($_SESSION['book']['discount_amount']*CURRENCY_RATE); ?></div>
                                    </div>
                                    <?php
                                } ?>
                                <div class="row">
                                    <div class="col-xs-6">
                                        <h3>
                                            <?php
                                            echo $texts['TOTAL'].' <small>('.$texts['INCL_TAX'].')</small>'; ?>
                                        </h3>
                                    </div>
                                    <div class="col-xs-6 lead text-right">
                                        <?php echo formatPrice($_SESSION['book']['total']*CURRENCY_RATE); ?>
                                    </div>
                                </div>
                                <?php
                                $tax_id = 0;
                                $result_tax = $db->prepare('SELECT * FROM pm_tax WHERE id = :tax_id AND checked = 1 AND value > 0 AND lang = '.LANG_ID.' ORDER BY rank');
                                $result_tax->bindParam(':tax_id', $tax_id);
                                foreach($_SESSION['book']['taxes'] as $tax_id => $taxes){
                                    $tax_amount = 0;
                                    foreach($taxes as $amount) $tax_amount += $amount;
                                    if($tax_amount > 0){
                                        if($result_tax->execute() !== false && $db->last_row_count() > 0){
                                            $row = $result_tax->fetch(); ?>
                                            <div class="row">
                                                <div class="col-xs-6">
                                                    <?php echo $row['name']; ?>
                                                </div>
                                                <div class="col-xs-6 text-right">
                                                    <?php echo formatPrice($tax_amount*CURRENCY_RATE); ?>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    }
                                } ?>
                            </div>
                            <?php
                        } ?>
                        <fieldset>
                            <legend><?php echo $texts['SPECIAL_REQUESTS']; ?></legend>
                            <div class="form-group">
                                <textarea class="form-control" name="comments" rows="5"><?php echo $comments; ?></textarea>
                                <div class="field-notice" rel="comments"></div>
                            </div>
                            <p><?php //echo $texts['BOOKING_TERMS']; ?></p>
                        </fieldset>
                    </div>
                </div>
                
                <a class="btn btn-default btn-lg pull-left" href="<?php echo (isset($_SESSION['book']['activities'])) ? DOCBASE.$sys_pages['booking-activities']['alias'] : DOCBASE.$sys_pages['booking']['alias']; ?>"><i class="fas fa-fw fa-angle-left"></i> <?php echo $texts['PREVIOUS_STEP']; ?></a>
                <?php
                if(isset($_SESSION['book']['amount_rooms']) || isset($_SESSION['book']['amount_activities'])){ ?>
                    <button type="submit" class="btn btn-primary btn-lg pull-right" name="book"><?php echo $texts['NEXT_STEP']; ?> <i class="fas fa-fw fa-angle-right"></i></button>
                    <?php
                }else{ ?>
                    <button type="submit" class="btn btn-primary btn-lg pull-right" name="request"><i class="fas fa-fw fa-paper-plane"></i> <?php echo $texts['MAKE_A_REQUEST']; ?></button>
                    <?php
                } ?>
            </form>
        </div>
    </div>
</section>
