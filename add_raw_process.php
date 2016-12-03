<?php

require_once('includes/general.php');

global $db;

// receive a json array
$req = $_REQUEST['request'];

// iterate req
foreach ($req as $item) {
	// insert data into raw_material table
	$sql = "INSERT INTO `raw_material` (`raw_name`, `unit`)
			VALUES ('".$item['raw_name']."', '".$item['unit']."')";

	$result = $db->query($sql);
}

/*****
 			raw_material table
raw_id     raw_name     unit

*****/

?>
