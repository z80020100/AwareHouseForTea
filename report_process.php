<?php
require_once('includes/general.php');

function getOrderInfo($db, $start_time, $end_time) {
    global $shift_start, $shift_end;
    $sql = "SELECT * FROM `orders` WHERE `o_time` >= '".$start_time."' AND `o_time` <= '".$end_time."' AND HOUR(`o_time`) >= ".$shift_start." AND HOUR(`o_time`) < ".($shift_end+1)." AND `status` != '".$GLOBALS['STATUS'][0]."' ";
    $result = $db->query($sql);
    $order_info = array();
    while($order = $db->fetch_array($result)){
		$order_corrupted = false;
        $sql = "SELECT * FROM `share` WHERE `o_id` = ".$order['o_id'] ;
        $s_result = $db->query($sql);
        $share_info = array();
        $order_total = 0;
        while($share = $db->fetch_array($s_result)){
            $sql = "SELECT * FROM `share_item` WHERE `sh_id` = ".$share['sh_id'];
            $sh_i_result = $db->query($sql);
            $item_info = array();
            $counting_total = 0;
            while($item = $db->fetch_array($sh_i_result)){

                $sql = "SELECT * FROM `main` WHERE `m_id` = ".$item['m_id'];
                $m_result = $db->query($sql);
                $main = $db->fetch_array($m_result);
				if($main == false)
				{
					$order_corrupted = true;
				}
				else{
					// Requirement Option
					$sql = "SELECT * FROM `sh-i_ai` WHERE `sh-i_id` = ".$item['sh-i_id']." AND `is_ro` = 1 ";
					$sh_i_ai_result = $db->query($sql);
					$ro_info = array();
					while($sh_i_ai = $db->fetch_array($sh_i_ai_result)){
						$sql = "SELECT * FROM `additional_item` WHERE `ai_id` = ".$sh_i_ai['ai_id'];
						$ro_result = $db->query($sql);
						$ro = $db->fetch_array($ro_result);

						$outRo = array();
						$outRo['name'] = $ro['name'];
						$outRo['price'] = $ro['price'];

						// Counting price
						$counting_total += $ro['price'];
						array_push($ro_info, $outRo);
					}
					// Additional Option
					$sql = "SELECT * FROM `sh-i_ai` WHERE `sh-i_id` = ".$item['sh-i_id']." AND `is_ro` = 0 ";
					$sh_i_ai_result = $db->query($sql);
					$ai_info = array();
					while($sh_i_ai = $db->fetch_array($sh_i_ai_result)){
						$sql = "SELECT * FROM `additional_item` WHERE `ai_id` = ".$sh_i_ai['ai_id'];
						$ai_result = $db->query($sql);
						$ai = $db->fetch_array($ai_result);

						$outAi = array();
						$outAi['name'] = $ai['name'];
						$outAi['price'] = $ai['price'];
						// Counting price
						$counting_total += $ai['price'];
						array_push($ai_info, $outAi);
					}
					$counting_total += $main['price'];
					$counting_total = $counting_total * $item['quantity'];

					$outItem = array();
					$outItem['name']        =   $main['name'];
					$outItem['main_price']  =   $main['price'];
					$outItem['m_id']        =   $main['m_id'];
					$outItem['s_id']        =   $main['s_id'];
					$outItem['quantity']        =   $item['quantity'];
					$outItem['comment']         =   $item['comment'];
					$outItem['RO_array']    =   $ro_info;
					$outItem['AI_array']        =   $ai_info;

					array_push($item_info, $outItem);

				}
            }
            $outShare = array();
            $outShare['total'] = $counting_total;
            $outShare['items_array'] = $item_info;
            $order_total +=  $counting_total;
            array_push($share_info, $outShare);
        }
        $outOrder = $order;
        $outOrder['share_array'] = $share_info;
        $outOrder['summary_array'] = makeSummary($share_info);
        $outOrder['total'] = $order_total ;
		if($order_corrupted == false)
			array_push( $order_info, $outOrder );
    }

    return $order_info;
}

function getMenu($db) {
    $sql = "SELECT * FROM `main`";
    $result = $db->query($sql);
    $menu = array();
    while ($menu_item = $db->fetch_array($result)) {
        $menu[$menu_item['name']] = 0;
    }

    return $menu;
}

