<?php

require_once('includes/general.php');
//header("Content-Type:text/html; charset=utf-8");

global $db;

$_PAGE_TITLE = '物料管理';
require_once('includes/header.php');

// redirect if it's not top_admin || top_staff || bottom_admin
if (!(is_admin() || is_headquarters_staff())) {
	header("location:login.php?shop_id=" . $_shopID);
	die('');
}


$shop_id = $_SESSION['shop_id'];
if ($shop_id == -1) {
	/***
		總店
	***/
	/***
		handled = 0 -> 分店叫料，總店尚未處理
		handled = 1 -> 分店叫料，總店已處理
		handled = 2 -> 分店叫料，總店已處理，且分店已看過
	***/
	// read data from ingredient table
	$sql = "SELECT `shop_id`, `ingredient`, `num`, `unit`, `call_time`
			FROM `ingredient`
			WHERE `handled` = 0
			ORDER BY `call_time`";

	$result = $db->query($sql);

	$template = $twig->loadTemplate('top_inventory.html');

	$_HTML .= $template->render(array(
		'data' => $result,
	));
}
else {
	/***
		分店
	***/
	/***
		handled = 0 -> 分店叫料，總店尚未處理
		handled = 1 -> 分店叫料，總店已處理
		handled = 2 -> 分店叫料，總店已處理，且分店已看過
	***/
	// read data from ingredient table
	$sql = "SELECT `ingredient`, `num`, `unit`, `call_time`, `handled`
			FROM `ingredient`
			WHERE `handled` != 2 AND `shop_id` = '".$shop_id."'
			ORDER BY `call_time`";

	$result = $db->query($sql);

	$template = $twig->loadTemplate('inventory.html');

	$_HTML .= $template->render(array(
		'data' => $result,
	));
}


require_once('includes/footer.php');

?>
