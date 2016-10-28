<?php

/* general functions **********************************************/



/*
CANCEL 		: 	已取消
WAIT		:	等待製作中
MAKING		:	製作中
DONE		:	餐點完成
PAID		:	已付錢
ARCHIVE	:	已封存
*/

/*
	functions to control order status
*/

$GLOBALS['STATUS'] = array(
	'CANCEL',  // cannot be deleted
	'WAIT',		// Default
	'MAKING',
	'DONE',
//	'PAID',
	'ARCHIVE'  // cannot be deleted
);

define("IDGUEST", 0);
define("IDCUSTOMER", 1);
define("IDSTAFF", 2);
define("IDADMIN", 3);

define("AUGUEST", (1 << IDGUEST) );
define("AUCUSTOMER", (1 << IDCUSTOMER) );
define("AUSTAFF", (1 << IDSTAFF));
define("AUADMIN", (1 << IDADMIN));

$_Identity = array();
$_Identity[IDGUEST] = array('id' => IDGUEST, 'desc' => '訪客/未啟用顧客', 'name' => 'guest');
$_Identity[IDCUSTOMER] = array('id' => IDCUSTOMER,'desc' => '顧客', 'name' => 'customer');
$_Identity[IDSTAFF] = array('id' => IDSTAFF,'desc' => '職員', 'name' => 'staff');
$_Identity[IDADMIN] = array('id' => IDADMIN,'desc' => '老闆', 'name' => 'admin');

define("DIRECT", 1);
define("JOIN", 2);
define("DEMO", 3);


function statusUp( $cStatus){
	$kIndex = array_search( $cStatus, $GLOBALS['STATUS']);
	if($kIndex == sizeof($GLOBALS['STATUS'])-1)
		return $GLOBALS['STATUS'][ $kIndex ] ;
	return $GLOBALS['STATUS'][ $kIndex+1 ];
}

function statusDown( $cStatus){
	$kIndex = array_search( $cStatus, $GLOBALS['STATUS']);
	if($kIndex == 0)
		return $GLOBALS['STATUS'][ $kIndex ] ;
	return $GLOBALS['STATUS'][ $kIndex-1 ];
}

/*
	User Login & Admin Login functions

	For security reasons, the login process is designed as follow:

	(1) Must be under SSL

	(2)
	Client side:  send plaintext password to server
	Server side: sha256(password)

*/

function user_create($username, $userpass, $userRegInfo, $shop_id){
	global $db;

	if(!is_admin()){
		/*$sql = "SELECT * FROM `config` WHERE `name` = 'verification'";
		$Qver = $db->query_select_one($sql);

		if($verification != $Qver['value']){
			return false;
		}*/

		//$sql = "SELECT * FROM `user_register` WHERE `u_id` = 'code'"

	}

	$sql = "SELECT * FROM `user` WHERE `u_name` = '".$username."' ";
	$Quser = $db->query_select_one($sql);
	if( $Quser ){
		return -1;
	}

	// $shop_id is not permitted to be empty string
	if ($shop_id == '')
		$shop_id = 0;

	// hash("sha256", "test1234");
	$sql = "INSERT INTO `user` (`u_id`, `u_name`, `u_pass`, `u_type`, `shop_id`) VALUES (NULL, '".$username."', '".hash("sha256",$userpass)."', '".$userRegInfo['utype']."', '".$shop_id."');";
	if( !$result = $db->query($sql) ){
		echo "<p>Error: " . $db->err . "</p>";
		echo "<p>Error text: " . $db->errtext . "</p>";
		die('error gf_uc_1<br>');
	}

	$new_user_id = $db->mysqli->insert_id;

	$sql = "INSERT INTO `user_info` (`ui_id`, `u_id`, `ui_advsecurity`, `ui_occupation`, `ui_phone`) VALUES (NULL, '".$db->mysqli->insert_id."', '".$userRegInfo['advsecurity']."', '', '".$userRegInfo['phone']."')";
	if( !$result = $db->query($sql) ){
		//die($sql);
		echo "<p>Error: " . $db->err . "</p>";
		echo "<p>Error text: " . $db->errtext . "</p>";
		die('error gf_uc_2');
	}
	return $new_user_id;

}