function getAllLists($db) {
    // List Series by order_num
    $sql = "SELECT * FROM `series` ORDER BY `series`.`order_num` ASC ";
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

        // List the "Additional options" that are directly related to the dish
        $sql = "SELECT * FROM  `additional_item` WHERE `at_id` = ".$main_data['at_id'] ;
        $ai_result = $db->query($sql);
        $all_ai = array();
        while($ai_data = $db->fetch_array($ai_result)){
        //  echo "<i>". $ai_data['name'] ."</i>,";
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

    return $all_series;
}

function getLogInfo($db, $start_time, $end_time) {
    global $shift_start, $shift_end;
    $sql = "SELECT * FROM `log` WHERE `time` >= '".$start_time."' AND `time` <= '".$end_time."' AND HOUR(`time`) >= ".$shift_start." AND HOUR(`time`) < ".($shift_end+1);
    $result = $db->query($sql);
    $ret = array();
    while($order = $db->fetch_array($result)){
        array_push($ret, $order);
    }
    return $ret;
}

function getAllDataArray($log) {
    /*
    AllDataArray = {
      series : {
        main: {
          quantity: #,
          price: #
        },
        main: {
          quantity: #,
          price: #
        },
        ...
      },
      series : {
        ...
      },
      ...
    }
    */
    $AllDataArray = array();
    $log_size = count($log);
    for ($i = 0; $i < $log_size; $i++) {
        if (!array_key_exists($log[$i]["s_text"], $AllDataArray)) {
            $AllDataArray[$log[$i]["s_text"]] = array();
        }

        if (!array_key_exists($log[$i]["m_text"], $AllDataArray[$log[$i]["s_text"]])) {
            $AllDataArray[$log[$i]["s_text"]][$log[$i]["m_text"]] = array("quantity" => 0, "price" => 0);
        }

        $tmp = & $AllDataArray[$log[$i]["s_text"]][$log[$i]["m_text"]];
        $tmp["quantity"] += intval($log[$i]["quantity"]);
        $tmp["price"] += intval($log[$i]["price"]) * intval($log[$i]["quantity"]);
    }
    return $AllDataArray;
}

$type = $_REQUEST['request']['type'];
$start_time = $_REQUEST['request']['time'][0];
$end_time = $_REQUEST['request']['time'][1];

switch ($type) {
  case "sales":
    $sales = array();

    // //GET THE order_info FROM DATABASE
    $order_info = getOrderInfo($db, $start_time, $end_time);

    // //GET THE WHOLE menu FROM THE DATABASE
    $menu = getMenu($db);

    $sales[0] = $menu;
    $sales[1] = $order_info;
    $sales[2] = getAllLists($db);

    echo json_encode($sales, JSON_UNESCAPED_UNICODE);
    break;

  case "orders":
    $ret = array();
    array_push($ret, $shift_start);
    array_push($ret, $shift_end);
    $time = array();

    $orders = getOrderInfo($db, $start_time, $end_time);
    $orders_size = count($orders);
    for ($i = 0; $i < $orders_size; $i++) {
        $t_1 = explode(" ", $orders[$i]['o_time']);
        $t_2 = explode(":", $t_1[1]);
        for ($j = 0; $j < count($orders[$i]['share_array']); $j++) {
            array_push($time, $t_2[0]);
        }
    }
    array_push($ret, $time);
    $log = getLogInfo($db, $start_time, $end_time);
    $ret[3] = getAllDataArray($log);
    echo json_encode($ret, JSON_UNESCAPED_UNICODE);
    break;

  case "menu":
    $menu = getMenu($db);
    $log = getLogInfo($db, $start_time, $end_time);
    $AllDataArray = getAllDataArray($log);
    $log_size = count($log);
    $total = 0;
    $ret = array();
    foreach ($AllDataArray as $series => $main_array) {
        foreach ($main_array as $main => $value) {
            if (!array_key_exists($main, $menu)) unset($main);
            else $total += intval($value["price"]);
        }
    }

    array_push($ret, $total);
    array_push($ret, $AllDataArray);

    echo json_encode($ret, JSON_UNESCAPED_UNICODE);
    break;
}
?>
