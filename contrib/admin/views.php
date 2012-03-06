<?php
require_once 'pjango/shortcuts.php';
require_once 'pjango/contrib/admin/util.php';
require_once 'pjango/http.php';

class AdminViews {
	
	function index() {

		
		render_to_response('admin/index.html', array());
	}
	
	function app_index($request, $app_label=false, $model=false, $id=false) {
	    $templateArr = array('current_admin_menu'=>$app_label,
	    				'current_admin_submenu'=>$app_label, 
	    				'current_admin_submenu2'=>$model,
	    				'title'=>pjango_gettext($model.' list')); 

	    $q = Doctrine_Query::create()
	        ->from($model.' o');
	    
// 	    $modelRelations = Doctrine_Core::getTable($model)->getRelations();	    
// 	    foreach($modelRelations as $name => $relation) {
// 	        $q->leftJoin('o.'.$name);
// 	    }
	    
	    $cl = new ChangeList($q);
	    $templateArr['cl'] = $cl;
	
	    render_to_response('admin/change_list.html', $templateArr);	
	}	
	
	function app_addchange($request, $app_label=false, $model=false, $id=false) {
	    $templateArr = array('current_admin_menu'=>$app_label,
		    				'current_admin_submenu'=>$app_label, 
	    					'current_admin_submenu2'=>$model,
		    				'title'=>pjango_gettext($model.' add/change')); 

	    
	    if(!$request->user->has_perm($app_label.'.can_change_'.$model)){
	        Messages::Error('Bu işlemi yapmak için yetkiniz yok.');
	        HttpResponseRedirect($_SERVER['HTTP_REFERER']);
	    }
	    
	    if (isset($_GET['is_popup'])){
	        $templateArr['is_popup'] = true;
	    }	    
	    
	    $modelClass = $model;
		$formClass = $modelClass.'Form';
		$formData = array();
		$contentType = ContentType::get_for_model($modelClass);
		
		if ($id){
		    $modelObj = Doctrine_Query::create()
    		    ->from($modelClass.' o')
    		    ->where('o.id = ?', $id)
    		    ->fetchOne();
		    
		    if ($modelObj) {
		        
		        if((method_exists($modelObj,'toMyArray'))){
		            $formData = $modelObj->toMyArray();
		        }else {
		        $formData = $modelObj->toArray();
		        }
		        
		        $metaData = PjangoMeta::getMeta($contentType->id, $modelObj->id);
		        foreach ($metaData as $metaDataItem) {
		            $formData[$metaDataItem->meta_key] = $metaDataItem->meta_value;
		        }		        
		    }
		}
		
		if ($request->POST){
		    $form = new $formClass($request->POST);
		    
		    try {
		        if (!$form->is_valid()) throw new Exception(pjango_gettext('There are some errors, please correct them below.'));
		        
		        $formData = $form->cleaned_data();
		        if(!$modelObj) $modelObj = new $modelClass();
		        
// 		        if ($modelObj->state() == Doctrine_Record::STATE_TCLEAN){
// 		            $modelObj->created_by = $request->user->id;
// 		        }else{
// 		            $modelObj->updated_by = $request->user->id;
// 		        }		  

		        if((method_exists($modelObj,'fromMyArray'))){
		            $modelObj->fromMyArray($formData);
		        }else {
		        $modelObj->fromArray($formData);
		        }		        
		        
		        $modelObj->save();
		        
		        PjangoMeta::setMeta($contentType->id, $modelObj->id, false, $request->POST);
		        
		        Messages::Info('The operation completed successfully');
		        HttpResponseRedirect("/admin/{$app_label}/{$model}/");
		        
	        } catch (Exception $e) {
	            Messages::Error($e->getMessage());
	        }
		}
		
		if (!$form) $form = new $formClass($formData);
		$templateArr['addchange_form'] = $form->as_list();		
	
	    render_to_response('admin/addchange.html', $templateArr);
	}	
	
	
	
	
	function settings($request, $category = 'GENERAL') {
		$templateArr = array('current_admin_menu'=>'settings', 
				'current_admin_submenu'		=> 'GENERAL',
				'current_admin_submenu2'	=> 'GENERAL'
		);	
		
		
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
			$templateArr['current_admin_submenu2'] = strtoupper($category);            
		}            
            
        $templateArr['settings'] = $q->fetchArray();               
            