function user_vercode( $updateAnyway = false, $u_name = false , $u_id = false){
	global $db;

	if(!$u_name)
		$u_name = $_SESSION['u_name'];

	if(!$u_id)
		$u_id = $_SESSION['u_id'];

	$hashme = $u_name.' '.$u_id.' '.time().' '.rand();
	$new_hash = substr( hash("sha256", $hashme) , 0, 4);

	$sql = "SELECT * FROM `user_register` WHERE `u_id` = '".$u_id."'";
	$Quver = $db->query_select_one($sql);

	$returnme = array(
		'hash' => '',
		'updated' => $updateAnyway
	);

	if( !$Quver ){
		$sql = "INSERT INTO `user_register` (`u_id`, `code`, `expiration`) VALUES ('".$u_id."', '".$new_hash."', '".(time()+60)."')";
		$db->query($sql);

		$returnme['hash'] = $new_hash;
		$returnme['updated'] = true;
		return $returnme;
	}
	else{
		if(time() > $Quver['expiration'] || $updateAnyway == true)
		{
			$sql = "UPDATE `user_register` SET `code` = '".$new_hash."', `expiration` = '".(time()+60)."' WHERE `user_register`.`u_id` = '".$u_id."'";
			$db->query($sql);
			$returnme['hash'] = $new_hash;
			$returnme['updated'] = true;
			return $returnme;
		}
		else{
			$returnme['hash'] = $Quver['code'];
			$returnme['updated'] = false;
			return $returnme;
		}
	}
}

function send_sms($dst, $msg){
	global $_DontSendSMS;
	if($_DontSendSMS == false){

		$smsURL = "http://202.39.48.216/kotsmsapi-1.php";

		$post = array(
			'username' => 'awarehouse',
			'password' => 'm198916',
			'dstaddr' => $dst,
			'smbody' => iconv("UTF-8", "big5", $msg),
			'dlvtime' => 0, // instantly
		);

		$ch = curl_init();

		$opt = array(
			CURLOPT_URL => $smsURL,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $post,
		);

		curl_setopt_array($ch, $opt);
		curl_exec($ch);
		curl_close($ch);
	}
	else{
		echo $msg;
	}
}

function user_login($username, $password, $phone_info){
	global $db;

	$sql = "SELECT * FROM `user` WHERE `u_name` = '".$username."' ";
	$Quser = $db->query_select_one($sql);

	if( $Quser ){
		$sql = "SELECT * FROM `user_info` WHERE `u_id` = ".$Quser['u_id'].";";
		$Quser_info = $db->query_select_one($sql);

		if($Quser_info['ui_advsecurity'] == 1){
			// if user enabled advanced security --> check password hash
			if($Quser['u_pass'] == hash("sha256", $password) ){

				$_SESSION['u_name'] = $Quser['u_name'];
				$_SESSION['u_id'] = $Quser['u_id'];
				$_SESSION['admin'] = ($Quser['u_type'] == IDADMIN);
				$_SESSION['staff'] = ($Quser['u_type'] == IDSTAFF);
				$_SESSION['u_type'] = $Quser['u_type'];
				$_SESSION['u_auth'] = 1 << $Quser['u_type'];
				$_SESSION['shop_id'] = $Quser['shop_id'];
				$_SESSION['ui_phone'] = $Quser_info['ui_phone'];
				return true;
			}
			else{
//				echo"yo";
				return false;
			}
		}
		else{
			// if user disabled advanced security --> check info match --> using phone
			if($Quser_info['ui_phone'] == $phone_info){

				$_SESSION['u_name'] = $Quser['u_name'];
				$_SESSION['u_id'] = $Quser['u_id'];
				$_SESSION['admin'] = ($Quser['u_type'] == IDADMIN);
				$_SESSION['staff'] = ($Quser['u_type'] == IDSTAFF);
				$_SESSION['u_type'] = $Quser['u_type'];
				$_SESSION['u_auth'] = 1 << $Quser['u_type'];
				$_SESSION['shop_id'] = $Quser['shop_id'];
				$_SESSION['ui_phone'] = $Quser_info['ui_phone'];
				return true;
			}
			else{
//				echo"yo";
				return false;
			}
		}
	}
	else
		return false;
}

function checkAuth($page_auth) {
	return ($_SESSION['u_auth'] & $page_auth) != 0;
}

function is_login() {
	// $_SESSION variable was set after user loginned
	return isset($_SESSION['u_name']);
}

function is_admin() {
	return checkAuth(AUADMIN);
}

function is_staff() {
	// boss (admin) is included in staff
	return checkAuth(AUSTAFF | AUADMIN);
}

function is_above_customer() {
	// check registered and activated user
	return checkAuth(AUCUSTOMER | AUSTAFF | AUADMIN);
}

