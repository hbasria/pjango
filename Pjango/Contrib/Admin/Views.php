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
		
		$modelAdminClass = sprintf('%s\Models\%sAdmin', $app_label, $model);
		$modelAdmin = new $modelAdminClass;
		$modelUrl = sprintf('%s/admin/%s/%s/',pjango_ini_get('SITE_URL'), $app_label, $model);
		$contentType = ContentType::get_for_model($model, $app_label);
		
		$pjangoMediaAdmin = new \Pjango\Core\Models\PjangoMediaAdmin();
	
		$q = Doctrine_Query::create()
			->from('PjangoMedia o')
			->where('o.content_type_id = ? AND o.object_id = ?', array($contentType->id, $id));
	
		$cl = new \Pjango\Contrib\Admin\ChangeList($app_label, 'PjangoMedia', $q, 
			$pjangoMediaAdmin->list_display, 
			$pjangoMediaAdmin->list_display_links, 
			$pjangoMediaAdmin->list_filter, 
			$pjangoMediaAdmin->date_hierarchy, 
			$pjangoMediaAdmin->search_fields, 
			$pjangoMediaAdmin->list_per_page, 
			$pjangoMediaAdmin->row_actions,
			$pjangoMediaAdmin->actions);
		$templateArr['cl'] = $cl;
	
		$templateArr['third_level_navigation'] = $modelAdmin->get_third_level_navigation('files', $modelUrl, $id);
		$templateArr['has_add_permission'] = $request->user->has_perm($app_label.'.can_add_'.$model);
		$templateArr['has_delete_permission'] = $request->user->has_perm($app_label.'.can_delete_'.$model);
		render_to_response('admin/change_list.html', $templateArr);
	}	
	
	function app_files_addchange($request, $app_label=false, $model=false, $id=false, $image_id=false) {
		$templateArr = array('current_admin_menu'=>$app_label,
				'current_admin_submenu'=>$model,
				'current_admin_submenu2'=>$model,
				'title'=>__(sprintf('%s %s Files', $app_label,$model)));
	
		$modelAdminClass = sprintf('%s\Models\%sAdmin', $app_label, $model);
		$modelAdmin = new $modelAdminClass();
		$modelUrl = sprintf('%s/admin/%s/%s/',pjango_ini_get('SITE_URL'), $app_label, $model);
		$modelClass = $model;
		
		$formClass  = 'Pjango\Core\Forms\PjangoMediaForm';
		$formData   = array();
	
		$templateArr['third_level_navigation'] = $modelAdmin->get_third_level_navigation('files', $modelUrl, $id);
	
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
	
				$imageObj->site_id = SITE_ID;
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
	    
	    if(!$request->user->has_perm($app_label.'.can_show_'.$model)){
	    	Messages::Error('You are not authorized to perform this operation.');
	    	HttpResponseRedirect("/admin/");
	    }
	    
	    $modelTable = Doctrine_Core::getTable($model);
	    
	    $q = Doctrine_Query::create()
	        ->from($model.' o');
	    
	    if($modelTable->getColumnDefinition('site_id')){
	        $q->addWhere('o.site_id = ? ', array(SITE_ID));
	    }
	    
	    if($model == 'Post'){
	        $q->addWhere('o.post_type = ?', array($app_label));	        
	    }
	    
	    $cl = new Pjango\Contrib\Admin\ChangeList($app_label, $model, $q);
	    $templateArr['cl'] = $cl;	    
	    
	    if(is_file(sprintf('%s/templates/%s/admin/change_list.html', SITE_PATH, strtolower($app_label)))){
	    	$templateFile = sprintf('%s/admin/change_list.html', strtolower($app_label));	    	
	    }else if(is_file(sprintf('%s/templates/%s/admin/change_list.html', APPLICATION_PATH, strtolower($app_label)))){
	    	$templateFile = sprintf('%s/admin/change_list.html', strtolower($app_label));
	    }else {
	    	$templateFile = 'admin/change_list.html';
	    }
	    
	    $templateArr['has_add_permission'] = $request->user->has_perm($app_label.'.can_add_'.$model);
	    $templateArr['has_delete_permission'] = $request->user->has_perm($app_label.'.can_delete_'.$model);
	    render_to_response($templateFile, $templateArr);
	}	
	
	function app_addchange($request, $app_label=false, $model=false, $id=false) {
	    $templateArr = array('current_admin_menu'=>$app_label,
			    				'current_admin_submenu'=>$app_label, 
		    					'current_admin_submenu2'=>$model,
			    				'title'=>pjango_gettext($model.' add')); 
	    
	    $site = \Pjango\Contrib\Admin\AdminSite::getInstance();
	    $contentType = ContentType::get_for_model($model, $app_label);
	    
	    $modelAdminClass = $site->_registry[$app_label][$model];
	    $modelUrl = sprintf('%s/admin/%s/%s/',pjango_ini_get('SITE_URL'), $app_label, $model);	    
	    
	    $modelAdminClassArr = explode('\\', $modelAdminClass);
	    $modelAdminClassArrTmp = array();
	    for ($i = 0; $i < count($modelAdminClassArr)-2; $i++) {
	    	$modelAdminClassArrTmp[] = $modelAdminClassArr[$i];	    	
	    }
	    
	    $formData = array();
	    $formClass = sprintf("%s\Forms\%sForm", implode('\\', $modelAdminClassArrTmp), $model); ;

	    
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
	            $formData['site_id'] = SITE_ID;
	            
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
		$retval = array(); 
	    $contentType = ContentType::get_for_model($model, $app_label);
	
	    if ($contentType){
 	        if($request->user->has_perm($contentType->app_label.'.can_delete_'.$model)){
 	        	
 	        	if ($id){
 	        		$modelObj = Doctrine::getTable($model)->find($id);
 	        		if($modelObj){
 	        			try {
 	        				$modelObj->delete();
 	        				$retval['messages']['info'] = '1 '.__('Records deleted');
 	        			} catch (Exception $e) {
 	        				$pos = strpos($e->getMessage(), 'foreign key constraint fails');
 	        				if ($pos === false) {
 	        					$retval['messages']['error'] = $e->getMessage(); 	        					
 	        				} else {
 	        					$retval['messages']['error'] = __('Integrity constraint violation');
 	        				}
 	        			}
 	        		} 	        		
 	        	}elseif (is_array($_POST['row_id'])){
 	        		$deletedRows = Doctrine_Query::create()
	 	        		->delete($model.' o')
	 	        		->addWhere('o.site_id = ?', SITE_ID)
	 	        		->whereIn('o.id', $_POST['row_id'])
	 	        		->execute();
 	        		$retval['messages']['info'] = $deletedRows.' '.__('Records deleted');
 	        	}
 	        }else{
 	        	$retval['messages']['error'] = __('You are not authorized to perform this operation.'); 	            
 	        }
	    }
	    
	    if ($id){
	    	if (isset($retval['messages']['info'])){
	    		Messages::Info($retval['messages']['info']);
	    	}
	    	if (isset($retval['messages']['error'])){
	    		Messages::Error($retval['messages']['error']);
	    	}
	    	HttpResponseRedirect($_SERVER['HTTP_REFERER']);
	    }else {
	    	echo json_encode($retval);
	    }
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
		
		$contentType = ContentType::get_for_model($modelClass, $app_label);
        
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
        	PjangoMeta::setMeta($contentType->id, $modelObj->id, false, $request->POST);
        	Messages::Info(pjango_gettext('The operation completed successfully'));
        	HttpResponseRedirect(sprintf('/admin/%s/%s/%d/edit/', $app_label, $model, $modelObj->id));
        }
	
        if (!$form) $form = new $formClass($formData);
        $templateArr['addchange_form'] = $form;
        $templateArr['third_level_navigation'] = $modelAdmin->get_third_level_navigation('meta', $modelUrl, $id);
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
    	    ->where('s.site_id = ?', SITE_ID)
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
