<?php
use Pjango\Util\Messages;
class AdminViews {

	function index($request) {
		$templateArr = array();	
		
		render_to_response('admin/index.html', $templateArr);
	}
	
	function app_files($request, $app_label=false, $model=false, $id=false) {
		$templateArr = array('current_admin_menu'=>$app_label,
				'current_admin_submenu'=>$model,
				'current_admin_submenu2'=>$model,
				'title'=>__("$app_label $model Files"));
		
		$coreApps = array('Post');
		
		$modelAdminClass = sprintf('%s\Models\%sAdmin', $app_label, $model);
		
		if(in_array($app_label, $coreApps)){
			$modelAdminClass = sprintf('Pjango\Contrib\%s\Models\%sAdmin', $app_label, $model);
		}		
		
		
		$modelAdmin = new $modelAdminClass();
		$modelUrl = sprintf('%s/admin/%s/%s/',pjango_ini_get('SITE_URL'), $app_label, $model);
		$modelClass = $model;		
		$contentType = ContentType::get_for_model($model, $app_label);
	
		$templateArr['third_level_navigation'] = $modelAdmin->get_third_level_navigation('files', $modelUrl, $id);
	
		$q = Doctrine_Query::create()
			->from('PjangoMedia o')
			->where('o.content_type_id = ? AND o.object_id = ?', array($contentType->id, $id));
	
		$cl = new \Pjango\Contrib\Admin\ChangeList($q);
		$templateArr['cl'] = $cl;
	
		render_to_response('admin/change_list.html', $templateArr);
	}	
	
	function app_files_addchange($request, $app_label=false, $model=false, $id=false, $image_id=false) {
		$templateArr = array('current_admin_menu'=>$app_label,
				'current_admin_submenu'=>$model,
				'current_admin_submenu2'=>$model,
				'title'=>__($model.' images'));
	
		$templateArr['extraheads'] = array(
				sprintf('<script type="text/javascript" src="%s/js/filemanager/filemanager.js"></script>', pjango_ini_get('ADMIN_MEDIA_URL')),
				sprintf('<script type="text/javascript" src="%s/js/PjangoMedia_addchange.js"></script>', pjango_ini_get('ADMIN_MEDIA_URL'))
		);
		
		$coreApps = array('Post');
		
		$modelAdminClass = sprintf('%s\Models\%sAdmin', $app_label, $model);
		
		if(in_array($app_label, $coreApps)){
			$modelAdminClass = sprintf('Pjango\Contrib\%s\Models\%sAdmin', $app_label, $model);
		}		
		 
		$modelAdmin = new $modelAdminClass();
		$modelUrl = sprintf('%s/admin/%s/%s/',pjango_ini_get('SITE_URL'), $app_label, $model);
		$modelClass = $model;
		
		$formClass  = 'Pjango\Core\Forms\PjangoMediaForm';
		$formData   = array();
	
		if(is_array($modelAdmin->third_level_navigation)){
	    	for ($i = 0; $i < count($modelAdmin->third_level_navigation); $i++) {
	    		$modelAdmin->third_level_navigation[$i]['url'] = $modelUrl.$id."/".$modelAdmin->third_level_navigation[$i]['key']."/";
	    		if($modelAdmin->third_level_navigation[$i]['key'] == 'files'){
	    			$modelAdmin->third_level_navigation[$i]['class'] = 'active';
	    			
	    			if (isset($modelAdmin->third_level_navigation[$i+1])){
	    				$modelAdmin->third_level_navigation[$i+1]['class'] = 'passive after-active';
	    			}
	    		}
	    	}
	    		    	      	
        	$templateArr['third_level_navigation'] = $modelAdmin->third_level_navigation;
        } 
	
		$modelObj = Doctrine_Core::getTable($model)->find($id);
	
		if(!$modelObj){
			Messages::Info(pjango_gettext('No records found'));
			HttpResponseRedirect($modelUrl);
		}
		 
		$contentType = ContentType::get_for_model($model, $app_label);
	
		$formData['object_id'] = $modelObj->id;
		$formData['content_type_id'] = $contentType->id;
	
		if ($image_id){
			$imageObj = Doctrine_Query::create()
			->from('PjangoMedia o')
			->where('o.id = ?', $image_id)
			->fetchOne();
	
			if ($imageObj) {
				$formData = $imageObj->toArray();
			}
		}
	
		if ($request->POST){
			$form = new $formClass($request->POST);
	
			try {
				if (!$form->is_valid()) throw new Exception('There are incomplete required fields. Please complete them.');
				$formData = $form->cleaned_data();
				$formData['file_path'] = preg_replace('/(.*?)thumb\//i', SITE_PATH.'/', $formData['file_path']);
				
				$isFrame = strpos($this->description, '<iframe');
				//eğer descriptionda iframe varsa video embed kodu eklenmişse
				if ($isFrame !== false) {
					$fileType = 'video/embed';
				}else {
					if(is_file($formData['file_path'])){
						$finfo = finfo_open(FILEINFO_MIME_TYPE);
						$fileType = finfo_file($finfo, $formData['file_path']);
						finfo_close($finfo);
					}
				}
				
				if(!$imageObj) $imageObj = new PjangoMedia();							
	
				$imageObj->fromArray($formData);
				$imageObj->file_type = $fileType;
	
				if ($imageObj->state() == Doctrine_Record::STATE_TCLEAN){
					$imageObj->created_by = $request->user->id;
					$imageObj->updated_by = $request->user->id;
				}else {
					$imageObj->updated_by = $request->user->id;
				}
	
				$imageObj->site_id = pjango_ini_get('SITE_ID');
				$imageObj->save();
	
				Messages::Info(pjango_gettext('The operation completed successfully'));
				HttpResponseRedirect("/admin/{$app_label}/{$model}/{$id}/files/");
			} catch (Exception $e) {
				Messages::Error($e->getMessage());
			}
	
		}
	
		if (!$form) $form = new $formClass($formData);
		$templateArr['addchange_form'] = $form;
	
		render_to_response('admin/addchange.html', $templateArr);
	}	
	
