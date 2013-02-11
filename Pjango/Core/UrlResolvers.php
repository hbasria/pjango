<?php

namespace Pjango\Core;

function get_callable($lookup_view, $can_fail=false){
	$parts = explode('\\', $lookup_view);			
	$partsSize = sizeof($parts);
	
	$methodName = $parts[$partsSize-1];
	$className = $parts[$partsSize-3].$parts[$partsSize-2];
	$pathName = implode(DIRECTORY_SEPARATOR, array_slice($parts, 0, $partsSize-1)).".php";

// 	echo $lookup_view."\n";
	
	require_once($pathName);
	
	$callable = array($className, $methodName ); 

	if( @is_callable( $callable ) === true ){ 
		return $callable;
	}else {
		return false;
	}
}





