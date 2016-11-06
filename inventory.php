<?php

require_once('includes/general.php');
//header("Content-Type:text/html; charset=utf-8");

global $db;

$_PAGE_TITLE = '物料管理';
require_once('includes/header.php');

not_admin_redirect();

// read data from ingredient table 
$sql = "SELECT `shop_id`, `ingredient`, `num`, `unit`, `call_time`
		FROM `ingredient` 
		WHERE `handled` = 0
		ORDER BY `call_time`";

$result = $db->query($sql);

$template = $twig->loadTemplate('inventory.html');

$_HTML .= $template->render(array(
	'data' => $result,
));


require_once('includes/footer.php');

?>
