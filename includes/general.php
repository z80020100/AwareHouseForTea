<?php

/* include libraries **********************************************/

require_once ("dbclass.php");
require_once ("db_config.php");
require_once ("general_functions.php");

// Mustache template system
//require 'includes/Mustache/Autoloader.php';
//Mustache_Autoloader::register();

require_once dirname(__FILE__).'/Twig/lib/Twig/Autoloader.php';
Twig_Autoloader::register(true);
$loader = new Twig_Loader_Filesystem('./templates/');
$twig = new Twig_Environment($loader, array(
    //'cache' => '/templates/cache/',
));

/******************************************************************/


session_start();
header("Content-Type:text/html; charset=utf-8");

if(!isset($_SESSION['u_auth']))
	$_SESSION['u_auth'] = AUGUEST;

$_shopID = get_shopID();
if ($_shopID == NULL)
	die("Not provide shop_id");
// TODO injection check


// 未來要獨立成為設定檔的部分 -----------------------
// 可能會變成資料庫存取

$db = new Db(DB_ADDRESS, DB_USER, DB_PASSWORD, DB_DATABASE);

// 開放訂餐的時間 從 4點 ~ 14點  (13-14是一個時段)
$shift_start =0;
$shift_end = 24;

$_AWMode = "BUSINESS"; // ACCOUNTING, BUSINESS


$_DontSendSMS = true;

//$_Auth = array();
//$_Auth['index.php'] = AUGUEST| AUCUSTOMER | AUSTAFF | AUADMIN;
//$_Auth['listorder.php'] = AUSTAFF | AUADMIN;

//echo basename($_SERVER["SCRIPT_FILENAME"]);
//echo $_Auth[basename($_SERVER["SCRIPT_FILENAME"])];


// 未來要獨立成為設定檔的部分 -----------------



?>
