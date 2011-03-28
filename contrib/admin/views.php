<?php
require_once 'pjango/shortcuts.php';

class AdminViews {
	
	function index() {

		
		render_to_response('admin/index.html', array());
	}
	
	function settings($category = 'GENERAL') {
		$templateArr = array('admin_menu_current'=>'menu_settings',
		'admin_submenu_current'=>'submenu_settings'); 		
		
		
		if(isset($_POST['Settings'])){
			foreach ($_POST['Settings'] as $key => $value) {
				$tmpSettings = Doctrine::getTable('Settings')->find($key);
        
                if ($tmpSettings) {
                    $tmpSettings->value = $value;
                    $tmpSettings->save();
                }
			}
		}

		$q = Doctrine_Query::create()
			->select('s.category, COUNT(s.category) AS count')		
            ->from('Settings s')
            ->groupBy('s.category');
            
		$templateArr['settings_category'] = $q->fetchArray();            
		//category
		
		$q = Doctrine_Query::create()
            ->from('Settings s');
            
		if ($category){
			$q->where('s.category = ?', array($category));
			$templateArr['admin_submenu_current'] = $category;            
		}            
            
        $templateArr['settings'] = $q->fetchArray();               
            
		render_to_response('admin/settings.html', $templateArr);
	}
	
	function action_delete($id = false) {
		print_r($_POST);
		
		
		
		$model = isset($_POST['model']) ? $_POST['model'] : false;
		$result_items = isset($_POST['result_items']) ? $_POST['result_items'] : false;
		
	}
	
	//
	

}