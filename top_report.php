<?php

$_PAGE_TITLE = '總營運報表';
require_once('includes/header.php');

not_admin_redirect();

$template = $twig->loadTemplate('top_report.html');

$_HTML .= $template->render(array(

));

require_once('includes/footer.php');

?>
