<?php
require_once 'Doctrine.php';
require_once 'pjango/h2o.php';
require_once 'pjango/utils/translation.php';
require_once 'pjango/core/urlresolvers.php';
require_once 'pjango/contrib/admin/sites.php';
require_once 'pjango/http.php';
require_once 'pjango/utils/messages.php';
require_once 'pjango/core/admin.php';

$GLOBALS['SETTINGS'] = $SETTINGS;
function pjango_ini_set($varname, $newvalue) {
	return $GLOBALS['SETTINGS'][$varname] = $newvalue;	
}

function pjango_ini_get($param) {
	if (isset($GLOBALS['SETTINGS'][$param]))
		return $GLOBALS['SETTINGS'][$param];
	else return false;	
}		

class Pjango_Application_Bootstrap {
	protected $_application;
	
	public function __construct($application){
		$this->_application = $application;
	}
	
	public function run($options = array()){
		global $urlpatterns;
		


		$this->init_set();
		$this->init_logging();
		$this->init_session();
		$this->init_sites();
		$this->init_doctrine();
		$this->init_locale();		
		$this->init_apps();
		$this->init_models();
		$this->init_settings();
		$this->init_h2o();
		
		
		//	ROOT_URLCONF daki url dosyasını yükle
		$urlpatterns = array();
		$rootUrls = reverse(pjango_ini_get('ROOT_URLCONF'));
		
		if($rootUrls){
		    require $rootUrls;
		}			
		
		## çoklu dil desteğini yükle ## 
		Translation::trans();
		
		if(isset($options['console']) && $options['console'] == true){
			echo "pjango application running ".$options['environment']." mode...\n";
		}else {
			$request = new HttpRequest();
			$request->request();			
		}
		
	}	
	
	protected function init_set(){		
		if (pjango_ini_get('DEBUG') === true) {
			error_reporting(E_ALL & ~E_NOTICE);
			ini_set("display_errors", 1);
		}else {
			ini_set("display_errors", 0);
		}
	}	
	
	protected function init_session(){
		session_start();		
		if(!isset($_SESSION['user'])) $_SESSION['user'] = serialize(array());
	}
	
	protected function init_sites(){
	    if (isset($_GET['site'])) {
	        $_SESSION['SITE_ID'] = $_GET['site'];
	    }
	    
	    if (isset($_SESSION['SITE_ID'])) {
	        pjango_ini_set('SITE_ID', $_SESSION['SITE_ID']);
	    }
	}	
	
	protected function init_locale(){
		if (pjango_ini_get('TIME_ZONE')) {
			date_default_timezone_set(pjango_ini_get('TIME_ZONE'));
		}
		
		//setlocale(LC_TIME, 'tr_TR');
		
		//	Language settings
		if (isset($_GET['lng'])) {
			//FIXME geçerli dilleri kontrol et
			$_SESSION['LANGUAGE_CODE'] = $_GET['lng'];
		}
		
		if (isset($_SESSION['LANGUAGE_CODE'])) {
			pjango_ini_set('LANGUAGE_CODE', $_SESSION['LANGUAGE_CODE']);
		}
	}
	
	protected function init_doctrine(){
		spl_autoload_register(array('Doctrine', 'autoload'));
		spl_autoload_register(array('Doctrine', 'modelsAutoload'));
		
		$doctrineManager = Doctrine_Manager::getInstance();
		$doctrineManager->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, true);
		$doctrineManager->setAttribute(Doctrine::ATTR_MODEL_LOADING, Doctrine::MODEL_LOADING_CONSERVATIVE);
		$doctrineManager->setAttribute(Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
		$doctrineManager->setCharset('utf8');
		$doctrineManager->setCollate('utf8_turkish_ci');
		
		$conn = $doctrineManager->openConnection($this->getDSN(), 'doctrine');
		$conn->setCharset('utf8');
		$conn->setCollate('utf8_turkish_ci');
	}
	
