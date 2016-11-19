<?php

$_PAGE_TITLE = '物料管理';
require_once('includes/header.php');

not_topboss_redirect();

$template = $twig->loadTemplate('inventory.html');

not_admin_redirect();

$_HTML .= $template->render(array());

require_once('includes/footer.php');

?>
