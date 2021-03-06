<?php
require_once 'Pjango/Core/UrlResolvers.php';

use Pjango\Core\RegexURLPattern;

function url($regex, $view, $name = NULL, $prefix = NULL) {
	
	if (!is_string($view)) {
		//TODO daha sonraki fonksiyonlarda kullanılabilir
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

function pjango_ini_set($varname, $newvalue) {
    return $GLOBALS['SETTINGS'][$varname] = $newvalue;
}

function pjango_ini_get($param) {
    if (isset($GLOBALS['SETTINGS'][$param]))
    return $GLOBALS['SETTINGS'][$param];
    else return false;
}

function reverse($viewname = '') {
    $pathArr = explode('\\', $viewname);
    
    $path = implode("/", $pathArr);

    $includePaths = explode(PATH_SEPARATOR, get_include_path());
    $includePaths = array_reverse($includePaths);

    $returnPath = false;
    foreach ($includePaths as $includePathItem) {
        $tmpPath = $includePathItem.'/'.$path;
        
        

        if(is_dir($tmpPath)){
            $returnPath = $tmpPath;
        }

        if(is_file($tmpPath.'.php')){
            $returnPath = $tmpPath.'.php';
        }
    }

    return $returnPath;
}

function is_mobile() {
	$useragent = $_SERVER['HTTP_USER_AGENT'];
	if(preg_match('/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|meego.+mobile|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
		return true;
	}
	return false;
}

function is_popup() {
	if (isset($_GET['popup'])){
		if($_GET['popup'] == '1' || $_GET['popup'] == 'true') return true;
	}	
	return false;
}

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
        
        $templateArr['user'] = User::get_current();         
        $h2oConfig = pjango_ini_get('H2O_CONFIG');
        
        $template = new H2o($templateFile, $h2oConfig);
        if ($isReturn){
            return $template->render($templateArr);
        }
         
         
        $templateArr['request'] = $_REQUEST;
        $templateArr['request']['is_mobile'] = is_mobile();
        $templateArr['is_popup'] = is_popup();

        echo $template->render($templateArr);
    }     
}

function render_to_string($templateFile, $templateArr){
	$templateArr = array_merge($templateArr, $GLOBALS['SETTINGS']);

	$templateArr['user'] = User::get_current();
	$h2oConfig = pjango_ini_get('H2O_CONFIG');

	$template = new H2o($templateFile, $h2oConfig);
	return $template->render($templateArr);	
}