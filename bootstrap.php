<?php
require_once 'pjango/utils/translation.php';
require_once 'pjango/core/urlresolvers.php';
require_once 'Messages.php';			

		
//if ($GLOBALS['DEBUG'] === true) {
	error_reporting(E_ALL & ~E_NOTICE);
	ini_set("display_errors", 1);
//}else {
//	ini_set("display_errors", 0);
//}





if (isset($GLOBALS['TIME_ZONE'])) {
	date_default_timezone_set($GLOBALS['TIME_ZONE']);
}

//setlocale(LC_TIME, 'tr_TR');

session_start();

//Language settings
if (isset($_GET['lng'])) {
	//FIXME geçerli dilleri kontrol et
	$_SESSION['LANGUAGE_CODE'] = $_GET['lng'];
}

if (isset($_SESSION['LANGUAGE_CODE'])) {
	$GLOBALS['LANGUAGE_CODE'] = $_SESSION['LANGUAGE_CODE'];
}



if(!isset($_SESSION['user'])) $_SESSION['user'] = 0;

require_once 'Doctrine.php';	
require_once 'h2o.php';	

spl_autoload_register(array('Doctrine', 'autoload'));
spl_autoload_register(array('Doctrine', 'modelsAutoload'));

$doctrineManager = Doctrine_Manager::getInstance();
$doctrineManager->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, true);
$doctrineManager->setAttribute(Doctrine::ATTR_MODEL_LOADING, Doctrine::MODEL_LOADING_CONSERVATIVE);
$doctrineManager->setAttribute(Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
$doctrineManager->setCharset('utf8');
$doctrineManager->setCollate('utf8_turkish_ci');

$conn = $doctrineManager->openConnection(get_dsn($GLOBALS['APPLICATION_ENV']), 'doctrine');
$conn->setCharset('utf8');
$conn->setCollate('utf8_turkish_ci');


$GLOBALS['LOADED_MODELS'] = Doctrine::getLoadedModels();



//INSTALLED_APPS uygulamalar için gerekli modülleri yükle
$GLOBALS['_INSTALLED_APPS'] = array();
foreach ($GLOBALS['INSTALLED_APPS'] as $app) {
	
	$appPath = reverse($app);
	
	if($appPath){
		$GLOBALS['_INSTALLED_APPS'][$app]['name'] = $app;
		$GLOBALS['_INSTALLED_APPS'][$app]['full_path'] = $appPath;
	    
		if(is_dir($appPath.'/models')){
			$GLOBALS['_INSTALLED_APPS'][$app]['model_path'] = $appPath.'/models';
        }
        
        if(is_dir($appPath.'/templates')){
        	$GLOBALS['_INSTALLED_APPS'][$app]['template_path'] = $appPath.'/templates';
        	$GLOBALS['TEMPLATE_DIRS'][] = $appPath.'/templates';
        }
        
        $tmpPath = $appPath.'/locale/'.$GLOBALS['LANGUAGE_CODE'].'/LC_MESSAGES/messages.po';
        if(is_file($tmpPath)){
        	$GLOBALS['_INSTALLED_APPS'][$app]['lang_file'] = $tmpPath;
        	$GLOBALS['LOCALE_PATHS'][] = $tmpPath;
        }
        
	    $tmpPath = $appPath.'/admin.php';
        if(is_file($tmpPath)){
            $GLOBALS['_INSTALLED_APPS'][$app]['admin_file'] = $tmpPath;
            require_once $tmpPath;
        }
        
	   $tmpPath = $appPath.'/templatetags.php';
        if(is_file($tmpPath)){
            $GLOBALS['_INSTALLED_APPS'][$app]['templatetags'] = $tmpPath;
            require_once $tmpPath;
        }
	}    
}


//modelleri yükle
Doctrine::loadModels(MODELS_PATH);
foreach ($GLOBALS['_INSTALLED_APPS'] as $app) {
	if(isset($app['model_path'])){
        Doctrine::loadModels($app['model_path']);		
	}
}

//ROOT_URLCONF daki url dosyasını yükle
$urlpatterns = array();
$rootUrls = reverse($GLOBALS['ROOT_URLCONF']);

if($rootUrls){
    require $rootUrls;
}

## dil dosyalarını yükle ## 
include_once 'POParser.php';

if(is_file(APPLICATION_PATH.'/cache/lang_'.$GLOBALS['LANGUAGE_CODE'].'.cache')){
	$content = file_get_contents(APPLICATION_PATH.'/cache/lang_'.$GLOBALS['LANGUAGE_CODE'].'.cache');
	$GLOBALS['LANG'] = unserialize($content);
}else {
	$pp = new POParser();
	$GLOBALS['LANG'] = array();
	foreach ($GLOBALS['LOCALE_PATHS'] as $langFile) {
	    $res = $pp->parse($langFile);
	    
	    foreach ($res[1] as $entry) {	        
	        if (isset($entry['msgid'])){
	            $GLOBALS['LANG'][encode_lang_key($entry['msgid'])] = $entry;
	        }       
	    }
	}
	
	file_put_contents(APPLICATION_PATH.'/cache/lang_'.$GLOBALS['LANGUAGE_CODE'].'.cache', serialize($GLOBALS['LANG']));   
}

/*
## veritabanındaki ayarları yükle
if (Doctrine::getTable('Settings')){
	$q = Doctrine_Query::create()
	    ->from('Settings s');
	            
	$results = $q->fetchArray();
	    
	foreach ($results as $row) {
	    $GLOBALS['SETTINGS'][$row['name']]= $row['value'];
	}
}*/



$GLOBALS['H2O_CONFIG']['template_dirs'] = $GLOBALS['TEMPLATE_DIRS'];









//FIXME bunları bir class ta toola
class ImportError extends Exception {}		
class ViewDoesNotExist extends Exception {}	






$requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

$parts = explode('/', $requestUri);
array_shift($parts);
$requestUri = implode('/', $parts);
		



$match = preg_match('/'.str_replace('/', '\/', 'admin/').'/', $requestUri, $params);

//admin sayfaları için login kontrolü
if($match){
	if (isset($_SESSION['user']['is_staff']) && $_SESSION['user']['is_staff'] == 1){
		//devam
	}else {
		echo '<meta http-equiv="Refresh" content="0;URL='.$GLOBALS['SITE_URL'].$GLOBALS['SETTINGS']['LOGIN_URL'].'">';
		exit();
	}


}



$regexURLPattern = NULL;

foreach ($urlpatterns as $urlpattern){		
	if($urlpattern->resolve($requestUri)){
		$regexURLPattern = $urlpattern;
		break;
	}
}

if ($regexURLPattern !== NULL) {
	call_user_func_array($regexURLPattern->_get_callback(), $regexURLPattern->get_default_args());
}else{
	echo "SAYFA YOK";
}




















function get_dsn($key = 'TEST') {
	$confArr = $GLOBALS['DATABASES'][$key];
	$dsn = '';
	
	//sqlite:////full/unix/path/to/file.db?mode=0666
	if($confArr['ENGINE'] == 'sqlite3'){
		$dsn = 'sqlite:///'.$confArr['NAME'].'?mode=0666';
	}elseif ($confArr['ENGINE'] == 'mysql'){
	   $dsn = $confArr['ENGINE'].'://'.$confArr['USER'].':'.$confArr['PASSWORD'].'@'.$confArr['HOST'].'/'.$confArr['NAME'];	
	}
	
	return $dsn;
}