		render_to_response('admin/settings.html', $templateArr);
	}
	
	function admin_action_addchange($request, $model, $id = false) {
		$templateArr = array('title'=>$model);
		$formClass = $model.'Form';
		
		if(!$request->user->has_perm('crm.can_change_'.$model)){
			Messages::Error('Bu işlemi yapmak için yetkiniz yok.');   
			HttpResponseRedirect($_SERVER['HTTP_REFERER']);
		}			
		
		if (isset($_GET['is_popup'])){
			$templateArr['is_popup'] = true;
		}
		
		if ($request->POST){
			$form = new $formClass($request->POST);
			
			if ($form->is_valid()){
				$obj = Doctrine::getTable($model)->find($request->POST['id']);
				
				if(!$obj) $obj = new $model();
				
				try {
					$obj->fromArray($form->cleaned_data());
		            $obj->save();
		            		            
		            Messages::Info('İşlem başarıyla tamamlandı'); 	

		            if (isset($_SESSION['_REFERER'])){
		            	$ref = $_SESSION['_REFERER'];
		            	$_SESSION['_REFERER'] = false;
		            	HttpResponseRedirect($ref); 	 	
		            }
		            
									
				} catch (Exception $e) {
					Messages::Error($e->getMessage());            	
            	}
				
			}
		}else {
			$_SESSION['_REFERER'] = $_SERVER['HTTP_REFERER'];
		}
		
		$templateArr['referer'] = $_SESSION['_REFERER'];
		
		$formData = array();
		
		if ($id){
	        $obj = Doctrine::getTable($model)->find($id);	   
	        $templateArr['obj'] = $obj;		         
	        if ($obj) $formData = $obj->toArray();
		}	
		
		if (!$form) $form = new $formClass($formData);
		$templateArr['addchange_form'] = $form->as_list();		
		
		render_to_response('admin/addchange.html', $templateArr);
	}
	
	function admin_action_delete($request, $model, $id = false) {
		
		$ct = ContentType::get_for_model($model);
		
		if ($ct){
			if($request->user->has_perm($ct->app_label.'.can_change_'.$model)){
				
				$o = Doctrine::getTable($model)->find($id);
        
		        if($o){
					try {
		        	   $o->delete();	
		        	   Messages::Info('1 kayıt silindi.');     	   
		        	} catch (Exception $e) {
		        		Messages::Error($e->getMessage());
		        	}				
		        }
			}else{
				Messages::Error('Bu işlemi yapmak için yetkiniz yok.');   
				HttpResponseRedirect($_SERVER['HTTP_REFERER']);
			}			
		}
		
        HttpResponseRedirect($_SERVER['HTTP_REFERER']);   
	}
	
	
	function action_delete($id = false) {
		print_r($_POST);
		
		
		
		$model = isset($_POST['model']) ? $_POST['model'] : false;
		$result_items = isset($_POST['result_items']) ? $_POST['result_items'] : false;
		
	}
	
	function pjangolist($request) {
		$templateArr = array('current_admin_menu'=>'settings',
					'current_admin_submenu'=>'pjangolist');

		$q = Doctrine_Query::create()
		->from('PjangoList o')
		->leftJoin('o.Translation t');
		
		$lng = pjango_ini_get('LANGUAGE_CODE');
		
		$list_display = array('Translation__'.$lng.'__name');
		
		$cl = new ChangeList($q, $list_display);
		$templateArr['cl'] = $cl;		

		
		render_to_response('admin/change_list.html', $templateArr);
	}
	
	
	function pjangolist_addchange($request, $id = false) {
		$templateArr = array('current_admin_menu'=>'settings',
					'current_admin_submenu'=>'pjangolist', 
					'title'=>'Pjango List');
	
		
		$modelClass = 'PjangoList';
		$formClass = $modelClass.'Form';
		$formData = array();
		$parent = false;
	
		if ($id){
			$addchangeObj = Doctrine_Query::create()
			->from('PjangoList o')
			->leftJoin('o.Translation t')
			->addWhere('o.id = ?', array($id))
			->fetchOne();
	
			if($addchangeObj){
				$parent = $addchangeObj->getNode()->getParent();
				$formData['parent_id'] = $parent->id;
				$templateArr['addchange_obj'] = $addchangeObj;
				$formData = $addchangeObj->toArray();
	
				$lng = pjango_ini_get('LANGUAGE_CODE');
	
				$formData['name'] = $addchangeObj->Translation[$lng]->name;
				$formData['slug'] = $addchangeObj->Translation[$lng]->slug;
	
			}
		}
	
		if ($request->POST){
			$form = new $formClass($request->POST);
	
			try {
	
				if (!$form->is_valid()) throw new Exception('Hataları kontrol ederek tekrar deneyin.');
	
				$formData = $form->cleaned_data();
				if(!$addchangeObj) $addchangeObj = new $modelClass();

				$lng = pjango_ini_get('LANGUAGE_CODE');
				
				$addchangeObj->Translation[$lng]->name = stripslashes($formData['name']);
				
				if ($formData['parent_id'] == 'root'){
					$addchangeObj->save();
					$treeObject = Doctrine_Core::getTable('PjangoList')->getTree();
					$treeObject->createRoot($addchangeObj);
				}else{
					if ($addchangeObj->state() == Doctrine_Record::STATE_TDIRTY){
						$addchangeObj->getNode()->insertAsLastChildOf($parent);
					}else{
						$addchangeObj->save();
						$curParent = $addchangeObj->getNode()->getParent();
					
						if ($curParent->id != $parent->id){
							$addchangeObj->getNode()->moveAsLastChildOf($parent);
								
						}
					}
					
				}
				
				
				
				Messages::Info(pjango_gettext('The operation completed successfully'));
				HttpResponseRedirect('/admin/settings/pjangolist/');
			} catch (Exception $e) {
				Messages::Error($e->getMessage());
			}
	
				
		}
	
		if (!$form) $form = new $formClass($formData);
		$templateArr['addchange_form'] = $form->as_list();
		 
		render_to_response('post/admin/addchange.html', $templateArr);
	}
	
	
	function pjangoimage_addchange($request, $id = false) {
		$templateArr = array();
	
		$modelClass = 'PjangoImage';
		$formClass = $modelClass.'Form';
		$formData = array();
		
		
		if (!$form) $form = new $formClass($formData);
		$templateArr['addchange_form'] = $form->as_list();
			
		render_to_response('admin/addchange.html', $templateArr);
	}
		
}