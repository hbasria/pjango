<?php
namespace Pjango\Core;

require_once 'Pjango/Conf/GlobalSettings.php';
require_once 'Pjango/Core/ClassLoader.php';
require_once 'Pjango/Core/ApplicationBootstrap.php';
require_once 'Pjango/Core/Util.php';
require_once 'Pjango/Conf/Urls/Defaults.php';

class Application {
    protected static $_instance;

    protected $_environment;
    protected $_bootstrap;
    protected $_logger;
    protected $_entityManager;
    protected $_loadedModels;


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
            $this->_bootstrap = new ApplicationBootstrap($this);
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
    
    public function setEntityManager($em){
        $this->_entityManager = $em;
    }  

    public function getEntityManager(){
        return $this->_entityManager;
    }    
    
    public function getLoadedModels(){
    	return $this->_loadedModels;
    }
    
    public function setLoadedModels($param){
    	return $this->_loadedModels = $param;
    }
    
    public function run($environment = 'DEV', $options = array()){
    	define('APPLICATION_ENV',(string) $environment);
        $this->_environment = (string) $environment;
        $options['environment'] = $this->_environment;
        $this->getBootstrap()->run($options);
    }
}





