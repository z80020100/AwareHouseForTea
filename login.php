<?php


$_PAGE_TITLE = '早餐店點餐系統';
require_once('includes/header.php');

$template = $twig->loadTemplate('login.html');
$message = "";
if(isset($_SESSION['u_name'])){
	session_destroy();
	$message = "已登出";
	header("refresh:1;url=index.php?shop_id=" . $_shopID);
}
//$message = hash("sha256", "test1234");
if(isset($_POST['submit'])){

	if(!isset($_POST['password']))
		$_POST['password'] = '';

	if(!isset($_POST['phone']))
		$_POST['phone'] = '';


	if(  user_login($_POST['username'] , $_POST['password'], $_POST['phone']) ){
		if($_SESSION['shop_id'] == -1 ){
			$message = "成功登入總店, 兩秒後自動進入總店控制台";
			header("refresh:2;url=edit_menu.php?shop_id=" . $_shopID);
		}
		else if($_SESSION['admin'] == 1){
			$message = "登入成功, 兩秒後自動進入老闆控制台";
			header("refresh:2;url=listorder.php?shop_id=" . $_shopID);
		}
		else{
			$message = "登入成功, 兩秒後自動回到首頁";
			header("refresh:2;url=index.php?shop_id=" . $_shopID);
		}
	}
	else
	{
		$message = "登入錯誤";
	}
}

if(isset($_GET['admin'] )){
	$_HTML .= $template->render(array(
		'ADMIN_LOGIN' => 'admin',
		'LOGIN_MESSAGE' => $message,
	));
}
else{
	$_HTML .= $template->render(array(
		'LOGIN_MESSAGE' => $message,
		'SHOP_ID' => $_shopID,
	));
}

require_once('includes/footer.php');

?>
