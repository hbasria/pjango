<?php
require_once 'pjango/shortcuts.php';
require_once 'pjango/contrib/admin/util.php';
require_once 'pjango/http.php';

class PostViews {
	
	function admin_index($request) {
		$uriArr = explode('/', getRequestUri());
		$taxonomy = $uriArr[count($uriArr)-3];
		
		$templateArr = array('current_admin_menu'=>$taxonomy, 
				'current_admin_submenu'=>$taxonomy,
				'current_admin_submenu2'=>'Post'); 
		
		$className = ucfirst($taxonomy);
		$adminClassName = $className.'Admin';
		
		$q = Doctrine_Query::create()
		    ->from('Post o')
		    ->leftJoin('o.Translation t')
		    ->leftJoin('o.Categories c')
			->leftJoin('c.Translation ct')
		    ->where('o.site_id = ? AND o.post_type = ?', array(pjango_ini_get('SITE_ID'), $taxonomy));

		if(class_exists($adminClassName)){
			$adminClass = new $adminClassName();
				
			$cl = new ChangeList($q, 
					$adminClass->list_display, 
					$adminClass->list_display_links, 
					$adminClass->list_filter, 
					$adminClass->date_hierarchy, 
					$adminClass->search_fields, 
					$adminClass->list_per_page, 
					$adminClass->row_actions);
		}else {
			$cl = new ChangeList($q);
		}

		$templateArr['cl'] = $cl;		      
		render_to_response('admin/change_list.html', $templateArr);
	}
	
	function admin_addchange($request, $id = false) {
		$uriArr = explode('/', getRequestUri());
		
		if ($uriArr[count($uriArr)-2] == 'add'){
			$taxonomy = $uriArr[count($uriArr)-4];
		}elseif ($uriArr[count($uriArr)-2] == 'edit'){
			$taxonomy = $uriArr[count($uriArr)-5];
		}else{
			$taxonomy = 'post';
		}

		$templateArr = array('current_admin_menu'=>$taxonomy,
						'current_admin_submenu'=>$taxonomy, 
						'current_admin_submenu2'=>'Post',
						'title'=>pjango_gettext($taxonomy));
		
		$modelClass = 'Post';
		$formClass = $modelClass.'Form';	

		if (class_exists(ucfirst($taxonomy).'Form')) {
		    $formClass = ucfirst($taxonomy).'Form';
		}		
		
		$templateArr['third_level_navigation'] = array(
		    array('name'=>pjango_gettext(ucfirst($taxonomy).' properties'),
		    		'url'=>pjango_ini_get('SITE_URL').'/admin/'.$taxonomy,'class'=>'active'),
		);		

		$formData = array();	
		$contentType = ContentType::get_for_model($modelClass);
		
		if ($id){
			$addchangeObj = Doctrine_Query::create()
			->from('Post o')
			->leftJoin('o.Translation t')
			->addWhere('o.id = ?', array($id))
			->fetchOne();
		
			if($addchangeObj){
				$templateArr['addchange_obj'] = $addchangeObj;
				$formData = $addchangeObj->toArray();
		
				$lng = pjango_ini_get('LANGUAGE_CODE');
		
				$formData['title'] = $addchangeObj->Translation[$lng]->title;
				$formData['content'] = $addchangeObj->Translation[$lng]->content;
				$formData['excerpt'] = $addchangeObj->Translation[$lng]->excerpt;
				$formData['slug'] = $addchangeObj->Translation[$lng]->slug;
				
				if ($addchangeObj->Categories && count($addchangeObj->Categories) > 0){
					foreach ($addchangeObj->Categories as $categoryItem) {
						$formData['categories'] = $categoryItem->id;
					}
				}		

				$metaData = PjangoMeta::getMeta($contentType->id, $addchangeObj->id);
				
				foreach ($metaData as $metaDataItem) {
					$formData[$metaDataItem->meta_key] = $metaDataItem->meta_value;
				}				
			}
		}		

		if ($request->POST){
			$form = new $formClass($taxonomy, $request->POST);

			try {
				
				if (!$form->is_valid()) {					
					throw new Exception('Hataları kontrol ederek tekrar deneyin.');
				}
				
				$formData = $form->cleaned_data();
				if(!$addchangeObj) $addchangeObj = new $modelClass();
				
				$lng = pjango_ini_get('LANGUAGE_CODE');
				
				$addchangeObj->fromArray($formData);
				$addchangeObj->save();
				$addchangeObj->Translation[$lng]->title = stripslashes($request->POST['title']);
				$addchangeObj->Translation[$lng]->content = stripslashes($request->POST['content']);
				$addchangeObj->Translation[$lng]->excerpt = stripslashes($request->POST['excerpt']);
				$addchangeObj->Translation[$lng]->slug = stripslashes($request->POST['slug']);
				$addchangeObj->post_type = $taxonomy;
				$addchangeObj->site_id = pjango_ini_get('SITE_ID');
				$addchangeObj->created_by = $request->user->id;				
				
				$addchangeObj->unlink('Categories');
				$addchangeObj->link('Categories', $formData['categories']);
				$addchangeObj->save();
				
				PjangoMeta::setMeta($contentType->id, $addchangeObj->id, false, $request->POST);
				Messages::Info(pjango_gettext('The operation completed successfully'));
				HttpResponseRedirect('/admin/'.$taxonomy.'/'.$modelClass.'/');
			} catch (Exception $e) {
				Messages::Error($e->getMessage());
				
				
			}
		}		
        
        if (!$form) $form = new $formClass($taxonomy, $formData);
        $templateArr['addchange_form'] = $form->as_list();
        $templateArr['taxonomy'] = $taxonomy;
        
        $templateFileName = $taxonomy.'/admin/addchange.html';
        
        if (!file_exists(APPLICATION_PATH.'/apps/'.$taxonomy.'/templates/'.$templateFileName)) {
        	$templateFileName = 'post/admin/addchange.html';
        }        
    	
    	render_to_response($templateFileName, $templateArr);
	}
	
