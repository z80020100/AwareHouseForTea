<?php

$_PAGE_TITLE = '分店資訊';
require_once('includes/header.php');

// redirect if it's not top_admin || top_staff || bottom_admin
if (!(is_admin() || is_headquarters_staff())) {
        header("location:login.php?shop_id=" . $_shopID);
        die('');
}





$template = $twig->loadTemplate('store_info.html');
$_HTML .= $template->render(array(

));

require_once('includes/footer.php');

?>
