<?php

require_once('includes/general.php');
require_once('includes/general_functions.php');


//  SELECT * FROM `orders` WHERE `o_time` >= FROM_UNIXTIME(0) AND `status` != 'PAID'


//
//		itemCompare 用來對 item進行排序
//
//




$reqType = $_REQUEST['request']['type'];

switch($reqType){
	case "refresh":
	/*********************************************************************************

			老闆端讀取未完成的 Order
			由前端傳來時間，傳回該時間之後的所有未完成的單子 ( `status` != 'PAID' )
			並以makeSummary函式把原本小單型式的Item進行合併

	*********************************************************************************/
	if (!is_login()) die('not loginned');

	$refresh_time = $_REQUEST['request']['time'];
	//echo $refresh_time;
	//1900-01-01 00:00:00
	//
	//echo $sql . "\nyoooo\n";
	
	if($_REQUEST['request']['fresh'] == 'true'){
		$sql = "SELECT * FROM `orders` WHERE `o_utime` > '".$refresh_time."' AND `status` != '".$GLOBALS['STATUS'][0]."' AND `status` != '".$GLOBALS['STATUS'][(sizeof($GLOBALS['STATUS'])-1)]."' AND `shop_id` = '".$_shopID."'";
	}
	else
		$sql = "SELECT * FROM `orders` WHERE `o_utime` > '".$refresh_time."' AND `shop_id` = '".$_shopID."'";

	$result = $db->query($sql);
	$order_info = array();
	//$order_info[0] = $_REQUEST['request']['fresh'];
	while($order = $db->fetch_array($result)){
		//echo '<h3>'. $series_data['name'] . '</h3>';

		//if($order['o_estimate_time'] == $order['o_time'])  {
			// 代表老闆尚未設定等候時間(預設值為Current_time), 因此傳送NULL讓js處理
		if( !isset($order['o_estimate_time']) ){
			$order['o_estimate_time'] = 'NULL';
		}

		$outOrder_temp = order_detail($order['o_id']);
		
		
		$sql = "SELECT * FROM `user` LEFT JOIN (`user_info`) ON `user_info`.`u_id` = `user`.`u_id` WHERE `user`.`u_id` = '".$order['u_id']."'";
		$uresult = $db->query($sql);
		$order_user = $db->fetch_array($uresult);
		
		$order['user'] = $order_user;
		
		
		$outOrder = array_merge($order, $outOrder_temp);
		array_push( $order_info, $outOrder );
	}
	echo json_encode($order_info,JSON_UNESCAPED_UNICODE);
	break;

	case "updateOrderStatus":
		if (!is_login()) die('not loginned');
		if (!is_staff()) die('not staff');

		if($_REQUEST['request']['swipe'] == 'right'){
			$nextStatus = statusUp($_REQUEST['request']['current_status']);
		}
		else if($_REQUEST['request']['swipe'] == 'left'){
			$nextStatus = statusDown($_REQUEST['request']['current_status']);
		}
		$sql = "UPDATE `orders` SET `status` = '".$nextStatus."' WHERE `orders`.`o_id` = ".$_REQUEST['request']['oid'].";";
		$update_result = $db->query($sql);
		
		// Log
		if($_REQUEST['request']['current_status'] != 'ARCHIVE' && $nextStatus == 'ARCHIVE')
		{
			log_order($_REQUEST['request']['oid']);
			/*
			INSERT INTO `log` (`log_id`, `o_id`, `time`, `s_text`, `m_text`, `quantity`, `price`) VALUES (NULL, '1', '2016-07-14 10:00:00', 'dd', 'aa', '1', '2');
			*/
			
			//$sql = "INSERT INTO `log` (`log_id`, `o_id`, `time`, `s_text`, `m_text`, `quantity`, `price`) VALUES (NULL, '".$_REQUEST['request']['oid']."', '2016-07-14 10:00:00', 'dd', 'aa', '1', '2');";
			
			//$sql = "SELECT * FROM `orders` WHERE `o_id` = '".$_REQUEST['request']['oid']."' ";
			//$result = $db->query($sql);
			//$order = $db->fetch_array($result)
		}
		else if($_REQUEST['request']['current_status'] == 'ARCHIVE' && $nextStatus != 'ARCHIVE')
		{
			unlog_order($_REQUEST['request']['oid']);
		}
		echo  json_encode($nextStatus, JSON_UNESCAPED_UNICODE);
	break;

	case "updateOrderEstimate":
		if (!is_login()) die('not loginned');
		if (!is_staff()) die('not staff');

		$sql = "SELECT * FROM `orders` WHERE `orders`.`o_id` = ".$_REQUEST['request']['oid'].";";
		$order = $db->query_select_one($sql);

		if( isset($order['o_estimate_time'] ) ){
			$sql = "UPDATE `orders` SET `o_estimate_time` = ADDTIME( `o_estimate_time`   ,'0:".$_REQUEST['request']['addMIN'].":0') WHERE `orders`.`o_id` = ".$_REQUEST['request']['oid'].";";
			$update_result = $db->query($sql);
		}
		else{
			$sql = "UPDATE `orders` SET `o_estimate_time` = ADDTIME( NOW()   ,'0:".$_REQUEST['request']['addMIN'].":0') WHERE `orders`.`o_id` = ".$_REQUEST['request']['oid'].";";
			$update_result = $db->query($sql);
		}
		echo "ok";

	break;
}

//print_r($order_info);



?>