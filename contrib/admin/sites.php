<?php 
class AdminSite {
	public $_registry = array();
	
	public function __construct(){
		
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

$site = new AdminSite();