	function app_files_delete($request, $app_label=false, $model=false, $id=false, $files_id=false) {
	
		$contentType = ContentType::get_for_model($model);		
		$mediaObj = Doctrine_Query::create()
			->from('PjangoMedia o')
			->where('o.id = ?', array($files_id))
			->fetchOne();		
	
		if ($mediaObj){
			try {
				$mediaObj->delete();
				Messages::Info(__('records deleted'));					
			} catch (Exception $e) {}			
		}
	
		HttpResponseRedirect($_SERVER['HTTP_REFERER']);
	}	
	
	
	function app_index($request, $app_label=false, $model=false, $id=false) {
	    $templateArr = array('current_admin_menu'=>$app_label,
	    				'current_admin_submenu'=>$model, 
	    				'current_admin_submenu2'=>$model,
	    				'title'=>__($app_label.' '.$model.' List')); 
	    
	    $coreApps = array('Post', 'Pages');
	    if(in_array($app_label, $coreApps)){
	    	$app_label2 = sprintf('Pjango\Contrib\%s', $app_label);
	    }else {
	    	$app_label = $app_label;
	    }    
	    
	    if(!$request->user->has_perm($app_label.'.can_show_'.$model)){
	    	Messages::Error('You are not authorized to perform this operation.');
	    	HttpResponseRedirect("/admin/");
	    }
	    
	    $modelTable = Doctrine_Core::getTable($model);
	    
	    $q = Doctrine_Query::create()
	        ->from($model.' o');
	    
	    if($modelTable->getColumnDefinition('site_id')){
	        $q->addWhere('o.site_id = ? ', array(pjango_ini_get('SITE_ID')));
	    }
	    
	    if($model == 'Post'){
	        $q->addWhere('o.post_type = ?', array($app_label));	        
	    }
	    
	    $cl = new Pjango\Contrib\Admin\ChangeList($q);
	    $templateArr['cl'] = $cl;	    
	
	    $templateArr['has_add_permission'] = $request->user->has_perm($app_label2.'.can_add_'.$model);
	    $templateArr['has_delete_permission'] = $request->user->has_perm($app_label2.'.can_delete_'.$model);
	    render_to_response('admin/change_list.html', $templateArr);
	}	
	
