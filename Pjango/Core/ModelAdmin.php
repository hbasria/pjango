<?php
namespace Pjango\Core;

class ModelAdmin {
	protected static $_instance;
    private $model = false;

    public $list_display = array();
    public $list_display_links = array();
    public $list_filter = array();
    public $list_select_related = false;
    public $list_per_page = 25;
    public $list_editable = array();
    public $search_fields = array();
    public $date_hierarchy = false;
    public $save_as = false;
    public $save_on_top = false;
    public $ordering = false;
    public $actions = array('add');
    public $row_actions = array('edit', 'delete');

    public $admin_menu = false;
    public $apps_menu = false;
    
    public $extraheads = false;
    public $third_level_navigation = false;
    
    

    public function __construct(){


    }
    
    public static function getInstance(){
    	if ( ! isset(self::$_instance)) {
    		self::$_instance = new self();
    	}
    	return self::$_instance;
    }    
    
    public function get_third_level_navigation($currentKey, $modelUrl = '', $modelId = false){
    	$retVal = false;
    	
    	if(is_array($this->third_level_navigation)){
    		for ($i = 0; $i < count($this->third_level_navigation); $i++) {
    			if($modelId){
    				$this->third_level_navigation[$i]['url'] = $modelUrl.$modelId."/".$this->third_level_navigation[$i]['key']."/";
    			}else {
    				$this->third_level_navigation[$i]['url'] = $modelUrl.'add/';
    			}
    			
    			
    			if($this->third_level_navigation[$i]['key'] == $currentKey){
    				$this->third_level_navigation[$i]['class'] = 'active';
    	
    				if (isset($this->third_level_navigation[$i+1])){
    					$this->third_level_navigation[$i+1]['class'] = 'passive after-active';
    				}
    			}
    		}
    	
    		$retVal = $this->third_level_navigation;
    	}    
    	
    	return $retVal;    
    }

}