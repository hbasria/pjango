<?php

function render_to_response($templateFile, $templateArr, $isReturn = false){

	$templateArr = array_merge($templateArr, $GLOBALS);
	
	if (isset($_SESSION['user']['id'])){
	   $templateArr['user'] = $_SESSION['user'];
	}
    
	
	
	$template = new H2o($templateFile, $GLOBALS['H2O_CONFIG']);
    if ($isReturn){
    	return $template->render($templateArr);
    }
    
    echo $template->render($templateArr);
}

function redirect($to){
	
}


