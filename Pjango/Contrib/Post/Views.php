<?php

use Pjango\Util\Messages,
    Pjango\Contrib\Admin\ChangeList,
    Pjango\Contrib\Post\Forms\PostForm,
    Pjango\Contrib\Post\Forms\PostCategoryForm,
    Pjango\Core\Forms\PjangoMediaForm;

class PostViews {
	
	function post($request) {
		$templateArr = array();

		$q = Doctrine_Query::create()
			->from('Post o')
			->where('o.site_id = ? AND o.post_type = ? AND o.status = ?', array(SITE_ID, Post::TYPE_POST, Post::STATUS_PUBLISHED));
		
		$cl = new ChangeList($q);
		$templateArr['cl'] = $cl;		
				
		render_to_response('post/index.html', $templateArr);
	}
	
	function post_detail($request, $slug) {
		$template = 'post/detail.html';
		$templateArr = array();
	
		$post = Doctrine_Query::create()
			->from('Post o')
			->leftJoin('o.Translation t')
			->where('o.site_id = ? AND o.post_type = ? AND o.status = ? AND t.slug = ?', array(SITE_ID, Post::TYPE_POST, Post::STATUS_PUBLISHED, $slug))
			->fetchOne();
		
		if($post){
			$templateArr['post'] = Doctrine_Query::create()
				->from('Post o')
				->leftJoin('o.Translation t')
				->where('o.id = ?',array($post->id))
				->fetchOne();
			if (strlen($post->template)>0) $template = $post->template;			
		}
		render_to_response($template, $templateArr);
	}
	
