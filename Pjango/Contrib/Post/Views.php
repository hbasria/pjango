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
						'title'=>pjango_gettext($taxonomy));
		
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
		
		$templateArr['extraheads'] = array(
    		sprintf('<script type="text/javascript" src="%s/js/post_addchange.js"></script>', pjango_ini_get('ADMIN_MEDIA_URL'))
		);		
		
		if(is_array($modelAdmin->third_level_navigation)){
			$modelAdmin->third_level_navigation[0]['class'] = 'active';
			if(isset($modelAdmin->third_level_navigation[1])){
				$modelAdmin->third_level_navigation[1]['class'] = 'passive after-active';
			}
			$templateArr['third_level_navigation'] = $modelAdmin->third_level_navigation;
		}
				
		
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
				$formData['pub_date'] = date(pjango_ini_get('DATE_FORMAT'), strtotime($addchangeObj->pub_date));
				
				
				if ($addchangeObj->Categories && count($addchangeObj->Categories) > 0){
					foreach ($addchangeObj->Categories as $categoryItem) {
						$formData['categories'][] = $categoryItem->id;
					}
				}		

				$metaData = PjangoMeta::getMeta($contentType->id, $addchangeObj->id);
				
				foreach ($metaData as $metaDataItem) {
					$formData[$metaDataItem->meta_key] = $metaDataItem->meta_value;
				}	
				
				if(is_array($modelAdmin->third_level_navigation)){
					for ($i = 0; $i < count($modelAdmin->third_level_navigation); $i++) {
						$templateArr['third_level_navigation'][$i]['url'] = $modelUrl.$id."/".$templateArr['third_level_navigation'][$i]['key']."/";
					}
				}				
				
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
				$addchangeObj->pub_date = date('Y-m-d H:i:s', $formData['pub_date']);
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
				HttpResponseRedirect('/admin/'.$taxonomy.'/'.$modelClass.'/'.$addchangeObj->id.'/edit/');
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
	
    function admin_delete($request, $taxonomy = 'Post',$id) {
        $post = Doctrine::getTable('Post')->find($id);
        
        if($post){
        	try {        		
        		$contentType = ContentType::get_for_model('Post', $taxonomy);
        		
        		$deleted = Doctrine_Query::create()
	        		->delete('PjangoMedia o')
	        		->where('o.content_type_id = ? AND o.object_id = ?', array($contentType->id, $post->id))
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
        				'current_admin_submenu2'=>'PostCategory');

        $className = 'PostCategory';
        $adminClassName = ucfirst($taxonomy).'Admin';
        
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

	
	function admin_category_addchange($request, $taxonomy = 'post', $id = false) {
		$templateArr = array('current_admin_menu'=>$taxonomy, 
				'current_admin_submenu'=>$taxonomy,
				'current_admin_submenu2'=>'PostCategory');

		if($request->user->has_perm($taxonomy.'can_change')){
		    
		}
		
		$formData = array();
		$modelClass = 'PostCategory';
		$formClass = 'PostCategoryForm';

		$lng = pjango_ini_get('LANGUAGE_CODE');
		$site = pjango_ini_get('SITE_ID');
		
		//eğer kategori yoksa ekle
		$catTest = Doctrine_Query::create()
			->from('PostCategory o')
			->where('o.site_id = ? AND o.taxonomy = ?', array($site, $taxonomy))
			->count();		
		
		if ($catTest<=0){
			$category = new PostCategory();
			$category->Translation[$lng]->name = pjango_gettext($taxonomy.' Main Category');
			$category->Translation[$lng]->slug = pjango_gettext(ucfirst($taxonomy).'-main-category');
			$category->site_id = $site;			
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
			$form = new PostCategoryForm($taxonomy, $request->POST);
			
			if ($form->is_valid()){
				$formData = $form->cleaned_data();
				
				if(!$addchangeObj) $addchangeObj = new $modelClass();
				
				try {
					$parent = Doctrine::getTable($modelClass)->find($formData['parent_id']);

					$addchangeObj->taxonomy = $taxonomy;
					$addchangeObj->site_id = pjango_ini_get('SITE_ID');
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
		
		if (!$form) $form = new PostCategoryForm($taxonomy, $formData);
		$templateArr['addchange_form'] = $form->as_list();
		$templateArr['taxonomy'] = $taxonomy;
		render_to_response('post/admin/category_addchange.html', $templateArr);
	}
	
	function admin_images($request, $taxonomy = 'post', $id=false, $image_id=false) {
	    $templateArr = array('current_admin_menu'=>$taxonomy,
								'current_admin_submenu'=>$taxonomy, 
								'current_admin_submenu2'=>'Post',
								'title'=>pjango_gettext($taxonomy.' images'));
	    
	    $taxonomyUrl = sprintf('%s/admin/%s/Post/%d', pjango_ini_get('SITE_URL'), $taxonomy, $id);
	
	    $templateArr['third_level_navigation'] = array(
    	    array('key'=>'properties', 'url'=>$taxonomyUrl.'/edit/',   'name'=>pjango_gettext($taxonomy.' properties'), 'class'	=> 'passive'),
    	    array('key'=>'images',     'url'=>$taxonomyUrl.'/images/', 'name'=>pjango_gettext($taxonomy.' images'), 'class'	=> 'active')
	    );
	
	    $modelClass = 'Post';
	
	    $q = Doctrine_Query::create()
    	    ->from('PjangoMedia o')
    	    ->where('o.site_id = ? AND o.content_type_id = ? AND o.object_id = ?', array(pjango_ini_get('SITE_ID'), Post::get_content_type_id(), $id));
	
	    $cl = new ChangeList($q);
	    $templateArr['cl'] = $cl;
	
	    render_to_response('admin/change_list.html', $templateArr);
	}
	
	function admin_images_addchange($request, $taxonomy = 'post', $id=false, $image_id=false) {
	    $templateArr = array('current_admin_menu'=>$taxonomy,
								'current_admin_submenu'=>$taxonomy, 
								'current_admin_submenu2'=>$taxonomy,
								'title'=>pjango_gettext($taxonomy.' images'));
	
	    $templateArr['extraheads'] = array(
    	    sprintf('<script type="text/javascript" src="%s/js/filemanager/filemanager.js"></script>', pjango_ini_get('ADMIN_MEDIA_URL')),
    	    sprintf('<script type="text/javascript" src="%s/js/PjangoMedia_addchange.js"></script>', pjango_ini_get('ADMIN_MEDIA_URL'))
	    );
	    
	    $taxonomyUrl = sprintf('/%s/admin/%s/Post/%d', pjango_ini_get('SITE_URL'), $taxonomy, $id);
	
	    $templateArr['third_level_navigation'] = array(
    	    array('key'=>'properties', 'url'=>$taxonomyUrl.'/edit/',   'name'=>pjango_gettext($taxonomy.' properties'), 'class'	=> 'passive'),
    	    array('key'=>'images',     'url'=>$taxonomyUrl.'/images/', 'name'=>pjango_gettext($taxonomy.' images'), 'class'	=> 'active')
	    );
	
	    $modelClass = 'PjangoMedia';
	    $formClass = $modelClass.'Form';
	    $formData = array();
	
	    $post = Doctrine_Core::getTable('Post')->find($id);
	
	    if(!$post){
	        Messages::Info(pjango_gettext('No records found'));
	        HttpResponseRedirect($taxonomyUrl);
	    }
	
	    $formData['object_id'] = $post->id;
	    $formData['content_type_id'] = $post->get_content_type_id();
	
	    if ($image_id){
	        $modelObj = Doctrine_Query::create()
	        ->from($modelClass.' o')
	        ->where('o.id = ?', $image_id)
	        ->fetchOne();
	
	        if ($modelObj) {
	            $formData = $modelObj->toArray();
	        }
	    }
	
	    if ($request->POST){
	        $form = new PjangoMediaForm($request->POST);
	
	        try {
	            if (!$form->is_valid()) throw new Exception('There are incomplete required fields. Please complete them.');
	            $formData = $form->cleaned_data();
	            if(!$modelObj) $modelObj = new $modelClass();
	
	            $modelObj->fromArray($formData);
	
	            if ($modelObj->state() == Doctrine_Record::STATE_TCLEAN){
	                $modelObj->created_by = $request->user->id;
	                $modelObj->updated_by = $request->user->id;
	            }else {
	                $modelObj->updated_by = $request->user->id;
	            }
	
	            $modelObj->site_id = pjango_ini_get('SITE_ID');
	            $modelObj->save();
	
	            // 				default image seçilmiş ise diğerlerinin defaultunu kaldır ve resmi galeriye uygula
	            if($modelObj->is_default){
	                $updated = Doctrine_Query::create()
	                ->update($modelClass.' o')
	                ->set('o.is_default', '?', false)
	                ->where('o.content_type_id = ? AND o.object_id = ? AND o.id != ?', array($post->get_content_type_id(), $post->id, $modelObj->id))
	                ->execute();
	
	                $metaValues = array('meta_image'=>$formData['image']);
	                PjangoMeta::setMeta($post->get_content_type_id(), $post->id, false, $metaValues);
	            }
	
	            Messages::Info(pjango_gettext('The operation completed successfully'));
	            HttpResponseRedirect($taxonomyUrl.'/images/');
	        } catch (Exception $e) {
	            Messages::Error($e->getMessage());
	        }
	
	    }
	
	    if (!$form) $form = new PjangoMediaForm($formData);
	    $templateArr['addchange_form'] = $form->as_list();
	    	
	    render_to_response('admin/addchange.html', $templateArr);
	}	
	
	function admin_images_delete($request, $taxonomy = 'Post', $id=false, $image_id=false) {
	    $taxonomyUrl = sprintf('/%s/admin/%s/Post/%d', pjango_ini_get('SITE_URL'), $taxonomy, $id);
	    
	    $o = Doctrine::getTable('PjangoMedia')->find($image_id);
	
	    if($o){
	        try {
	            $o->delete();
	            Messages::Info(pjango_gettext('1 record has been deleted.'));
	        } catch (Exception $e) {
	            Messages::Error($e->getMessage());
	        }
	
	    }
	
	    HttpResponseRedirect($taxonomyUrl.'/images/');
	}	
	
}