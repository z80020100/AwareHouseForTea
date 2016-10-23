<?php

if(!isset($empty_footer)){
    $empty_footer = False;
}

if($empty_footer == True){
    $template = $twig->loadTemplate('footer_empty.html');
}
else{
    $template = $twig->loadTemplate('footer.html');
}

$_HTML .= $template->render(array(
	'PAGE_TITLE' => $_PAGE_TITLE,
));


echo $_HTML;

?>