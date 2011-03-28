<?php

require_once 'pjango/core/urlresolvers.php';

function url($regex, $view, $name = NULL, $prefix = NULL) {
	
	if (!is_string($view)) {
		//daha sonraki fonksiyonlarda kullanılabilir
	}else {		
		if (strlen($view) < 1) {
			throw new ImproperlyConfigured(sprintf('Empty URL pattern view name not permitted (for pattern %s)', $regex));
		}
		
		if ($prefix != NULL) {
			$view = $prefix . '.' . $view;
		}
		
		return new RegexURLPattern($regex, $view);
	}
}

function patterns($prefix){
	global $urlpatterns;
	
	$patternList = $urlpatterns;
	
	$numArgs = func_num_args();
	$getArgs = func_get_args();
	
	for($i=1; $i < $numArgs; $i++){
		$tmpArg = $getArgs[$i];
		
		$t = url($tmpArg[0], $tmpArg[1]);
				
		if ($t instanceof RegexURLPattern) {
			$t->add_prefix($prefix);
			$patternList[] = $t;
		}
	}
	
	return $patternList;	
}