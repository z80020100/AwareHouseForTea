<?php

$_PAGE_TITLE = '帳號新增';
require_once('includes/header.php');

not_topboss_redirect();

$template = $twig->loadTemplate('top_register.html');

if (isset($_POST['employer'])) {
  $sc_return = shop_create($_POST['shopname'], $_POST['shopaddress'], $_POST['shoptel']);
  if ($sc_return == -1) {
    $message = "店家創立失敗";
  } else {
    $userRegInfo = array(
			'phone' => $_POST['phone'],
      'advsecurity' => $_POST['enablepass'] == 'on' ? 1 : 0,
      'utype' => IDADMIN
		);

    $uc_return = user_create($_POST['username'] , $_POST['userpass'], $userRegInfo, $sc_return);
    if( $uc_return != -1) {
			$message = "帳號創立成功";
    } else {
      "帳號創立失敗";
    }
  }
}

if (isset($_POST['employee'])) {
  $userRegInfo = array(
    'phone' => $_POST['phone'],
    'advsecurity' => $_POST['enablepass'] == 'on' ? 1 : 0,
    'utype' => IDSTAFF
  );

  $uc_return = user_create($_POST['username'] , $_POST['userpass'], $userRegInfo, -1);
  if( $uc_return != -1) {
    $message = "帳號創立成功";
  } else {
    "帳號創立失敗";
  }
}

$_HTML .= $template->render(array(

));

require_once('includes/footer.php');

?>
