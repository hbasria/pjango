<?php
namespace Pjango\Core;

use Pjango,
    Pjango\Http\Request;

require_once 'Pjango/H2o.php';

class ApplicationBootstrap {
    protected $_application;
    protected $_connection;
    protected $_loadedModels;

    public function __construct($application){
        $this->_application = $application;
        $this->_loadedModels = array();
    }

    public function run($options = array()){
        global $urlpatterns;
                
        $classLoader = new ClassLoader('Pjango');
        $classLoader->register();
        
        $this->init_set();
        $this->init_session();        
        $this->init_sites();        
        $this->init_logging();                
        $this->init_locale();
        $this->init_apps();        
        $this->init_h2o();

        $this->init_settings();

        $urlpatterns = array();
        $rootUrls = reverse(pjango_ini_get('ROOT_URLCONF'));
        
        
        if($rootUrls){                        
            require $rootUrls;
        }
        
        $this->_application->setLoadedModels($this->_loadedModels);

        if(isset($options['console']) && $options['console'] == true){
			echo "pjango application running ".$options['environment']." mode...\n";
        }else {
            $request = new Request();
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
    	
    	$multipleSite = pjango_ini_get('MULTIPLE_SITE');
        if (is_array($multipleSite)) {
            $rootServers = $multipleSite['ROOT_SERVERS'];
            $serverNames = explode('.', $_SERVER["SERVER_NAME"]);            
            $reservedSubDomains = array('www','ftp','mail','smtp');
            
            $domain = implode('.', $serverNames);
            
            if(in_array($serverNames[0], $reservedSubDomains)){
            	$rootServer = implode('.', array_slice($serverNames, 2, count($serverNames)));
            	$serverName = implode('.', array_slice($serverNames, 1, count($serverNames)));            	
            }else {
            	$rootServer = implode('.', array_slice($serverNames, 1, count($serverNames)));
            	$serverName = implode('.', $serverNames);
            }
            
            $this->init_doctrine();
            //             aktif siteyi bul
            $site = false;
            if ($this->_connection){
            	$stmt = $this->_connection->prepare('SELECT * FROM pjango_site WHERE domain=:domain OR name=:name');
            	$stmt->execute(array('domain' => $serverName, 'name' => $serverName));
            	$site = $stmt->fetch();
            }            
            
            if ($site){
            	if(in_array($rootServer, $rootServers)){
                	// subdomain kullanılıyor
                	define('DOMAIN',false);
                	define('SUB_DOMAIN',$site['name']);
                	define('SITE_PATH', APPLICATION_PATH."/users/".SUB_DOMAIN);
                	pjango_ini_set('MEDIA_URL', '/users/'.SUB_DOMAIN.'/media');                	
                } else {                	
                	define('DOMAIN',$site['domain']);
                	define('SUB_DOMAIN',$site['name']);
                	define('SITE_PATH', APPLICATION_PATH."/users/".SUB_DOMAIN);
                	pjango_ini_set('MEDIA_URL', '/users/'.SUB_DOMAIN.'/media');
                }
                
                define('SITE_ID',$site['id']);
                pjango_ini_set('SITE_ID', $site['id']); //deprecet
                pjango_ini_set('MEDIA_ROOT', SITE_PATH.'/media'); 
                
            
                //         özel site ayarı girilmiş ise yükle
                if (is_file(SITE_PATH.'/settings.php')){
                    require  SITE_PATH.'/settings.php';
                }
                
                //uygulama ayarlarını site ayarlarının üzerine yaz
                require APPLICATION_PATH.'/settings.php';
                $GLOBALS['SETTINGS'] = array_merge($GLOBALS['SETTINGS'], $SETTINGS);                
                
            }else {
                define('SITE_PATH', APPLICATION_PATH);
                define('SITE_ID',1);
            	pjango_ini_set('SITE_ID', 1); //deprecet
                pjango_ini_set('MEDIA_URL', pjango_ini_get('SITE_URL').'/media');
            }         
            
        }else {
        	define('SITE_PATH', APPLICATION_PATH);
            if (is_file(SITE_PATH.'/settings.php')){
                require SITE_PATH.'/settings.php';
                $GLOBALS['SETTINGS'] = array_merge($GLOBALS['SETTINGS'], $SETTINGS);
            }            
            define('SITE_ID',1);
            pjango_ini_set('SITE_ID', 1); //deprecet
            $this->init_doctrine();
        }                
    }

    protected function init_locale(){
        if (pjango_ini_get('TIME_ZONE')) {
            date_default_timezone_set(pjango_ini_get('TIME_ZONE'));
        }

        //setlocale(LC_TIME, 'tr_TR');
        $lng = pjango_ini_get('LANGUAGE_CODE');

        //	Language settings
        if (isset($_GET['lng'])) {
            //FIXME check avaible languages
            $lng = $_GET['lng'];
			$_SESSION['LANGUAGE_CODE'] = $lng;
        }

        if (isset($_SESSION['LANGUAGE_CODE'])) {
            pjango_ini_set('LANGUAGE_CODE', $_SESSION['LANGUAGE_CODE']);
        }        
    }
	
	protected function init_doctrine($proxyDir = null){            
            $env = $this->_application->getEnvironment();
            $databases = pjango_ini_get('DATABASES');
            if($databases){
                require_once 'Doctrine.php';
                spl_autoload_register(array('Doctrine', 'autoload'));
                spl_autoload_register(array('Doctrine', 'modelsAutoload'));

                $doctrineManager = \Doctrine_Manager::getInstance();
                
                $doctrineManager->setAttribute(\Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, true);
                $doctrineManager->setAttribute(\Doctrine::ATTR_MODEL_LOADING, \Doctrine::MODEL_LOADING_CONSERVATIVE);
                $doctrineManager->setAttribute(\Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
                $doctrineManager->setCharset('utf8');
                $doctrineManager->setCollate('utf8_turkish_ci');  
                
                $this->_connection = $doctrineManager->openConnection($databases[$env], 'doctrine');
                $this->_connection->setCharset('utf8');
                $this->_connection->setCollate('utf8_turkish_ci');      
            }
	}
	
	protected function init_apps(){
        //INSTALLED_APPS uygulamalar için gerekli modülleri yükle
        $installedApps = pjango_ini_get('INSTALLED_APPS');
        $_installedApps = array();
        
        $languageCode = pjango_ini_get('LANGUAGE_CODE');
        
        foreach ($installedApps as $app) {
            
            $appPath = reverse($app);            
            $classLoader = new ClassLoader($app);
            $classLoader->register();   
            
            
			if($appPath){
                $_installedApps[$app]['name'] = $app;
				$_installedApps[$app]['full_path'] = $appPath;
             
                if(is_dir($appPath.'/Models')){
                    $_installedApps[$app]['model_path'] = $appPath.'/Models';
                    $this->_loadedModels[$app] = \Doctrine::loadModels($appPath.'/Models');
                }

                if(is_dir($appPath.'/Templates')){
                    $_installedApps[$app]['template_path'] = $appPath.'/Templates/';
                    $GLOBALS['SETTINGS']['TEMPLATE_DIRS'][] = $appPath.'/Templates/';
                }

                $tmpPath = $appPath.'/Locale/'.$languageCode.'/LC_MESSAGES/messages.po';
                if(is_file($tmpPath)){
		        	$_installedApps[$app]['lang_file'] = $tmpPath;
                    $GLOBALS['SETTINGS']['LOCALE_PATHS'][] = $tmpPath;
                }

                $tmpPath = $appPath.'/Admin.php';
		        if(is_file($tmpPath)){
                    $_installedApps[$app]['admin_file'] = $tmpPath;
                    require_once $tmpPath;
                }

                $tmpPath = $appPath.'/Templatetags.php';
		        if(is_file($tmpPath)){
                    $_installedApps[$app]['templatetags'] = $tmpPath;
                    require_once $tmpPath;
                }
		        
                $tmpPath = $appPath.'/Forms.php';
                if(is_file($tmpPath)){
                    $_installedApps[$app]['forms'] = $tmpPath;
                    require_once $tmpPath;
		        }      
            }
        }
        
        $tmpPath = SITE_PATH.'/locale/'.$languageCode.'/LC_MESSAGES/messages.po';
        $GLOBALS['SETTINGS']['LOCALE_PATHS'][] = $tmpPath;        
        
        $isDebug = pjango_ini_get('DEBUG');
        $cacheFile = sprintf('%s/cache/lang_%s_%s.cache',SITE_PATH, SITE_ID, $lng);
        Pjango\PTrans::init($cacheFile, $isDebug);        
        
        pjango_ini_set('_INSTALLED_APPS', $_installedApps);        
	}

    protected function init_h2o(){
    	$h2oConfig = pjango_ini_get('H2O_CONFIG');
    	if(!is_array($h2oConfig)) $h2oConfig = array();
    	
        $h2oConfig['searchpath'] = pjango_ini_get('TEMPLATE_DIRS');
        pjango_ini_set('H2O_CONFIG', $h2oConfig);        
    }

    protected function init_logging(){
        require_once 'Log.php';
        $conf = pjango_ini_get('LOGGING');
        $logger = \Log::singleton($conf['handler'], SITE_PATH."/".$conf['name'], $conf['ident'], $conf);
        $this->_application->setLogger($logger);
    }

    protected function init_settings(){
        if(pjango_ini_get('DATABASES')){
            $siteId = pjango_ini_get('SITE_ID');
            
            try {
    	        $settings = \Doctrine_Query::create()
                    ->from('Settings s')
                    ->where('s.site_id = ?', $siteId)
                    ->execute();

        	        foreach ($settings as $setting_item) {
        	            pjango_ini_set($setting_item->name, $setting_item->value);
        	        }
    	  
    	    } catch (Exception $e) {}
	    }
	}

}