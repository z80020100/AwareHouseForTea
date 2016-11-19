<?php

$_PAGE_TITLE = '上游叫料';
require_once('includes/header.php');

not_admin_redirect();

$template = $twig->loadTemplate('call.html');

$_HTML .= $template->render(array());

require_once('includes/footer.php');

?>
