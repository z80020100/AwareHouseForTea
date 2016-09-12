<?php

require_once('includes/general.php');
header("Content-Type:text/html; charset=utf-8");

$_HTML = '';

$template = $twig->loadTemplate('header.html');

if(!isset($all_series))
	$all_series = array();

if(!isset($_SESSION['u_type'])){
	$_USER_IDENTITY = $_Identity[IDGUEST]['name'];
	$_USER_NAME = '訪客';
}
else{
	$_USER_IDENTITY = $_Identity[$_SESSION['u_type']]['name'];
	$_USER_NAME = $_SESSION['u_name'];
}


$_HTML .= $template->render(array(
	'username' => $_USER_NAME,
	'PAGE_TITLE' => $_PAGE_TITLE,
	'USER_IDENTITY' => $_USER_IDENTITY,
	'all_series' => $all_series,
));

//echo $_Identity[$_SESSION['u_type']]['name'];

?>