<?php
require_once('includes/general.php');

header("Content-Type:text/html; charset=utf-8");

not_admin_redirect();

$action = $_REQUEST["action"];

$ro_arrays = array();
$at_arrays = array();
$ai_arrays = array();
$at_id_arrays = array();

if($action == "read_ro")
{ 
	$read_sql = "SELECT * FROM `additional_type` WHERE `multiple_choice` = 0";
	$ro_result = $db->query($read_sql);
	while($ro_data = $db->fetch_array($ro_result)){
		array_push( $ro_arrays, $ro_data );
		//$ro_arrays[$ro_num]['required'] = $ro_data;
	}
	echo json_encode($ro_arrays, JSON_UNESCAPED_UNICODE);
}
else if($action == "read_detail")
{
	$at_id = $_REQUEST["at_id"];
	
	$read_sql = "SELECT * FROM `additional_item` WHERE `at_id` = " . $at_id;
	$ro_result = $db->query($read_sql);
	while($ro_data = $db->fetch_array($ro_result)){
		array_push( $ro_arrays, $ro_data );
		//$ro_arrays[$ro_num]['required'] = $ro_data;
	}
	echo json_encode($ro_arrays, JSON_UNESCAPED_UNICODE);
}

else if($action == "read_at")
{
	$mul_value = $_REQUEST["mul_value"];
	if($mul_value == "multi")
		$read_sql = "SELECT * FROM `additional_type` WHERE `multiple_choice` = 1";
	else
		$read_sql = "SELECT * FROM `additional_type` WHERE `multiple_choice` = 0";
	$at_result = $db->query($read_sql);
	while($at_data = $db->fetch_array($at_result)){
		array_push( $at_arrays, $at_data );
	}
	echo json_encode($at_arrays, JSON_UNESCAPED_UNICODE);
}

else if($action == "write_required_option")
{
	$read_sql = "SELECT MAX(at_id) FROM `additional_type`";
	$at_result = $db->query($read_sql);
	while($at_data = $db->fetch_array($at_result)){
		array_push( $at_arrays, $at_data );
	}
	$new_type = $_REQUEST["new_type"];
	$mul_type = $_REQUEST["mul_type"];
	if($mul_type == "single")
	{
		$write_sql = "INSERT INTO `additional_type` (`option_name`, `multiple_choice`) VALUES ('" .$new_type. "', '0')";
	}
	else if($mul_type == "multi")
	{
		$write_sql = "INSERT INTO `additional_type` (`option_name`, `multiple_choice`) VALUES ('" .$new_type. "', '1')";
	}		
	
	$db->query($write_sql);
	$new_at_id = $db->mysqli->insert_id; // 返回上次INSERT操作新增的ID
	
	echo json_encode($new_at_id, JSON_UNESCAPED_UNICODE); // 回傳新增的at_id
}

else if($action == "edit_ai")
{
	$data_type = $_REQUEST["data_type"];
	$data = $_REQUEST["data"];
	$ai_id = $_REQUEST["ai_id"];
	$write_sql = "UPDATE `additional_item` SET `$data_type` = '$data' WHERE `additional_item`.`ai_id` = $ai_id";
	$result = $db->query($write_sql); // 回傳操錯是否成功(true or false)
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
}

else if($action == "add_new_ai")
{
	$at_id = $_REQUEST["at_id"];

	$read_sql = "SELECT MAX(ai_id) FROM `additional_item` WHERE 1";
	$result = $db->query($read_sql);
	while($data = $db->fetch_array($result)){
		array_push( $ai_arrays, $data );
	}
		
	$write_sql = "INSERT INTO `additional_item` (`at_id`, `name`, `price`) VALUES ('$at_id', '未命名品項', '0')";
	$result = $db->query($write_sql);
	$new_ai_id = $db->mysqli->insert_id; // 返回上次INSERT操作新增的ID

	echo json_encode($new_ai_id, JSON_UNESCAPED_UNICODE);
}

else if($action == "write_main")
{
	$name = $_REQUEST["name"];
	$price = $_REQUEST["price"];
	$s_id = $_REQUEST["s_id"];
	$at_id = $_REQUEST["at_id"];
	$required_option = $_REQUEST["required_option"];
	
	$read_sql = "SELECT MAX(order_num) FROM `main` WHERE 1";
	$result = $db->query($read_sql);
	$order_array = array();
	while($data = $db->fetch_array($result)){
		array_push( $order_array, $data );
	}
	$max_order = $order_array[0]['MAX(order_num)'];
	if($max_order == NULL)
		$max_order = 0;
	else
		$max_order++;
	
	$write_sql = "INSERT INTO `main` (`name`, `price`, `s_id`, `at_id`, `required_option`, `order_num`) 
				  VALUES ('$name', '$price', '$s_id', '$at_id', '$required_option', $max_order)";
	$result = $db->query($write_sql);
	$new_main_id = $db->mysqli->insert_id;
	
	echo json_encode($new_main_id, JSON_UNESCAPED_UNICODE);
}

else if($action == "write_required_option_table")
{
	$m_id = $_REQUEST["m_id"];
	$at_id_array = $_REQUEST["at_id_array"];	
	
	for($i = 0; $i < count($at_id_array); $i++){
		$at_id = $at_id_array[$i];
		$write_sql = "INSERT INTO `required_option` (`m_id`, `at_id`) 
					  VALUES ('$m_id', '$at_id')";
		$result = $db->query($write_sql);
	}
		
	echo json_encode(count($at_id_array), JSON_UNESCAPED_UNICODE);
}

else if($action == "read_ro_for_m_id")
{
	$m_id = $_REQUEST["m_id"];
	
	$read_sql = "SELECT `at_id` FROM `required_option` WHERE `m_id` = $m_id";
	$result = $db->query($read_sql);
	while($data = $db->fetch_array($result)){
		array_push( $at_id_arrays, $data );
	}
	for($i = 0; $i < count($at_id_arrays); $i++){
		$read_sql = "SELECT * FROM `additional_type` WHERE `at_id` = " . $at_id_arrays[$i]['at_id'];
		$result = $db->query($read_sql);
		while($data = $db->fetch_array($result)){
			array_push( $at_arrays, $data );
		}
	}
		
	echo json_encode($at_arrays, JSON_UNESCAPED_UNICODE);
}

else if($action == "del_main")
{
	$m_id = $_REQUEST["m_id"];
	
	$write_sql = "DELETE FROM `main` WHERE `main`.`m_id` = $m_id";
	$result = $db->query($write_sql); // 回傳操錯是否成功(true or false)
	
	$write_sql = "DELETE FROM `required_option` WHERE `required_option`.`m_id` = $m_id";
	$result = $db->query($write_sql); // 回傳操錯是否成功(true or false)
	
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
}

else if($action == "edit_main_del_ro") // 先刪除所有與m_id相關之required_option，再以新增方式編輯
{
	$m_id = $_REQUEST["m_id"];
	$name = $_REQUEST["name"];
	$price = $_REQUEST["price"];
	$at_id = $_REQUEST["at_id"];
	$required_option = $_REQUEST["required_option"];
		
	$write_sql = "DELETE FROM `required_option` WHERE `required_option`.`m_id` = $m_id";
	$result = $db->query($write_sql); // 回傳操作是否成功(true or false)

	$write_sql = "UPDATE `main` SET `name` = '$name', `price` = $price, `at_id` = $at_id, `required_option` = $required_option. WHERE `main`.`m_id` = $m_id;";
	$result = $db->query($write_sql); // 回傳操作是否成功(true or false)
	
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
}


?>
