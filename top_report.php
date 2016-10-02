<?php

// require_once('includes/general.php');
// header("Content-Type:text/html; charset=utf-8");

$_PAGE_TITLE = '總營運報表';
require_once('includes/header.php');
not_admin_redirect();
// $login_error = false;
// if(!isset($_SESSION['user_name'])){
// //  header('location:login.php');
// }

$template = $twig->loadTemplate('top_report.html');

/*
$sql = "SELECT * FROM `orders` ORDER BY `orders`.`o_time` DESC where `orders`.`status` = WAIT";
$result = $db->query($sql);
$num = $db->numrow($result);
$all_orders = array();
*/


// echo $template->render(array(

// ));

$_HTML .= $template->render(array(

));

require_once('includes/footer.php');

?>