	function app_addchange($request, $app_label=false, $model=false, $id=false) {
	    $templateArr = array('current_admin_menu'=>$app_label,
			    				'current_admin_submenu'=>$app_label, 
		    					'current_admin_submenu2'=>$model,
			    				'title'=>pjango_gettext($model.' add')); 
	
	    $coreApps = array('Post','Auth');
	    $modelAdminClass = sprintf('%s\Models\%sAdmin', $app_label, $model);
	    $modelUrl = sprintf('%s/admin/Accounting/%s/',pjango_ini_get('SITE_URL'), $model);
	    $formClass = sprintf("%s\Forms\%sForm", $app_label, $model);
	    $formData = array();
	    $contentType = ContentType::get_for_model($model, $app_label);
	    
	    if(in_array($app_label, $coreApps)){
	    	$modelAdminClass = sprintf('Pjango\Contrib\%s\Models\%sAdmin', $app_label, $model);
	    	$formClass = sprintf("Pjango\Contrib\%s\Forms\%sForm", $app_label, $model);
	    }
	    
	    $modelAdmin = new $modelAdminClass();	    	    
	    $templateArr['extraheads'] = $modelAdmin->extraheads;	    
	
	    if ($id){
	        $templateArr['title'] = __($model.' Change');
	        $modelObj = Doctrine_Query::create()
    	        ->from($model.' o')
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
	            
	            $templateArr['third_level_navigation'] = $modelAdmin->get_third_level_navigation('edit', $modelUrl, $id);
	        }
	    }else {
	    	$templateArr['third_level_navigation'] = $modelAdmin->get_third_level_navigation('edit', $modelUrl);
	    }
	