	protected function init_apps(){
    	//INSTALLED_APPS uygulamalar için gerekli modülleri yükle
		$installedApps = pjango_ini_get('INSTALLED_APPS');
		$_installedApps = array();
		
		
		
		foreach ($installedApps as $app) {
			$languageCode = pjango_ini_get('LANGUAGE_CODE');
			$appPath = reverse($app);
			
			if($appPath){
				$_installedApps[$app]['name'] = $app;
				$_installedApps[$app]['full_path'] = $appPath;
			    
				if(is_dir($appPath.'/models')){
					$_installedApps[$app]['model_path'] = $appPath.'/models';
		        }
		        
		        if(is_dir($appPath.'/templates')){
		        	$_installedApps[$app]['template_path'] = $appPath.'/templates';
		        	$GLOBALS['SETTINGS']['TEMPLATE_DIRS'][] = $appPath.'/templates';
		        }
		        
		        $tmpPath = $appPath.'/locale/'.$languageCode.'/LC_MESSAGES/messages.po';
		        if(is_file($tmpPath)){
		        	$_installedApps[$app]['lang_file'] = $tmpPath;
		        	$GLOBALS['SETTINGS']['LOCALE_PATHS'][] = $tmpPath;
		        }
		        
			    $tmpPath = $appPath.'/admin.php';
		        if(is_file($tmpPath)){
		            $_installedApps[$app]['admin_file'] = $tmpPath;
		            require_once $tmpPath;
		        }
		        
			   $tmpPath = $appPath.'/templatetags.php';
		        if(is_file($tmpPath)){
		            $_installedApps[$app]['templatetags'] = $tmpPath;
		            require_once $tmpPath;
		        }
		        
				$tmpPath = $appPath.'/forms.php';
		        if(is_file($tmpPath)){
		            $_installedApps[$app]['forms'] = $tmpPath;
		            require_once $tmpPath;
		        }      
			}    
		}	

		pjango_ini_set('_INSTALLED_APPS', $_installedApps);		
	}
	
	protected function init_models(){
		$_installedApps = pjango_ini_get('_INSTALLED_APPS');
		$loadedModels = array();
		foreach ($_installedApps as $app) {
			if(isset($app['model_path'])){
		        $loadedModels[$app['name']] = Doctrine::loadModels($app['model_path']);		
			}
		}		
	}
	
	protected function init_h2o(){
		$h2oConfig = array('template_dirs' => pjango_ini_get('TEMPLATE_DIRS'));		
		pjango_ini_set('H2O_CONFIG', $h2oConfig);
	}
	
	protected function init_logging(){
	    $loggerFile = reverse('Log');
	    
	    if ($loggerFile) {
	        require_once $loggerFile;
	        
	        $conf = pjango_ini_get('LOGGING');
	        
	        $logger = Log::singleton($conf['handler'], $conf['name'], $conf['ident'], $conf);	        
	        $this->_application->setLogger($logger);
	    }
	}	
	
	protected function init_settings(){
	    $siteId = pjango_ini_get('SITE_ID');
	    
	    try {
	        $settings = Doctrine_Query::create()
	        ->from('Settings s')
	        ->where('s.site_id = ?', $siteId)
	        ->execute();	   

	        foreach ($settings as $setting_item) {
	            pjango_ini_set($setting_item->name, $setting_item->value);
	        }	        
	        
	    } catch (Exception $e) {}
	}
	
	
	
	
	protected function getDSN() {
		$env = $this->_application->getEnvironment();
		
		$databases = pjango_ini_get('DATABASES');
		
		$confArr = $databases[$env];
		$dsn = '';
		
		//sqlite:////full/unix/path/to/file.db?mode=0666
		if($confArr['ENGINE'] == 'sqlite3'){
			$dsn = 'sqlite:///'.$confArr['NAME'].'?mode=0666';
		}elseif ($confArr['ENGINE'] == 'mysql'){
		   $dsn = $confArr['ENGINE'].'://'.$confArr['USER'].':'.$confArr['PASSWORD'].'@'.$confArr['HOST'].'/'.$confArr['NAME'];	
		}
		
		return $dsn;
	}
	
	
	
}


class Pjango_Application {
	protected static $_instance;
	
	protected $_environment;
	protected $_bootstrap;
	protected $_logger;
	
	
	public function __construct(){
		
	}
	
    public static function getInstance(){
        if ( ! isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }	
    
    public function getBootstrap(){
        if (null === $this->_bootstrap) {
            $this->_bootstrap = new Pjango_Application_Bootstrap($this);
        }
        return $this->_bootstrap;
    }    
    
    public function getEnvironment() {
        return $this->_environment;
    }    
    
    public static function getLogger($cls = 'pjango'){
        $pa = self::getInstance();
        $pa->_logger->setIdent($cls);
        return $pa->_logger;
    }	

    public function setLogger($logger){
        $pa = self::getInstance();
        $pa->_logger = $logger;        
    }    
    
    public function run($environment, $options = array()){
    	$this->_environment = (string) $environment;
    	
    	$options['environment'] = $this->_environment;    	
        $this->getBootstrap()->run($options);
    }    
}

