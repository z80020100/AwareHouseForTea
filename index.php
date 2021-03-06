<meta charset="utf-8">
<?php

$_PAGE_TITLE = '樂台茶POS系統';
$empty_header = True;
$empty_footer = True;
require_once('includes/header.php');

not_login_redirect();
if (is_staff()) {
	not_staff_redirect();
}

// List Series by order_num
$sql = "SELECT * FROM `series` WHERE `shop_id` = '".$_shopID."' ORDER BY `series`.`order_num` ASC ";

$result = $db->query($sql);
$num = $db->numrow($result);
$all_series = array();
while($series_data = $db->fetch_array($result)){
	$snum = array_push( $all_series, $series_data )-1;

	// List the dishes in the series
	$sql = "SELECT * FROM `main` WHERE `s_id` = ". $series_data['s_id'] ." ORDER BY `main`.`order_num` ASC";
	$m_result = $db->query($sql);
	$all_main = array();
	while($main_data = $db->fetch_array($m_result)){
		$mnum = array_push($all_main, $main_data)-1;

		// List the "Additional options" that are directly related to the dish
		$sql = "SELECT * FROM  `additional_item` WHERE `at_id` = ".$main_data['at_id'] ;
		$ai_result = $db->query($sql);
		$all_ai = array();
		while($ai_data = $db->fetch_array($ai_result)){
			$ainum = array_push($all_ai, $ai_data)-1;
		}
		$all_main[$mnum]['ai'] = $all_ai;

		$all_ro = array();
		if($main_data['required_option'] == true){
			// 1 dish <--> several required options
			$sql = "SELECT * FROM `required_option` WHERE `m_id` = ".$main_data['m_id'];
			$ro_result = $db->query($sql);
			$all_ro = array();
			while($ro_data = $db->fetch_array($ro_result)){
				$ronum = array_push($all_ro, $ro_data)-1;

				// 1 required option <--> several item to choose
				$sql = "SELECT * FROM  `additional_item` WHERE `at_id` = ".$ro_data['at_id'] ;
				$ai_result = $db->query($sql);
				$all_ro_ai = array();
				while($ai_data = $db->fetch_array($ai_result)){
					$ro_ainum = array_push($all_ro_ai, $ai_data)-1;
				}
				$all_ro[$ronum]['ai'] = $all_ro_ai;
			}
		}
		$all_main[$mnum]['ro'] = $all_ro;
	}
	$all_series[$snum]['main'] = $all_main;
}


// Query all addition item 
$sql = "SELECT * FROM `additional_type` WHERE `multiple_choice` = 1";
$result = $db->query($sql);
$multiple_addition_type = array();
$multiple_addition_item = array();
while($series_data = $db->fetch_array($result)){
	$snum = array_push( $multiple_addition_type, $series_data )-1;
	$sql = "SELECT DISTINCT name FROM `additional_item` WHERE `at_id` = ".$series_data['at_id'];

	$a_result = $db->query($sql);
	while($a_data = $db->fetch_array($a_result)){
		$anum = array_push($multiple_addition_item, $a_data)-1;
    }
}


$sql = "SELECT * FROM `additional_type` WHERE `multiple_choice` = 0";
$result = $db->query($sql);
$single_addition_type = array();
$single_addition_item = array();
while($series_data = $db->fetch_array($result)){
	$snum = array_push( $single_addition_type, $series_data )-1;
	$sql = "SELECT DISTINCT name FROM `additional_item` WHERE `at_id` = ".$series_data['at_id'];

	$a_result = $db->query($sql);
	while($b_data = $db->fetch_array($a_result)){
		$anum = array_push($single_addition_item, $b_data)-1;
    }
}

$sql = "SELECT DISTINCT name, price FROM `additional_item`";
$all_material = $db->query($sql);

if($_AWMode == "ACCOUNTING")
    $template = $twig->loadTemplate('customer_menu_accounting.html');

else if($_AWMode == "BUSINESS") {
	if (is_staff())
		$template = $twig->loadTemplate('staff_menu_business.html');
	else
		$template = $twig->loadTemplate('customer_menu_business.html');
}


$_HTML .= $template->render(array(
	'all_series' => $all_series,
	'multiple_addition_item' => $multiple_addition_item,
	'single_addition_item' => $single_addition_item,
	'single_addition_type' => $single_addition_type,
	'all_material' => $all_material,
    //'verification_code' => $Qver['value'],
));

require_once('includes/footer.php');


?>
