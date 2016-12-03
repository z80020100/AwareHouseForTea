<?php

require_once('includes/general.php');
//header("Content-Type:text/html; charset=utf-8");

global $db;

$_PAGE_TITLE = '增加物料';
require_once('includes/header.php');

// redirect if it's not top_admin || top_staff
if (!(is_topboss() || is_headquarters_staff())) {
  header("location:login.php?shop_id=" . $_shopID);
  die('');
}


$shop_id = $_SESSION['shop_id'];

// read data from raw_material table
$sql = "SELECT `raw_name`, `unit` FROM `raw_material`";

$result = $db->query($sql);

$template = $twig->loadTemplate('add_raw.html');

$_HTML .= $template->render(array(
  'data' => $result,
));


require_once('includes/footer.php');

?>
