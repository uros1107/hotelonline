<?php
/**
 * Script called (Ajax) on login
 */
require_once('../../../../common/lib.php');
require_once('../../../../common/define.php');

$user_id = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 0;

if(isset($_GET['token']) && isset($_GET['id']) && is_numeric($_GET['id'])){
    $id = $_GET['id'];
    $result_token = $db->query('SELECT * FROM pm_user WHERE token = '.$db->quote(htmlentities($_GET['token'], ENT_COMPAT, 'UTF-8')).' AND id = '.$id.' AND (checked = 0 OR checked IS NULL)');
    if($result_token !== false && $db->last_row_count() > 0){
        if($db->query('UPDATE pm_user SET checked = 1, token = \'\' WHERE id = '.$id) !== false){
            
            $row = $result_token->fetch();
            
            $_SESSION['user']['id'] = $id;
            $_SESSION['user']['login'] = $row['login'];
            $_SESSION['user']['email'] = $row['email'];
            $_SESSION['user']['type'] = $row['type'];
        }
    }
    if(isset($_GET['redirect']) && $_GET['redirect'] != '')
        header('Location: '.$_GET['redirect']);
    else
        header('Location: '.DOCBASE.LANG_ALIAS);
        
    exit();
}else{
    $response = array('html' => '', 'notices' => array(), 'error' => '', 'success' => '', 'redirect' => '');

    if($user_id > 0) $action = 'edit';
    else $action = 'add';
    
    if(isset($_POST['signup_type'])) $signup_type = $_POST['signup_type'];
    else $signup_type = 'complete';
    
    $user_type = (isset($_POST['hotel_owner']) && $_POST['hotel_owner'] == 1) ? 'hotel' : 'registered';
    
    if(isset($_POST['signup_redirect'])) $signup_redirect = ($user_type == 'hotel') ? getUrl(true).DOCBASE.ADMIN_FOLDER : $_POST['signup_redirect'];
    else $signup_redirect = '';
    
    $login = htmlentities($_POST['username'], ENT_COMPAT, 'UTF-8');
    $email = htmlentities($_POST['email'], ENT_COMPAT, 'UTF-8');
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if($login == '') $response['notices']['username'] = $texts['REQUIRED_FIELD'];
    if(($action == 'edit' && $password != '') || $user_id == 0){
        if($password_confirm != $password) $response['notices']['password_confirm'] = $texts['PASS_DONT_MATCH'];
        if(strlen($password) < 6) $response['notices']['password'] = $texts['PASS_TOO_SHORT'];
    }
    if($email == '' || !preg_match('/^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$/i', $email)) $response['notices']['email'] = $texts['INVALID_EMAIL'];

    $result_exists = $db->query('SELECT * FROM pm_user WHERE id != '.$db->quote($user_id).' AND (email = '.$db->quote($email).' OR login = '.$db->quote($login).')');
    if($result_exists !== false && $db->last_row_count() > 0){
        $row = $result_exists->fetch();
        if($email == $row['email']) $response['notices']['email'] = $texts['ACCOUNT_EXISTS'];
        if($login == $row['login']) $response['notices']['username'] = $texts['USERNAME_EXISTS'];
    }
    
    if($signup_type != 'quick'){
        $firstname = htmlentities($_POST['firstname'], ENT_COMPAT, 'UTF-8');
        $lastname = htmlentities($_POST['lastname'], ENT_COMPAT, 'UTF-8');
        $address = htmlentities($_POST['address'], ENT_COMPAT, 'UTF-8');
        $postcode = htmlentities($_POST['postcode'], ENT_COMPAT, 'UTF-8');
        $city = htmlentities($_POST['city'], ENT_COMPAT, 'UTF-8');
        $company = htmlentities($_POST['company'], ENT_COMPAT, 'UTF-8');
        $country = htmlentities($_POST['country'], ENT_COMPAT, 'UTF-8');
        $mobile = htmlentities($_POST['mobile'], ENT_COMPAT, 'UTF-8');
        $phone = htmlentities($_POST['phone'], ENT_COMPAT, 'UTF-8');
        $privacy_agreement = isset($_POST['privacy_agreement']) ? true : false;

        if(!$privacy_agreement) $response['notices']['privacy_agreement'] = $texts['REQUIRED_FIELD'];
        if($firstname == '') $response['notices']['firstname'] = $texts['REQUIRED_FIELD'];
        if($lastname == '') $response['notices']['lastname'] = $texts['REQUIRED_FIELD'];
        if($address == '') $response['notices']['address'] = $texts['REQUIRED_FIELD'];
        if($postcode == '') $response['notices']['postcode'] = $texts['REQUIRED_FIELD'];
        if($city == '') $response['notices']['city'] = $texts['REQUIRED_FIELD'];
        if($country == '' || $country == '0') $response['notices']['country'] = $texts['REQUIRED_FIELD'];
        if($phone == '' || preg_match('/([0-9\-\s\+\(\)\.]+)/i', $phone) !== 1) $response['notices']['phone'] = $texts['REQUIRED_FIELD'];
        
        $name = $firstname.' '.$lastname;
    }else{
        $firstname = '';
        $lastname = '';
        $address = '';
        $postcode = '';
        $city = '';
        $company = '';
        $country = '';
        $mobile = '';
        $phone = '';
        
        $name = $login;
    }
            
    if(count($response['notices']) == 0){

        $token = md5(uniqid($email, true));
    
        $data = array();
        $data['id'] = ($user_id > 0) ? $user_id : null;
        $data['firstname'] = $firstname;
        $data['lastname'] = $lastname;
        $data['login'] = $login;
        $data['email'] = $email;
        $data['address'] = $address;
        $data['postcode'] = $postcode;
        $data['city'] = $city;
        $data['company'] = $company;
        $data['country'] = $country;
        $data['mobile'] = $mobile;
        $data['phone'] = $phone;
        if($password != '') $data['pass'] = md5($password);
        if($action == 'edit') $data['edit_date'] = time();
        else{
            $data['add_date'] = time();
            $data['token'] = $token;
            $data['checked'] = 0;
            $data['type'] = $user_type;
        }

        if($action == 'edit')
            $result_user = db_prepareUpdate($db, 'pm_user', $data);
        else
            $result_user = db_prepareInsert($db, 'pm_user', $data);
            
        if($result_user->execute() !== false){
            
            if($action == 'edit'){
                $_SESSION['user']['login'] = $login;
                $_SESSION['user']['email'] = $email;
                $response['success'] = $texts['ACCOUNT_EDIT_SUCCESS'];
            }else{
				
				$mail = getMail($db, 'ACCOUNT_CONFIRMATION', array(
					'{link}' => getUrl().'?token='.$token.'&id='.$db->lastInsertId().'&redirect='.urlencode($signup_redirect)
				));
				
                if($mail !== false && sendMail($email, $name, $mail['subject'], $mail['content']) !== false)
                    $response['success'] = $texts['ACCOUNT_CREATED'];
                else
                    $response['error'] = $texts['ACCOUNT_CREATE_FAILURE'];
            }
        }else
            $response['error'] = ($user_id > 0) ? $texts['ACCOUNT_EDIT_FAILURE'] : $texts['ACCOUNT_CREATE_FAILURE'];
    }else
        $response['error'] = $texts['FORM_ERRORS'];

    echo json_encode($response);
}
