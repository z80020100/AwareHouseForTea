<?php

$_PAGE_TITLE = '店家列表';
require_once('includes/header.php');

$template = $twig->loadTemplate('listshop.html');

$sql = "SELECT * FROM `shop`";
$all_shops = array();
foreach ($db->query($sql) as $row) {
  $shop = array(
    'shop_id' => $row['shop_id'],
    'shop_name' => $row['shop_name'],
    'shop_address' => $row['shop_address'],
    'shop_tel' => $row['shop_tel']
  );

  $all_shops[] = $shop;
}


$_HTML .= $template->render(array(
  'allshops' => $all_shops
));

require_once('includes/footer.php');

?>
