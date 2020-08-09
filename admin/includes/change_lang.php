<?php

$_SESSION['CHANGE_LANG'] = 'en.ini';
if(isset($_POST['check_id']))
{
    if($_POST['check_id'] == 1)
    {
        session_start();
        $_SESSION['CHANGE_LANG'] = 'es.ini';
    }else{
        session_start();
        $_SESSION['CHANGE_LANG'] = 'en.ini';
    }
   
}

// $admin_lang_file = SYSBASE.ADMIN_FOLDER.'/includes/langs/'.$_SESSION['CHANGE_LANG'];
// if(ADMIN && is_file($admin_lang_file)){
//     $texts = @parse_ini_file($admin_lang_file);
// if(is_null($texts))
//     $texts = @parse_ini_string(file_get_contents($admin_lang_file));
// }  
// require_once('../../common/define.php'); 
echo $_SESSION['CHANGE_LANG'];

?>