	    if ($request->POST){
	        $form = new $formClass($request->POST);
	
	        try {
	            if (!$form->is_valid()) throw new Exception(pjango_gettext('There are some errors, please correct them below.'));
	
	            $formData = $form->cleaned_data();
	            if(!$modelObj) $modelObj = new $model();
	            
// 	            if contains site_id field
	            $formData['site_id'] = pjango_ini_get('SITE_ID');
	            
// 	            if contains created_by and updated_by  field	            
	            if ($modelObj->state() == Doctrine_Record::STATE_TCLEAN){
    	            $formData['created_by'] = $request->user->id;
    	        }
    	        $formData['updated_by'] = $request->user->id;
	            	
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
	    $templateArr['addchange_form'] = $form;
	
	    render_to_response('admin/addchange.html', $templateArr);
	}		
	
	function app_delete($request, $app_label=false, $model=false, $id=false) {	
	    $contentType = ContentType::get_for_model($model, $app_label);
	
	    if ($contentType){
 	        if($request->user->has_perm($contentType->app_label.'.can_delete_'.$model)){
	
	            $modelObj = Doctrine::getTable($model)->find($id);
	
	            if($modelObj){
	                try {
	                    $modelObj->delete();
 	                    Messages::Info(__('1 Records deleted'));
	                } catch (Exception $e) {
	                	$pos = strpos($e->getMessage(), 'foreign key constraint fails');
	                	if ($pos === false) {
	                		Messages::Error($e->getMessage());
	                	} else {
	                		Messages::Error(__('Integrity constraint violation'));
	                	}
	                }
	            }
 	        }else{
 	            Messages::Error(__('You are not authorized to perform this operation.'));
 	        }
	    }
	
	    HttpResponseRedirect($_SERVER['HTTP_REFERER']);
	}	
	
	function app_meta($request, $app_label=false, $model=false, $id=false) {
		$templateArr = array('current_admin_menu'=>$app_label,
				'current_admin_submenu'=>$model,
				'current_admin_submenu2'=>$model,
				'title'=>__($model.' Meta'));
	
		$modelAdminClass = sprintf('%s\Models\%sAdmin', $app_label, $model);
		$modelAdmin = new $modelAdminClass();		
		$modelUrl = sprintf('%s/admin/%s/%s/',pjango_ini_get('SITE_URL'), $app_label, $model);
		$modelClass = $model;
		
		$formClass = sprintf('%s\Forms\%sMetaForm', $app_label, $model);
		$formData = array();
		
		$contentType = ContentType::get_for_model($modelClass);
	
	    if(is_array($modelAdmin->third_level_navigation)){
	    	for ($i = 0; $i < count($modelAdmin->third_level_navigation); $i++) {
	    		$modelAdmin->third_level_navigation[$i]['url'] = $modelUrl.$id."/".$modelAdmin->third_level_navigation[$i]['key']."/";
	    		if($modelAdmin->third_level_navigation[$i]['key'] == 'meta'){
	    			$modelAdmin->third_level_navigation[$i]['class'] = 'active';
	    			
	    			if (isset($modelAdmin->third_level_navigation[$i+1])){
	    				$modelAdmin->third_level_navigation[$i+1]['class'] = 'passive after-active';
	    			}
	    		}
	    	}
	    		    	      	
        	$templateArr['third_level_navigation'] = $modelAdmin->third_level_navigation;
        }    
        
        if ($id){
        	$modelObj = Doctrine_Query::create()
	        	->from($modelClass.' o')
	        	->where('o.id = ?', $id)
	        	->fetchOne();
        
        	if ($modelObj) {
        		$metaData = PjangoMeta::getMeta($contentType->id, $modelObj->id);
        		foreach ($metaData as $metaDataItem) {
        			$formData[$metaDataItem->meta_key] = $metaDataItem->meta_value;
        		}
        	}
        }  

        if ($request->POST){
        	PjangoMeta::setMeta($contentType->id, $modelObj->id, false, $_POST);
        	Messages::Info(pjango_gettext('The operation completed successfully'));
        	HttpResponseRedirect(sprintf('/admin/%s/%s/%d/edit/', $app_label, $model, $modelObj->id));
        }
	
        if (!$form) $form = new $formClass($formData);
        $templateArr['addchange_form'] = $form->as_list();
    
        render_to_response('admin/addchange.html', $templateArr);
	}	
	
	function settings($request, $category = 'GENERAL') {
	    $templateArr = array('current_admin_menu'=>'settings',
					'current_admin_submenu'		=> 'settings',
					'current_admin_submenu2'	=> $category
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
    	    ->where('s.site_id = ?', pjango_ini_get('SITE_ID'))
    	    ->groupBy('s.category');
	
	    $templateArr['settings_category'] = $q->fetchArray();
	
	    $q = Doctrine_Query::create()
	        ->from('Settings s');
	
	    if ($category){
	        $q->where('s.category = ?', array($category));
	        $templateArr['current_admin_submenu2'] = strtoupper($category);
	    }
	
	    $templateArr['settings'] = $q->fetchArray();
	
	    render_to_response('admin/settings.html', $templateArr);
	}	
	
	function app_model_settings($request, $app_label = false, $model = false) {
		$templateArr = array('current_admin_menu'=>$app_label,
				'current_admin_submenu'=>$model,
				'current_admin_submenu2'=>'Settings',
				'title'=> __(sprintf('%s %s Settings', $app_label, $model)));
		
		$coreApps = array('Post');
		$app_label0 = $app_label;
		if(in_array($app_label, $coreApps)){
			$app_label = sprintf('Pjango\Contrib\%s', $app_label);
		}		
		
		$contentType = ContentType::get_for_model($model, $app_label);
		$formClass = sprintf('%s\Forms\%sSettingsForm', $app_label, $model);
		$formData = array();		
		$ignoredSettings = array('is_active','title','show_title','category_id','content');
		
		$settings = Doctrine_Query::create()
			->from('Settings o')
			->where('o.category = ? AND o.site_id = ? ', array($app_label, SITE_ID))
			->fetchArray();
		
		foreach ($settings as $settingsValue) {
			$formData[$settingsValue['name']] = $settingsValue['value'];
		}
		
		if(class_exists('PageLayout')){
			$pageLayout = Doctrine_Query::create()
				->from('PageLayout o')
				->where('o.site_id = ? AND o.content_type_id = ?', array(SITE_ID, $contentType->id))
				->fetchOne();
			
			if($pageLayout){
				$formData = array_merge($formData, $pageLayout->toArray());				
			}			
		}
		
		if ($request->POST){
			$form = new $formClass($request->POST);
			
			
			try {
				if (!$form->is_valid()) throw new Exception(pjango_gettext('There are some errors, please correct them below.'));
				$formData = $form->cleaned_data();
				
				if(class_exists('PageLayout')){
					if (!$pageLayout){
						$pageLayout = new PageLayout();
					}
					
					$pageLayout->fromArray($formData);
					$pageLayout->content_type_id = $contentType->id;
					$pageLayout->site_id = SITE_ID;
					$pageLayout->save();
				}
				
				foreach ($formData as $key => $value) {
					if(in_array($key, $ignoredSettings)){						
						unset($formData[$key]);
					}
				}				
				
				Settings::saveFromArray($app_label, $formData);
				
				Messages::Info('The operation completed successfully');
				HttpResponseRedirect(sprintf('/admin/%s/%s/settings/', $app_label0, $model));
			} catch (Exception $e) {
				Messages::Error($e->getMessage());
			}
		}
		
		
		if (!$form) $form = new $formClass($formData);
		$templateArr['addchange_form'] = $form;
		
		render_to_response('admin/addchange.html', $templateArr);		
	}
}
