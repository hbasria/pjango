<?php
//responseType
//    TYPE_JSARRAY
//    TYPE_JSON
//    TYPE_XML
//    TYPE_TEXT
//    TYPE_HTMLTABLE
//TODO : $responseType = "text/html"
function render_to_response($templateFile, $templateArr, $isReturn = false){
    $responseType = isset($_GET['responsetype']) ? $_GET['responsetype'] : 'html';
    
    if ($responseType == "json"){
    	if (isset($templateArr['cl'])){
    		$templateArr['result_list'] = $templateArr['cl']->result_list->toArray();
    	}

    	echo json_encode($templateArr);
    }else{    	
    	$templateArr = array_merge($templateArr, $GLOBALS['SETTINGS']);
    	
    	if (isset($_SESSION['user']['id'])) $templateArr['user'] = get_user();
    	
    	$h2oConfig = pjango_ini_get('H2O_CONFIG');
	
		$template = new H2o($templateFile, $h2oConfig);
	    if ($isReturn){
	    	return $template->render($templateArr);
	    }
	    
	    
	    $templateArr['request'] = $_REQUEST;
    	    	
    	echo $template->render($templateArr);
    }
    
    	
}

function redirect($to){
	
}


