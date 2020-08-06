<?php
define("SYSBASE",realpath(__DIR__."/../../../")."/");
require_once(SYSBASE."common/lib.php");
require_once(SYSBASE."common/define.php");
    
if(isset($db) && $db !== false){
    
    if(isset($_GET['curr']) && is_numeric($_GET['curr']) > 0){

        $curr_id = $_GET['curr'];
        $rate = '';

        $result_currency = $db->query('SELECT * FROM pm_currency WHERE id = '.$curr_id);
        if($result_currency !== false && $db->last_row_count() > 0){
            $row = $result_currency->fetch();
            $code = $row['code'];
            $sign = $row['sign'];

            if($code != DEFAULT_CURRENCY_CODE && ($handle = fopen('https://api.fixer.io/latest?base='.DEFAULT_CURRENCY_CODE.'&symbols='.$code, 'r')) !== false){
                if(($data = fgets($handle)) !== false){
                    $data = json_decode($data);
                    $rates = $data->rates;
                    $rate = $rates->{$code};
                }
            }
            if(is_numeric($rate)){
                $_SESSION['currency']['rate'] = $rate;
                $_SESSION['currency']['code'] = $code;
                $_SESSION['currency']['sign'] = $sign;
            }
        }
    }
}
