<?php
require_once('includes/general.php');
header("Content-Type:text/html; charset=utf-8");

not_login_redirect();

$dst = $_POST['request']['dstaddr'];

$sql = "UPDATE `user_info` SET `ui_phone` = '".$dst."' WHERE `user_info`.`u_id` = '".$_SESSION['u_id']."';";
$db->query($sql);

$vercode = user_vercode(true);

$msg = '你的驗證碼是:'.$vercode['hash'] ;

//echo $msg;

send_sms($dst, $msg);

?>