<?php

require_once('includes/general.php');
//header("Content-Type:text/html; charset=utf-8");

$_PAGE_TITLE = '上游叫料';
require_once('includes/header.php');

not_admin_redirect();

// read data from raw_material table
/*****
      raw_material table
raw_id     raw_name     unit

*****/
$sql = "SELECT * FROM `raw_material`";

$result = $db->query($sql);

$template = $twig->loadTemplate('call.html');

$_HTML .= $template->render(array(
  'data' => $result,
));

require_once('includes/footer.php');

?>
