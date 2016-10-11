<?php

$_PAGE_TITLE = '';
require_once('includes/header.php');
not_admin_redirect();

$template = $twig->loadTemplate('empty.html');


$_HTML .= $template->render(array(

));

require_once('includes/footer.php');

?>
