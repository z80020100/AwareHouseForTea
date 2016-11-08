<?php

$empty_header = True;
$empty_footer = True;

$_PAGE_TITLE = '註冊帳號';
require_once('includes/header.php');

$template = $twig->loadTemplate('customer_register.html');

$_HTML .= $template->render(array(

));

require_once('includes/footer.php');

?>
