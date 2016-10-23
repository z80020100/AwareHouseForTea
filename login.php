<?php

$empty_header = True;
$empty_footer = True;

$_PAGE_TITLE = 'HappyTTea樂台茶 線上點餐系統';
require_once('includes/header.php');

$template = $twig->loadTemplate('login.html');
$message = "";
if(isset($_SESSION['u_name'])){
	session_destroy();
	$message = "已登出";
	header("refresh:1;url=login.php?shop_id=" . $_shopID);
}

if(isset($_POST['submit'])){

	if(!isset($_POST['password']))
		$_POST['password'] = '';

	if(!isset($_POST['phone']))
		$_POST['phone'] = '';

	if(user_login($_POST['username'] , $_POST['password'], $_POST['phone'], true)){
		$message = "登入成功, 兩秒後自動回到首頁";
		header("refresh:2;url=index.php?shop_id=" . $_shopID);
	}
	else
	{
		$message = "登入錯誤";
	}
}

$_HTML .= $template->render(array(
	'LOGIN_MESSAGE' => $message,
));

require_once('includes/footer.php');

?>
