<?php
define('ADMIN', true);
require_once('../common/lib.php');
require_once('../common/define.php');
define('TITLE_ELEMENT', $texts['DASHBOARD'].' - '.$texts['LOGIN']);

$action = (isset($_GET['action'])) ? $_GET['action'] : '';

if($action == 'logout' && isset($_SESSION['user'])) unset($_SESSION['user']);

if(isset($_SESSION['user'])){
    if($_SESSION['user']['type'] != 'registered'){
        header('Location: index.php');
        exit();
    }else
        unset($_SESSION['user']);
}

if($db !== false && isset($_POST['login'])){
    $user = htmlentities($_POST['user'], ENT_COMPAT, 'UTF-8');
    $password = $_POST['password'];
    
    if(check_token('/'.ADMIN_FOLDER.'/login.php', 'login', 'post')){
        
        $result_user = $db->query('SELECT * FROM pm_user WHERE login = '.$db->quote($user).' AND pass = '.$db->quote(md5($password)).' AND checked = 1');
        if($result_user !== false && $db->last_row_count() > 0){
            $row = $result_user->fetch();
            $_SESSION['user']['id'] = $row['id'];
            $_SESSION['user']['login'] = $user;
            $_SESSION['user']['email'] = $row['email'];
            $_SESSION['user']['type'] = $row['type'];
            $_SESSION['user']['add_date'] = $row['add_date'];
            header('Location: index.php');
            exit();
        }else
            $_SESSION['msg_error'][] = $texts['LOGIN_FAILED'];
    }else
        $_SESSION['msg_error'][] = $texts['BAD_TOKEN2'];
}

if($db !== false && isset($_POST['reset'])){
    
    if(defined('DEMO') && DEMO == 1)
        $_SESSION['msg_error'][] = 'This action is disabled in the demo mode';
    else{
        $email = htmlentities($_POST['email'], ENT_COMPAT, 'UTF-8');

        if(check_token('/'.ADMIN_FOLDER.'/login.php', 'login', 'post')){

            $result_user = $db->query('SELECT * FROM pm_user WHERE email = '.$db->quote($email).' AND checked = 1');
            if($result_user !== false && $db->last_row_count() > 0){
                $row = $result_user->fetch();
                $url = getUrl();
                $new_pass = genPass(6);
                $mailContent = '
                <p>Hi,<br>You requested a new password from <a href=\"'.$url.'\" target=\"_blank\">'.$url.'</a><br>
                Bellow, your new connection informations<br>
                Username: '.$row['login'].'<br>
                Password: <b>'.$new_pass.'</b><br>
                You can modify this random password in the settings via the manager.</p>';
                if(sendMail($email, $row['name'], 'Your new password', $mailContent) !== false)
                    $db->query('UPDATE pm_user SET pass = '.$db->quote(md5($new_pass)).' WHERE id = '.$row['id']);
            }
            $_SESSION['msg_success'][] = 'A new password has been sent to your e-mail.';
        }else
            $_SESSION['msg_error'][] = 'Bad token! Thank you for re-trying by clicking on "New password".';
    }
}

$csrf_token = get_token('login'); ?>
<!DOCTYPE html>
<head>
    <?php include('includes/inc_header_common.php'); ?>
