<?php

require_once('includes/general.php');

global $db;

// receive a json array
$req = $_REQUEST['request'];

// update if all info is the same
$sql = "UPDATE `ingredient` 
		SET `handled` = 1
		WHERE `shop_id` = '".$req['shop_id']."' 
		AND `call_time` = '".$req['call_time']."'
		AND `ingredient` = '".$req['ingredient']."'
		AND `num` = '".$req['num']."'
		AND `unit` = '".$req['unit']."'
		LIMIT 1";

$result = $db->query($sql);

?>