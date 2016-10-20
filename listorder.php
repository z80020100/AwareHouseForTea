<?php

// require_once('includes/general.php');
// header("Content-Type:text/html; charset=utf-8");

$_PAGE_TITLE = '即時訂單';
require_once('includes/header.php');

not_login_redirect();
not_staff_redirect();

$template = $twig->loadTemplate('listorder.html');

// $sql = "SELECT * FROM `orders` WHERE `orders`.`status` = 'WAIT' ORDER BY `orders`.`o_time` DESC";
// $result = $db->query($sql);
// $all_orders = array();
// if ($result) {
// 	while ($row = $result->fetch_assoc()) {
// 		$all_orders[] = $row;
// 	}
// }

$_HTML .= $template->render(array(

));

require_once('includes/footer.php');

?>
