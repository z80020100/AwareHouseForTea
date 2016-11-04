<?php
//header("Content-Type:text/html; charset=utf-8");

$_PAGE_TITLE = '簡訊推播';
require_once('includes/header.php');

$template = $twig->loadTemplate('sms_promote.html');

not_admin_redirect();

$info = array();

// only query customer
// query u_id, ui_phone
$sql = "SELECT `user`.`u_id`, `user_info`.`ui_phone` FROM `user`, `user_info` WHERE `user`.`u_id` = `user_info`.`u_id` AND `user`.`u_type` = 1";
foreach ($db->query($sql) as $row) {
    
    // only query current shop_id
    // query table `log` for quantity, price
    $total_price = 0;
    $sql = "SELECT `quantity`,`price` FROM `log` WHERE `u_id` = " . $row['u_id'] . " AND `shop_id` = " . $_SESSION['GET_shop_id'];
    foreach ($db->query($sql) as $log) {
        $total_price += intval($log['quantity']) * intval($log['price']);
    }
    $row['price'] = $total_price;

    // only query current shop_id
    // query number of rows in table `orders`
    $total_times = 0;
    $sql = "SELECT * FROM `orders` WHERE `u_id` = " . $row['u_id'] . " AND `shop_id` = " . $_SESSION['GET_shop_id'];
    $result = $db->query($sql);
    if ($result)
        $total_times = $db->numrow($result);
    $row['times'] = $total_times;

    $info[] = $row;
}

$_HTML .= $template->render(array(
    'num_info' => count($info),
	'info' => $info
));

require_once('includes/footer.php');

?>
