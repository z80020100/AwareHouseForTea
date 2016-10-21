<?php



require_once('includes/general.php');
header("Content-Type:text/html; charset=utf-8");

not_login_redirect();
if(!checkAuth(AUCUSTOMER|AUSTAFF|AUADMIN)){
		header("location:register.php?shop_id=" . $_shopID);
		die();
}


// List Series by order_num
$sql = "SELECT * FROM `series` WHERE `shop_id` = '".$_shopID."' ORDER BY `series`.`order_num` ASC ";

$result = $db->query($sql);
$num = $db->numrow($result);
$all_series = array();
while($series_data = $db->fetch_array($result)){
	//echo '<h3>'. $series_data['name'] . '</h3>';
	$snum = array_push( $all_series, $series_data )-1;

	// List the dishes in the series
	$sql = "SELECT * FROM `main` WHERE `s_id` = ". $series_data['s_id'] ." ORDER BY `main`.`order_num` ASC";
	$m_result = $db->query($sql);
	$all_main = array();
	while($main_data = $db->fetch_array($m_result)){
		$mnum = array_push($all_main, $main_data)-1;
		//echo '<li>';
		//echo 'm_id('.$main_data['m_id'].'):'.'name('.$main_data['name'].'):'.'price('.$main_data['price'].'):'.'at_id('.$main_data['at_id'].'):';

		//$sql = "SELECT * FROM `additional_type` WHERE `at_id` = ". $main_data['at_id'] ;
		//$at_data = $db->query_select_one($sql);   // Fetch directly : only one additional_type per main, no need to loop

		//echo "additional_item(";
		// List the "Additional options" that are directly related to the dish
		$sql = "SELECT * FROM  `additional_item` WHERE `at_id` = ".$main_data['at_id'] ;
		$ai_result = $db->query($sql);
		$all_ai = array();
		while($ai_data = $db->fetch_array($ai_result)){
		//	echo "<i>". $ai_data['name'] ."</i>,";
			$ainum = array_push($all_ai, $ai_data)-1;
		}
		$all_main[$mnum]['ai'] = $all_ai;
		//echo ")";


		$all_ro = array();
		if($main_data['required_option'] == true){
			// 1 dish <--> several required options
			$sql = "SELECT * FROM `required_option` WHERE `m_id` = ".$main_data['m_id'];
			$ro_result = $db->query($sql);
			$all_ro = array();
			while($ro_data = $db->fetch_array($ro_result)){
				$ronum = array_push($all_ro, $ro_data)-1;
				//echo "<b>".$ro_data['name']."</b>";
				//echo "(";
				// 1 required option <--> several item to choose
				$sql = "SELECT * FROM  `additional_item` WHERE `at_id` = ".$ro_data['at_id'] ;
				$ai_result = $db->query($sql);
				$all_ro_ai = array();
				while($ai_data = $db->fetch_array($ai_result)){
					$ro_ainum = array_push($all_ro_ai, $ai_data)-1;
					//echo "<i>". $ai_data['name'] ."</i>,";
				}
				$all_ro[$ronum]['ai'] = $all_ro_ai;
				//echo ")";
			}
		}
		else{
			//echo "( noro )";
		}
		$all_main[$mnum]['ro'] = $all_ro;

		//echo '</li>';
	}
	$all_series[$snum]['main'] = $all_main;
}



$_PAGE_TITLE = '早餐店菜單';
require_once('includes/header.php');


//$template = new Mustache_Engine(array());

if($_AWMode == "ACCOUNTING")
    $template = $twig->loadTemplate('customer_menu_accounting.html');

else if($_AWMode == "BUSINESS")
    $template = $twig->loadTemplate('customer_menu_business.html');






$_HTML .= $template->render(array(
	'all_series' => $all_series,
    //'verification_code' => $Qver['value'],
));




require_once('includes/footer.php');



?>