    function admin_delete($request,$id) {
    	$uriArr = explode('/', $_SERVER["REQUEST_URI"]);
    	
    	$taxonomy = $uriArr[count($uriArr)-4];
    	    	
        $o = Doctrine::getTable('Post')->find($id);
        
        if($o){
        	try {
        		$o->unlink('Categories');
        		$o->save();
        		$o->delete();	
        	   	Messages::Info(pjango_gettext('1 record has been deleted.'));
        	} catch (Exception $e) {
        		Messages::Error($e->getMessage());
        	}
            
        }
        
        HttpResponseRedirect('/admin/'.$taxonomy.'/');        
    } 
	
    function admin_category_index($request) {
        $uriArr = explode('/', getRequestUri());
        $taxonomy = $uriArr[count($uriArr)-3];
        $className = $uriArr[count($uriArr)-2];
        $adminClassName = $className.'Admin';
        
        $templateArr = array('current_admin_menu'=>$taxonomy,
        				'current_admin_submenu'=>$taxonomy,
        				'current_admin_submenu2'=>$className); 
        
        $q = Doctrine_Query::create()
            ->from($className.' o')
            ->leftJoin('o.Translation t')
            ->where('o.site_id = ? AND o.taxonomy = ?', array(pjango_ini_get('SITE_ID'), $taxonomy));
        
        if(class_exists($adminClassName)){
            $adminClass = new $adminClassName();
        
            $cl = new ChangeList($q,
            $adminClass->list_display,
            $adminClass->list_display_links,
            $adminClass->list_filter,
            $adminClass->date_hierarchy,
            $adminClass->search_fields,
            $adminClass->list_per_page,
            $adminClass->row_actions);
        }else {
            $cl = new ChangeList($q);
        }
        
        $templateArr['cl'] = $cl;
        render_to_response('admin/change_list.html', $templateArr);        
        
    }

	
	function admin_category_addchange($request, $id = false) {
		
		$uriArr = explode('/', getRequestUri());
		
		if ($uriArr[count($uriArr)-2] == 'add'){
			$taxonomy = $uriArr[count($uriArr)-4];
		}elseif ($uriArr[count($uriArr)-2] == 'edit'){
			$taxonomy = $uriArr[count($uriArr)-5];
		}else{
			$taxonomy = 'post';
		}
		
		$templateArr = array('current_admin_menu'=>$taxonomy, 
				'current_admin_submenu'=>$taxonomy,
				'current_admin_submenu2'=>'PostCategory'); 
		
		$formData = array();
		$modelClass = 'PostCategory';
		$formClass = 'PostCategoryForm';
		

		$lng = pjango_ini_get('LANGUAGE_CODE');
		
		//eğer kategori yoksa ekle
		$catTest = Doctrine_Query::create()
			->from('PostCategory o')
			->where('o.taxonomy = ?', array($taxonomy))
			->count();		
		
		if ($catTest<=0){
			$category = new PostCategory();
			$category->Translation[$lng]->name = $taxonomy.pjango_gettext('Main Category');	
			$category->site_id = pjango_ini_get('SITE_ID');			
			$category->taxonomy = $taxonomy;
			$category->save();
			$treeObject = Doctrine_Core::getTable('PostCategory')->getTree();
			$treeObject->createRoot($category);			
		}

			
		
		if ($id){
			$addchangeObj = Doctrine_Query::create()
				->from('PostCategory o')
				->leftJoin('o.Translation t')
				->addWhere('o.id = ?', array($id))
				->fetchOne();
			
			if ($addchangeObj) {
				$parent = $addchangeObj->getNode()->getParent();				
				$formData = $addchangeObj->toArray();
				
				$templateArr['addchange_obj'] = $addchangeObj;
				$formData['parent_id'] = $parent->id;
				$formData['name'] = $addchangeObj->Translation[$lng]->name;
				$formData['slug'] = $addchangeObj->Translation[$lng]->slug;				
			}
		}	



		if ($request->POST){
			$form = new $formClass($taxonomy, $request->POST);
			
			if ($form->is_valid()){
				$formData = $form->cleaned_data();
				
				if(!$addchangeObj) $addchangeObj = new $modelClass();
				
				try {
					$parent = Doctrine::getTable($modelClass)->find($formData['parent_id']);

					$addchangeObj->taxonomy = $taxonomy;
					$addchangeObj->site_id = pjango_ini_get('SITE_ID');
					$addchangeObj->Translation[$lng]->name = $formData['name'];
					$addchangeObj->Translation[$lng]->slug = $formData['slug'];
						
					if ($addchangeObj->state() == Doctrine_Record::STATE_TDIRTY){
						$addchangeObj->getNode()->insertAsLastChildOf($parent);
					}else{
						$addchangeObj->save();							
						$curParent = $addchangeObj->getNode()->getParent();
						
						if ($curParent->id != $parent->id){
							$addchangeObj->getNode()->moveAsLastChildOf($parent);
							
						}
					}
				
					Messages::Info(pjango_gettext('The operation completed successfully'));
					HttpResponseRedirect('/admin/'.$taxonomy.'/'.$modelClass.'/');
				
				} catch (Exception $e) {
					Messages::Error($e->getMessage());
				}				
			}
		}
		
		if (!$form) $form = new $formClass($taxonomy, $formData);
		$templateArr['addchange_form'] = $form->as_list();
		$templateArr['taxonomy'] = $taxonomy;
		render_to_response('post/admin/category_addchange.html', $templateArr);
	}
	
}