function is_headquarters_staff() {
	return $_SESSION['shop_id'] == -1;
}

function is_topboss() {
	return is_headquarters_staff() && is_admin();
}

function is_same_store() {
	// check if this page and user is belong the same store
	global $_shopID;
	return $_SESSION['shop_id'] == $_shopID;
}

function set_shopID() {
	if ( isset($_GET['shop_id']) && strlen($_GET['shop_id']) != 0 ) {
		if (!preg_match("/(^-1$)|(^0$)|(^[1-9][0-9]*$)/", $_GET['shop_id'])) {
			$_SESSION['error_message'] = "Unvalid shop_id.";
			return false;
		}

		if (isset($_SESSION['GET_shop_id']) && $_SESSION['GET_shop_id'] == $_GET['shop_id']) {
			return true;
		} else {
			global $db;
			$sql = "SELECT * FROM `shop` WHERE `shop_id` = " . $_GET['shop_id'];
			$Quser = $db->query_select_one($sql);

			if ($Quser) {
				$_SESSION['GET_shop_id'] = $_GET['shop_id'];
				$_SESSION['shop_name'] = $Quser['shop_name'];
				return true;
			} else {
				$_SESSION['error_message'] = "No such shop_id.";
				return false;
			}
		}
	}
	else {
		if (isset($_SESSION['GET_shop_id']))
			return true;
		else {
			$_SESSION['error_message'] = "Not provide shop_id.";
			return false;
		}
	}
}

function not_login_redirect() {
	global $_shopID;
	if( !is_login() ) {
		header("location:login.php?shop_id=" . $_shopID);
		die('');
	}
}

function not_staff_redirect() {
	not_login_redirect();
	global $_shopID;
	if ( !(is_staff() && is_same_store()) ) {
		header("location:index.php?shop_id=" . $_shopID);
		die('');
	}
}

function not_admin_redirect() {
	not_login_redirect();
	global $_shopID;
	if ( !(is_admin() && is_same_store()) ) {
		header("location:index.php?shop_id=" . $_shopID);
		die('');
	}
}

function not_topboss_redirect() {
	not_login_redirect();
	global $_shopID;
	if ( !is_topboss() ) {
		header("location:index.php?shop_id=" . $_shopID);
		die('');
	}
}

function not_activated_redirect() {
	not_login_redirect();
	global $_shopID;
	if ( !is_above_customer() ) {
		header("location:register.php?shop_id=" . $_shopID);
		die('');
	}
}

function itemCompare($a,$b){
    if ($a['s_id'] > $b['s_id'])
        return 1;
    else if ($a['s_id'] < $b['s_id'])
        return -1;

    if ($a['m_id'] > $b['m_id'])
        return 1;
    else if ($a['m_id'] < $b['m_id'])
        return -1;

    sort($a['RO_array']);
    sort($b['RO_array']);
    if ($a['RO_array'] > $b['RO_array'])
        return 1;
    else if ($a['RO_array'] < $b['RO_array'])
        return -1;

    sort($a['AI_array']);
    sort($b['AI_array']);
    if ($a['AI_array'] > $b['AI_array'])
        return 1;
    else if ($a['AI_array'] < $b['AI_array'])
        return -1;

    return 0;
}

function makeSummary($share_array){
	$all_items = array();
	foreach ($share_array as $share){
		$all_items = array_merge($all_items, $share['items_array']);
	}
	usort($all_items, "itemCompare");

	$i = -1;
	$sum_array = array();
	foreach($all_items as $item){
		if($i == -1){
			array_push($sum_array, $item);
			$i++;
		}
		else{
			if( itemCompare($sum_array[$i], $item) == 0){
				$sum_array[$i]['quantity'] += $item['quantity'];
			}
			else{
				array_push($sum_array, $item);
				$i++;
			}
		}
	}

	return $sum_array;

}


