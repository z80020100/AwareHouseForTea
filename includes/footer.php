<?php



$template = $twig->loadTemplate('footer.html');


$_HTML .= $template->render(array(
	'PAGE_TITLE' => $_PAGE_TITLE,
));


echo $_HTML;

?>