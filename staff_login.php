<?php

$_PAGE_TITLE = '樂台茶管理系統';
require_once('includes/header.php');

$template = $twig->loadTemplate('staff_login.html');
$message = "";
if(isset($_SESSION['u_name'])){
	session_destroy();
	$message = "已登出";
	header("refresh:1;url=staff_login.php?shop_id=" . $_shopID);
}

if(isset($_POST['submit'])){

	if(!isset($_POST['password']))
		$_POST['password'] = '';

	if(!isset($_POST['phone']))
		$_POST['phone'] = '';

	if(user_login($_POST['username'] , $_POST['password'], $_POST['phone'], false)){
		if($_SESSION['shop_id'] == -1 ){
			$message = "成功登入總店, 兩秒後自動進入總店控制台";
			header("refresh:2;url=inventory.php?shop_id=" . $_SESSION['shop_id']);
		}
		else if($_SESSION['admin'] == 1){
			$message = "登入成功, 兩秒後自動進入老闆控制台";
			header("refresh:2;url=report.php?shop_id=" . $_SESSION['shop_id']);
		}
		else if ($_SESSION['staff'] == 1) {
			$message = "登入成功, 兩秒後自動回到首頁";
			header("refresh:2;url=index.php?shop_id=" . $_SESSION['shop_id']);
    }
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
