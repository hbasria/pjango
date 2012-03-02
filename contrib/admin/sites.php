<?php 
class AdminSite {
	protected static $_instance;
	public $_registry = array();
	
	public function __construct(){
		
	}
	
    public static function getInstance(){
        if ( ! isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }		
	
	public function register($model_or_iterable, $admin_class=null){
		
		/*if (is_null($admin_class)) {
			$admin_class = ModelAdmin;
		}
		
		/*
		if ($model_or_iterable instanceof ModelAdmin){
			
		}else {
			
		}*/
		
		$this->_registry[$model_or_iterable] = $admin_class;
		 
		
	}
}

$site = AdminSite::getInstance();