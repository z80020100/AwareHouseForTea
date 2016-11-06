<?php

require_once('includes/general.php');

global $db;

// receive a json array
$req = $_REQUEST['request'];

// get current time with year:month:date hour:minute:second format
$curTime = date("Y-m-d H:i:s");
// get shop id for the user
$shop_id = $_SESSION['shop_id'];
// iterate req
foreach ($req as $item) {
	// insert data into ingredient table
	$sql = "INSERT INTO `ingredient` (`shop_id`, `ingredient`, `num`, 
									  `unit`, `call_time`, `handled`) 
			VALUES ('".$shop_id."', '".$item['ingredient']."', '".$item['num']."',
			 		'".$item['unit']."', '".$curTime."', 0)";

	$result = $db->query($sql);
}

/*****
 						ingredient table
shop_id     ingredient     num     unit     call_time     handled

*****/

?>
