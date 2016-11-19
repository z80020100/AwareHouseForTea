<?php

$_PAGE_TITLE = '營運報表';
require_once('includes/header.php');

not_admin_redirect();

$template = $twig->loadTemplate('report.html');

$_HTML .= $template->render(array(

));

require_once('includes/footer.php');

?>