	function admin_addchange($request, $taxonomy = 'Post', $id = false) {
		$templateArr = array('current_admin_menu'=>$taxonomy,
						'current_admin_submenu'=>$taxonomy, 
						'current_admin_submenu2'=>'Post',
						'title'=>__($taxonomy.' Properties'));
		
		$modelAdminClass = sprintf('%s\Models\PostAdmin', $taxonomy);
		$modelClass = 'Post';		
		$modelUrl = sprintf('%s/admin/%s/Post/',pjango_ini_get('SITE_URL'), $taxonomy);
		$formClass = 'Pjango\Contrib\Post\Forms\PostForm';
		$formData = array();
		$contentType = ContentType::get_for_model($modelClass, $taxonomy);
		
		if (class_exists($modelAdminClass)){
			$modelAdmin = new $modelAdminClass();
		}else {
			$modelAdmin = new Pjango\Contrib\Post\Models\PostAdmin();			
		}
		
		if (file_exists(APPLICATION_PATH.'/apps/'.$taxonomy.'/Forms/PostForm.php')) {
			$formClass = sprintf('%s\Forms\PostForm', $taxonomy);
		}		
		
		$templateArr['third_level_navigation'] = $modelAdmin->get_third_level_navigation('edit', $modelUrl);				
		
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
						$formData['categories'][] = $categoryItem->id;
					}
				}		

				$metaData = PjangoMeta::getMeta($contentType->id, $addchangeObj->id);
				
				foreach ($metaData as $metaDataItem) {
					$formData[$metaDataItem->meta_key] = $metaDataItem->meta_value;
				}	
				
				$templateArr['third_level_navigation'] = $modelAdmin->get_third_level_navigation('edit', $modelUrl, $id);	
				
			}
		}		

		if ($request->POST){
			$form = new $formClass($taxonomy, $request->POST);

			try {				
				if (!$form->is_valid()) throw new Exception('There are incomplete required fields. Please complete them.');
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
				$addchangeObj->site_id = SITE_ID;
				$addchangeObj->created_by = $request->user->id;							
				$addchangeObj->unlink('Categories');
				$addchangeObj->link('Categories', $formData['categories']);
				
				if(class_exists('Menu')){
					if(intval($formData['meta_menu_location_id'])>0){
						$parentMenu = Doctrine::getTable('Menu')->find($formData['meta_menu_location_id']);						
						if($parentMenu){
							$deletedRows = Doctrine_Query::create()
								->delete('Menu o')
								->where('o.site_id = ? AND o.id = ?', array(SITE_ID, $formData['meta_menu_id']))
								->execute();							
							
							$menu = new \Menu();
							$menu->Translation[$lng]->name = $formData['title'];
							$menu->Translation[$lng]->slug = $formData['slug'];
							$menu->url = '/'.$formData['slug'];
							$menu->site_id = SITE_ID;
							$menu->save();
							$menu->getNode()->insertAsLastChildOf($parentMenu);							
							$request->POST['meta_menu_id'] = $menu->id;
						}
							
					}else {
						$deletedRows = Doctrine_Query::create()
							->delete('Menu o')
							->where('o.site_id = ? AND o.id = ?', array(SITE_ID, $formData['meta_menu_id']))
							->execute();
					}
				}
				
				$addchangeObj->save();
				
				PjangoMeta::setMeta($contentType->id, $addchangeObj->id, false, $request->POST);
				Messages::Info(pjango_gettext('The operation completed successfully'));
				HttpResponseRedirect('/admin/'.$taxonomy.'/'.$modelClass.'/');
			} catch (Exception $e) {
				Messages::Error($e->getMessage());
				
				
			}
		}		
        
        if (!$form) $form = new $formClass($taxonomy, $formData);
        $templateArr['addchange_form'] = $form;
        $templateArr['taxonomy'] = $taxonomy;
        
        $templateFileName = sprintf('%s/admin/addchange.html', strtolower($taxonomy));
        
        if (!file_exists(APPLICATION_PATH.'/templates/'.$templateFileName)) {
        	$templateFileName = 'admin/addchange.html';
        }        
    	
    	render_to_response($templateFileName, $templateArr);
	}
	
    function admin_delete($request, $taxonomy = 'Post',$id) {
        $post = Doctrine::getTable('Post')->find($id);
        
        if($post){
        	try {        		
        		$contentType = ContentType::get_for_model('Post', $taxonomy);
        		
        		$deleted = Doctrine_Query::create()
	        		->delete('PjangoMedia o')
	        		->where('o.site_id = ? AND o.content_type_id = ? AND o.object_id = ?', array(SITE_ID, $contentType->id, $post->id))
        			->execute();

        		$post->unlink('Categories');
        		$post->save();
        		$post->delete();	
        	   	Messages::Info(pjango_gettext('1 record has been deleted.'));
        	} catch (Exception $e) {
        		Messages::Error($e->getMessage());
        	}
            
        }
        
        HttpResponseRedirect('/admin/'.$taxonomy.'/Post/');        
    } 
	
    function admin_category_index($request, $taxonomy = 'Post') {
        $templateArr = array('current_admin_menu'=>$taxonomy,
        				'current_admin_submenu'=>'Post',
        				'current_admin_submenu2'=>'PostCategory',
        				'title'=> __('Post Category List'));

        $modelClass = 'PostCategory';
        $modelAdminClass = sprintf('%s\Models\%sAdmin', $taxonomy, $modelClass);
        
        $q = Doctrine_Query::create()
            ->from($modelClass.' o')
            ->leftJoin('o.Translation t')
            ->where('o.site_id = ? AND o.taxonomy = ?', array(SITE_ID, $taxonomy));
        
        if(class_exists($modelAdminClass)){
            $adminClass = new $modelAdminClass();
        
            $cl = new ChangeList($taxonomy, 'PostCategory', $q,
	            $adminClass->list_display,
	            $adminClass->list_display_links,
	            $adminClass->list_filter,
	            $adminClass->date_hierarchy,
	            $adminClass->search_fields,
	            $adminClass->list_per_page,
	            $adminClass->row_actions);
        }else {
            $cl = new ChangeList($taxonomy, 'PostCategory', $q);
        }
        
        $templateArr['cl'] = $cl;
        render_to_response('admin/change_list.html', $templateArr);                
    }

	
	function admin_category_addchange($request, $taxonomy = 'Post', $id = false) {
		$templateArr = array('current_admin_menu'=>$taxonomy, 
				'current_admin_submenu'=>$taxonomy,
				'current_admin_submenu2'=>'PostCategory',
				'title'=> __('Post Category Add/Change'));

		if(!$request->user->has_perm($taxonomy.'.can_change_PostCategory')){
			Messages::Error(__('Do not have permission to do this.'));
			HttpResponseRedirect($_SERVER['HTTP_REFERER']);
		}
						
		$modelClass = 'PostCategory';
		if($taxonomy == 'Post'){
			$formClass = 'Pjango\Contrib\Post\Forms\PostCategoryForm';
		}else {
			$formClass = $taxonomy.'\Forms\PostCategoryForm';
		}		
		$formData = array();
		$lng = pjango_ini_get('LANGUAGE_CODE');
		
		//eğer kategori yoksa ekle
		$catTest = Doctrine_Query::create()
			->from('PostCategory o')
			->where('o.site_id = ? AND o.taxonomy = ?', array(SITE_ID, $taxonomy))
			->count();		
		
		if ($catTest<=0){
			$category = new PostCategory();
			$category->Translation[$lng]->name = __($taxonomy.' Main Category');
			$category->Translation[$lng]->slug = __(ucfirst($taxonomy).'-main-category');
			$category->site_id = SITE_ID;			
			$category->taxonomy = $taxonomy;
			$category->save();
			$treeObject = Doctrine_Core::getTable('PostCategory')->getTree();
			$treeObject->createRoot($category);			
		}
					
		if ($id){
			$addchangeObj = Doctrine_Query::create()
				->from('PostCategory o')
				->leftJoin('o.Translation t')
				->addWhere('o.site_id = ? AND o.id = ?', array(SITE_ID, $id))
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
					$addchangeObj->site_id = SITE_ID;
					$addchangeObj->Translation[$lng]->name = $formData['name'];
					$addchangeObj->Translation[$lng]->slug = $formData['slug'];
						
					if ($addchangeObj->state() == Doctrine_Record::STATE_TDIRTY || $addchangeObj->state() == Doctrine_Record::STATE_TCLEAN) {
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
		$templateArr['addchange_form'] = $form;
		$templateArr['taxonomy'] = $taxonomy;
		render_to_response('admin/addchange.html', $templateArr);
	}	
}