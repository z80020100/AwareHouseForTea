<?php
require_once('includes/general.php');

header("Content-Type:text/html; charset=utf-8");
not_login_redirect();
//echo $at_id;
// hamburger 40
// milk tea 10
$at_id = $_REQUEST["at_id"];
//echo json_encode($at_id,JSON_UNESCAPED_UNICODE);
//echo $at_id["at_id_array"][0];

//echo json_encode($at_id,JSON_UNESCAPED_UNICODE);
//echo json_encode($at_id["at_id_array"],JSON_UNESCAPED_UNICODE);
//echo json_encode($at_id["at_id_array"][0],JSON_UNESCAPED_UNICODE);
//echo $at_id["at_id_array"][2];
//echo sizeof($at_id["at_id_array"]);
//echo json_encode(sizeof($at_id["at_id_array"])),JSON_UNESCAPED_UNICODE);

$ai_arrays = array();


for($i=0 ; $i<sizeof($at_id["at_id_array"]); ++$i) {

	$sql = "SELECT * FROM `additional_type` WHERE `at_id` = ". $at_id["at_id_array"][$i] ;
	$at_data = $db->query_select_one($sql);


	$sql = "SELECT * FROM `required_option` WHERE `at_id` = ". $at_id["at_id_array"][$i] ;
	$isro_result = $db->query($sql);
	$isro_num = $db->numrow($isro_result);
	$isro = 0;
	if( $isro_num != 0 )
		$isro = 1;

	$ai_array = array(
			'multiple_choice'=> $at_data['multiple_choice'],
			'is_ro' => $isro,
			'ais' => array(),
			);

	$sql = "SELECT * FROM  `additional_item` WHERE `at_id` = ".$at_id["at_id_array"][$i] ;
	$ai_result = $db->query($sql);
	$all_ai = array();
	while($ai_data = $db->fetch_array($ai_result)){
		array_push($ai_array['ais'] , $ai_data);
	}

	$ai_arrays[$i] = $ai_array;

	/*
	   $ai_array = array(
	   'multiple_choice' => 0,
	   'is_ro' => 0,
	   'ais' => array(
	   array('ai_id' => 6, 'name'=>'半糖', 'price' => 10),
	   array('ai_id' => 7, 'name'=>'全糖', 'price' => 20),
	   ),
	   );



	   if($at_id["at_id_array"][$i] ==1)
	   {
	   $ai_array = array(
	   'multiple_choice' => 0,
	   'ais' => array(
	   array('ai_id' => 6, 'name'=>'半糖', 'price' => 10, 'is_ro' => 0),
	   array('ai_id' => 7, 'name'=>'全糖', 'price' => 20, 'is_ro' => 0),
	   ),
	   );

	   }
	   elseif($at_id["at_id_array"][$i] ==2){
	   $ai_array = array(
	   'multiple_choice' => 1,
	   'ais' => array(
	   array('ai_id' => 8, 'name'=>'加蛋', 'price' => 10, 'is_ro' => 1),
	   array('ai_id' => 10, 'name'=>'加菜', 'price' => 5, 'is_ro' => 1),
	   ),
	   );

	   }
	   elseif($at_id["at_id_array"][$i] ==3){
	   $ai_array = array(
	   'multiple_choice' => 1,
	   'ais' => array(
	   array('ai_id' => 20, 'name'=>'abc', 'price' => 10, 'is_ro' => 1),
	   array('ai_id' => 21, 'name'=>'def', 'price' => 5, 'is_ro' => 1),
	   ),
	   );

	   }
	   elseif($at_id["at_id_array"][$i]==4){
	   $ai_array = array(
	   'multiple_choice' => 1,
	   'ais' => array(
	   array('ai_id' => 22, 'name'=>'aaa', 'price' => 10, 'is_ro' => 1),
	   array('ai_id' => 23, 'name'=>'bbb', 'price' => 5, 'is_ro' => 1),
	   ),
	   );

	   }
	   else{

	   echo "fail";
	   }
	 */


}

echo json_encode($ai_arrays,JSON_UNESCAPED_UNICODE);




?>