function order_detail($o_id){
	global $db;
		$sql = "SELECT * FROM `share` WHERE `o_id` = ".$o_id ;
		//echo $sql . "\nyoooo\n";
		$s_result = $db->query($sql);
		$share_info = array();
		$order_total = 0;
		while($share = $db->fetch_array($s_result)){
			$sql = "SELECT * FROM `share_item` WHERE `sh_id` = ".$share['sh_id'];
			//echo $sql . "\nyoooo\n";
			$sh_i_result = $db->query($sql);
			$item_info = array();
			$counting_total = 0;
			while($item = $db->fetch_array($sh_i_result)){

				$sql = "SELECT * FROM `main` WHERE `m_id` = ".$item['m_id'];
				$m_result = $db->query($sql);
				$main = $db->fetch_array($m_result);

				$item_total = 0;
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
					$item_total += $ro['price'];
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
					$item_total += $ai['price'];
					array_push($ai_info, $outAi);
				}
				$item_total += $main['price'];
				$item_total = $item_total * $item['quantity'];

				$counting_total += $item_total;

				$outItem = array();
				$outItem['name'] 		= 	$main['name'];
				$outItem['main_price'] 	= 	$main['price'];
				$outItem['m_id'] 		= 	$main['m_id'];
				$outItem['s_id'] 		= 	$main['s_id'];
				$outItem['quantity'] 		= 	$item['quantity'];
				$outItem['comment'] 		= 	$item['comment'];
				$outItem['RO_array'] 	= 	$ro_info;
				$outItem['AI_array'] 		= 	$ai_info;

				array_push($item_info, $outItem);
			}
			$outShare = array();
			$outShare['total'] = $counting_total;
			$outShare['items_array'] = $item_info;
			$order_total +=  $counting_total;
			array_push($share_info, $outShare);
		}
		$outOrder['share_array'] = $share_info;
		$outOrder['summary_array'] = makeSummary($share_info);
		$outOrder['total'] = $order_total ;

	return $outOrder;
}

function log_order($o_id){
	global $db;
		$sql = "SELECT * from `orders` WHERE `o_id` = ".$o_id;
		if(! $o_result = $db->query($sql))
			die('order sql failure');
		$order = $db->fetch_array($o_result);


		$sql = "SELECT * FROM `share` WHERE `o_id` = ".$o_id ;
		//echo $sql . "\nyoooo\n";
		$s_result = $db->query($sql);
		$share_info = array();
		$order_total = 0;
		while($share = $db->fetch_array($s_result)){
			$sql = "SELECT * FROM `share_item` WHERE `sh_id` = ".$share['sh_id'];
			//echo $sql . "\nyoooo\n";
			$sh_i_result = $db->query($sql);
			$item_info = array();
			$counting_total = 0;
			while($item = $db->fetch_array($sh_i_result)){

				$sql = "SELECT * FROM `main` WHERE `m_id` = ".$item['m_id'];
				$m_result = $db->query($sql);
				$main = $db->fetch_array($m_result);
				$item_price = $main['price'];

				$sql = "SELECT * FROM `series` WHERE `s_id` = ".$main['s_id'];
				$se_result = $db->query($sql);
				$series = $db->fetch_array($se_result);

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
					$item_price += $ro['price'];
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
					$item_price += $ai['price'];
					array_push($ai_info, $outAi);
				}
				$counting_total += $main['price'];

				$counting_total = $counting_total * $item['quantity'];

				$outItem = array();
				$outItem['s_text'] 		= $series['name'];
				$outItem['m_text'] 		= 	$main['name'];
				$outItem['item_price']  = $item_price;
				//$outItem['main_price'] 	= 	$main['price'];
				//$outItem['m_id'] 		= 	$main['m_id'];
				//$outItem['s_id'] 		= 	$main['s_id'];

				$outItem['quantity'] 		= 	$item['quantity'];
				$outItem['comment'] 		= 	$item['comment'];
				$outItem['RO_array'] 	= 	$ro_info;
				$outItem['AI_array'] 		= 	$ai_info;


				// Writing into log without merging with makeSummary

				$sql = "INSERT INTO `log` (`log_id`, `o_id`, `time`, `s_text`, `m_text`, `quantity`, `price`)"
						."VALUES (NULL, '".$o_id."', '".$order['o_time']."', '".$outItem['s_text']."', '".$outItem['m_text']."', '".$outItem['quantity']."', '".$outItem['item_price']."');";

				$db->query($sql);

				//array_push($item_info, $outItem);
			}
			//$outShare = array();
			//$outShare['total'] = $counting_total;
			//$outShare['items_array'] = $item_info;
			//$order_total +=  $counting_total;
			//array_push($share_info, $outShare);
		}
		//$outOrder['share_array'] = $share_info;
		//$outOrder['summary_array'] = makeSummary($share_info);
		//$outOrder['total'] = $order_total ;

	//return $outOrder;
}

function unlog_order($o_id){
	global $db;
	$sql = "DELETE FROM `log` WHERE `o_id` = '".$o_id."'";
	$db->query($sql);
}


?>
