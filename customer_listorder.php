<?php

$_PAGE_TITLE = '早餐店後台管理系統';
require_once('includes/header.php');

not_login_redirect();

$template = $twig->loadTemplate('customer_listorder.html');

$_HTML .= $template->render(array());

require_once('includes/footer.php');

?>
