<?php

$_PAGE_TITLE = '即時訂單';
require_once('includes/header.php');

not_staff_redirect();

$template = $twig->loadTemplate('listorder.html');


$_HTML .= $template->render(array(

));

require_once('includes/footer.php');

?>
