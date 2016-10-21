<?php

$_PAGE_TITLE = '早餐店後台管理系統';
require_once('includes/header.php');

not_login_redirect();
if(!checkAuth(AUCUSTOMER|AUSTAFF|AUADMIN)){
		header("location:register.php?shop_id=" . $_shopID);
		die('');
}

$template = $twig->loadTemplate('customer_listorder.html');

/*
$sql = "SELECT * FROM `orders` ORDER BY `orders`.`o_time` DESC where `orders`.`status` = WAIT";
$result = $db->query($sql);
$num = $db->numrow($result);
$all_orders = array();
*/

/*	$sql = "SELECT * FROM `config` WHERE `name` = 'verification'";
	$Qver = $db->query_select_one($sql);

	$sql = "SELECT * FROM `config` WHERE `name` = 'verification_time'";
	$Qver_t = $db->query_select_one($sql);

	if( $Qver_t['value']  < time() ){
		$sql = "UPDATE `config` SET `value` = '".(time()+30)."' WHERE `config`.`name` = 'verification_time'";
		$db->query($sql);

		$new_hash = substr( hash("sha256", $Qver['value']) , 0, 4);

		$sql = "UPDATE `config` SET `value` = '".$new_hash."' WHERE `config`.`name` = 'verification' ";
		$db->query($sql);

		$Qver['value'] = $new_hash;
	}
*/

$_HTML .= $template->render(array(
	//'verification_code' => $Qver['value'],
));

require_once('includes/footer.php');
?>
