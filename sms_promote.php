<?php
//header("Content-Type:text/html; charset=utf-8");

$_PAGE_TITLE = '簡訊推播';
require_once('includes/header.php');

not_admin_redirect();

if (isset($_POST['sms_mode'])) {
    
    $info = array();
    $smsmode = $_POST['sms_mode'];

    // sms_mode, 0 for customer, 1 for shop
    if ($smsmode == 0 && isset($_POST['starttime']) && isset($_POST['endtime']) && isset($_POST['baseline'])) {
        $starttime = $_POST['starttime'];
        $endtime = $_POST['endtime'];
        $baseline = $_POST['baseline'];
        $current_shopid = $_SESSION['GET_shop_id'];
        
        // only query customer
        // query u_id, ui_phone
        $sql = "SELECT `user`.`u_id`, `user_info`.`ui_phone` FROM `user`, `user_info` WHERE `user`.`u_id` = `user_info`.`u_id` AND `user`.`u_type` = 1";
        
        foreach ($db->query($sql) as $row) {
            $u_id = $row['u_id'];

            // only query current shop_id
            // query table `log` for quantity, price
            $total_price = 0;
            $sql = "SELECT `quantity`,`price` FROM `log` WHERE `u_id` = $u_id AND `shop_id` = $current_shopid AND UNIX_TIMESTAMP(`time`) >= $starttime AND UNIX_TIMESTAMP(`time`) <= $endtime";
            foreach ($db->query($sql) as $log) {
                $total_price += intval($log['quantity']) * intval($log['price']);
            }
            $row['price'] = $total_price;

            // only query current shop_id
            // query number of rows in table `orders`
            $total_times = 0;
            $sql = "SELECT * FROM `orders` WHERE `u_id` = $u_id AND `shop_id` = $current_shopid AND UNIX_TIMESTAMP(`o_time`) >= $starttime AND UNIX_TIMESTAMP(`o_time`) <= $endtime";
            $result = $db->query($sql);
            if ($result)
                $total_times = $db->numrow($result);
            $row['times'] = $total_times;

            // check if total_price is bigger than or equal to baseline
            if ($total_price >= $baseline)
                $info[] = $row;
        }
    }
    else if ($smsmode == 1) {
        // only query shop
        // query shop owner's phone number
        $sql = "SELECT `shop`.`shop_id`, `shop`.`shop_name`, `shop`.`shop_owner`, `shop`.`shop_account`, `user_info`.`ui_phone` FROM `shop`, `user_info` WHERE `shop`.`shop_account` = `user_info`.`u_id`";
        
        foreach ($db->query($sql) as $shop) {
            $info[] = $shop;
        }
    }

    echo json_encode($info);
    exit();
}

$template = $twig->loadTemplate('sms_promote.html');

$_HTML .= $template->render(array());

require_once('includes/footer.php');

?>
