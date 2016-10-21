<?php

$_PAGE_TITLE = '創造帳號';
require_once('includes/header.php');

$page_verify = false;
$verification_success = false;
if(isset($_SESSION['u_name'])){
	if($_SESSION['u_type'] == 0) 	// NOT ACTIVATED
		$page_verify = true;
	else if(is_admin()){  			// is the user admin?
		$page_verify = false;
	}
	else{
		header("location:index.php?shop_id=" . $_shopID);
		die('');
	}
}

if($page_verify == true){
	$template = $twig->loadTemplate('register_verification.html');
	$message = "";
}
else{
	$template = $twig->loadTemplate('register.html');
	$message = "";
}


//not_admin_redirect();

if(!isset( $_POST['userpass']))
	 $_POST['userpass'] = '';

if(!isset( $_POST['verification']))
	 $_POST['verification'] = '';

if(!isset($_POST['phone']))
	$_POST['phone'] = '';

if(!isset($_POST['occupation']))
	$_POST['occupation'] = '';

if(isset($_POST['submit'])){
	if($page_verify == false){

		$userRegInfo = array(
			'phone' => $_POST['phone'],
		);

		if(is_admin()){
			$userRegInfo['advsecurity'] = $_POST['enablepass'] == 'on' ? 1 : 0;
			$userRegInfo['utype'] = $_POST['utype'];
		}
		else{
			$userRegInfo['advsecurity'] = $_POST['enablepass'] == 'on' ? 1 : 0;
			$userRegInfo['utype'] = IDGUEST;
		}

		$uc_return = user_create($_POST['username'] , $_POST['userpass'], $userRegInfo, $_shopID);
		if( $uc_return != -1 && is_admin() ){
			$message = "帳號創立成功";
			if($userRegInfo['utype'] == IDGUEST)
			{
				$vercode = user_vercode(false, $_POST['username'], $uc_return);
				$message .= "，傳送驗證碼到".$_POST['phone'];
				send_sms($_POST['phone'], '你的驗證碼是:'.$vercode['hash'] );
			}
			//header("refresh:1;url=register.php");
		}
		else if( $uc_return != -1 ){
			$message = "傳送驗證碼中..";

			user_login($_POST['username'] , $_POST['userpass'], $_POST['phone']);
			$vercode = user_vercode();
			//echo $vercode['hash'];
			send_sms($_POST['phone'], '你的驗證碼是:'.$vercode['hash'] );
			header("refresh:1;url=register.php?shop_id=" . $_shopID);
		}
		else
		{
			$message = "帳號創立失敗";
		}
	}
	else{
		$vercode = user_vercode();
		if($_POST['verification'] == $vercode['hash'] && $vercode['updated'] == false){
			$sql = "UPDATE `user` SET `u_type` = '1' WHERE `user`.`u_id` = '".$_SESSION['u_id']."';";
			$db->query($sql);
			$message = "驗證碼正確，即將轉到菜單..";
			$verification_success = true;
			$_SESSION['u_type'] = 1;
			$_SESSION['u_auth'] = 1 << 1;
			header("refresh:1;url=index.php?shop_id=" . $_shopID);
		}
		else{
			if($vercode['updated'] == true)
				$message = "驗證碼已經失效，請重新傳送";
			else
				$message = "驗證碼錯誤";
		}

	}
}



if(is_admin()){
	$register_title = "創造帳號";
	$register_admin = true;
}
else{
	$register_title = "註冊";
	$register_admin = false;
}

$_HTML .=  $template->render(array(
	'USERNAME' => (isset($_SESSION['u_name']))?$_SESSION['u_name']:'',
	'USERPHONE' => (isset($_SESSION['ui_phone']))?$_SESSION['ui_phone']:'',
	'REGISTER_TITLE' => $register_title,
	'REGISTER_MESSAGE' => $message,
	'REGISTER_ADMIN' => $register_admin,
	'all_utype' => $_Identity,
	'VERIFICATION_SUCCESS' => $verification_success,
));

require_once('includes/footer.php');
?>