</head>
<body class="white">
    <div class="container">
        <form id="form" class="form-horizontal" role="form" action="login.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <div class="col-sm-3 col-md-4"></div>
            <div class="col-sm-6 col-md-4" id="loginWrapper">
                <div id="logo">
                    <img src="images/logo-admin.png">
                </div>
                <div id="login">
                    <div class="alert-container">
                        <div class="alert alert-success alert-dismissable"></div>
                        <div class="alert alert-warning alert-dismissable"></div>
                        <div class="alert alert-danger alert-dismissable"></div>
                    </div>
                    <?php
                    if($action == 'reset'){ ?>                        
                        <div class="row mb10">
                            <label class="col-sm-3 text-center" style="margin-top: 10px">
                                E-mail:
                            </label>
                            <div class="col-sm-9">
                                <input class="form-control" type="text" value="" name="email">
                            </div>
                        </div>
                        <div class="row mb10">
                            <div class="col-xs-3 text-left">
                                <a href="login.php"><i class="fas fa-fw fa-power-off"></i> Login</a>
                            </div>
                            <div class="col-xs-9 text-right">
                                <button class="btn btn-default" type="submit" value="" name="reset"><i class="fas fa-fw fa-sync"></i> Get New password</button>
                            </div>
                        </div>
                        <?php
                    }else{
						if(defined('DEMO') && DEMO == 1) echo '<div class="alert alert-info text-center">DEMO &nbsp;&nbsp; <i class="fa fa-fw fa-user"></i> <i>admin</i>&nbsp; | &nbsp;<i class="fa fa-fw fa-lock"></i> <i>admin123</i></div>'; ?>                        
                        <div class="row mb10">
                            <div class="col-sm-12 text-left">
                                <h4 style="font-weight: bold; margin-left: 5px">Log In</h4>
                            </div>
                        </div>
                        <div class="row mb10">
                            <label class="col-sm-3 text-left" style="margin-top: 6px">
                                <?php echo ($texts['USERNAME'] . ":"); ?>
                            </label>
                            <div class="col-sm-9 text-left">
                                <input class="form-control" type="text" value="" name="user">
                            </div>
                        </div>                        
                        <div class="row mb20">
                            <label class="col-sm-3 text-left" style="margin-top: 6px"> 
                                <?php echo ($texts['PASSWORD'] . ":"); ?>
                            </label>
                            <div class="col-sm-9 text-left">
                                <input class="form-control" type="password" value="" name="password">
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-sm-6 col-6 text-left">
                                <label>
                                    <input type="checkbox" {{ old('remember') ? 'checked' : '' }}> Remember Me
                                </label>
                            </div>                    
                            <div class="col-sm-6 col-6 text-right">
                                <a href="login.php?action=reset">Forgot password&nbsp;?</a>
                                
                            </div>
                        </div>
                        <!-- <div class="row mb15"><a class="open-signup-form" href="#"><?php echo $texts['I_SIGN_UP'];?></a></div> -->
                        <div class="row mb10">                            
                            <div class="col-sm-12 text-right" style="padding-right: 25px">
                                <button class="btn btn-default" type="submit" value="" name="login" style="width: 50%"><i class="fas fa-fw fa-power-off"></i> <?php echo $texts['LOGIN']; ?></button>
                            </div>
                        </div>
                        <div class="row" style="padding: 10px 10px 3px 10px;">
                            <div class="col-sm-5" style="border-bottom: 1px solid; padding: 9px"></div>
                            <div class="col-sm-2 text-center">
                                <h5 style="font-weight: bold;">OR</h5>
                            </div>
                            <div class="col-sm-5" style="border-bottom: 1px solid; padding: 9px"></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 text-left" style="margin-left: 5px">
                                <h5 style="font-weight: bold;">Login with:</h5>
                            </div>
                        </div>
                        <div class="row mb10" style="padding: 10px">
                            <div class="col-sm-4" style="padding: 5px">
                                <a href="#" class="btn btn-primary btn-block"><i class="fab fa-facebook-f" style="font-size: 17px"></i>&nbsp;<b>Facebook</b></a>
                            </div>
                            <div class="col-sm-4" style="padding: 5px">
                                <a href="#" class="btn btn-info btn-block"><i class="fab fa-twitter" style="font-size: 17px"></i>&nbsp;<b>Twitter</b></a>
                            </div>
                            <div class="col-sm-4" style="padding: 5px">
                                <a href="#" class="btn btn-danger btn-block"><i class="fab fa-google" style="font-size: 17px"></i>&nbsp;<b>Google</b></a>
                            </div>
                        </div>
                        <?php
                    } ?>
                </div>
            </div>
            <div class="col-sm-3 col-md-4"></div>
        </form>
    </div>
</body>
</html>
<?php
$_SESSION['msg_error'] = array();
$_SESSION['msg_success'] = array();
$_SESSION['msg_notice'] = array(); ?>
