<?php

//require_once('includes/general.php');
//header("Content-Type:text/html; charset=utf-8");

$_PAGE_TITLE = '即時訂單';
require_once('includes/header.php');

not_login_redirect();
not_staff_redirect();

$template = $twig->loadTemplate('listorder.html');

/*
$sql = "SELECT * FROM `orders` ORDER BY `orders`.`o_time` DESC where `orders`.`status` = WAIT";
$result = $db->query($sql);
$num = $db->numrow($result);
$all_orders = array();
*/

not_staff_redirect();

$_HTML .= $template->render(array(
	
));


require_once('includes/footer.php');

?>
