<?php

$empty_header = True;
$empty_footer = True;

$_PAGE_TITLE = 'Happy Go 線上快樂購';
require_once('includes/header.php');

$template = $twig->loadTemplate('happytea_customer_menu.html');

$_HTML .= $template->render(array(

));

require_once('includes/footer.php');

?>
