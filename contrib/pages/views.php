<?php
require_once 'pjango/shortcuts.php';
require_once 'pjango/contrib/admin/util.php';
require_once 'pjango/http.php';

class PagesViews {
	
	function flatpage($request, $url) {
		$template = 'pages/default.html';
		$templateArr = array();
		
		
		$fp = Doctrine_Query::create()
			->from('Post o')
			->leftJoin('o.Translation t')
	        ->where('t.slug = ?',array($url))
			->fetchOne();
		
		$page = Doctrine_Query::create()
			->from('Post o')
			->leftJoin('o.Translation t')
	        ->where('o.id = ?',array($fp->id))
			->fetchOne();
        
        $templateArr['page'] = $page;
        
        if (strlen($page->template)>0)
        	$template = $page->template;
        
        
		render_to_response($template, $templateArr);
	}	

	function admin_pages($request) {
		$templateArr = array('current_admin_menu' => 'pages',
				'current_admin_submenu' => 'pages',
				'title' => pjango_gettext('Pages')); 	
		
		$q = Doctrine_Query::create()
			->from('Post p')
			->where('p.post_type = ?', Post::TYPE_PAGE);
		
		$cl = new ChangeList($q);
		$templateArr['cl'] = $cl;		
		        
		render_to_response('admin/change_list.html', $templateArr);
	}

    function admin_pages_addchange($request, $id = false) {
		$templateArr = array('current_admin_menu' => 'pages',
				'current_admin_submenu' => 'pages',
				'title' => pjango_gettext('Pages')); 
    	    	
		$model = 'Post';
		$formClass = 'PostForm';   
		$formData = array(); 	
		
		
		if ($id){
			$obj = Doctrine_Query::create()
				->from('Post o')
				->leftJoin('o.Translation t')
				->addWhere('o.id = ?', array($id))
				->fetchOne();
			 
			if($obj){
				$templateArr['addchange_obj'] = $obj;
				$formData = $obj->toArray();
		
				$lng = pjango_ini_get('LANGUAGE_CODE');
		
				$formData['title'] = $obj->Translation[$lng]->title;
				$formData['content'] = $obj->Translation[$lng]->content;
				$formData['excerpt'] = $obj->Translation[$lng]->excerpt;
				$formData['slug'] = $obj->Translation[$lng]->slug;		
			}
			 
		
		
		
		}	
    	
		if ($request->POST){			
			$form = new $formClass($request->POST);
			
			if ($form->is_valid()){
				$formData = $form->cleaned_data();
				$obj = Doctrine::getTable($model)->find($request->POST['id']);
				if(!$obj) $obj = new $model();
				
				try {
					$obj->fromArray($formData);	
					$obj->post_type = Post::TYPE_PAGE;
					$obj->Author = $request->user;
					$obj->save();					
					
					$lng = pjango_ini_get('LANGUAGE_CODE');
					
					$obj->Translation[$lng]->title = stripslashes($request->POST['title']);
	            	$obj->Translation[$lng]->content = stripslashes($request->POST['content']);
	            	$obj->Translation[$lng]->excerpt = stripslashes($request->POST['excerpt']);
	            	$obj->Translation[$lng]->slug = stripslashes($request->POST['slug']);
	            	$obj->save();

		            
		            Messages::Info(pjango_gettext('The operation completed successfully'));
					HttpResponseRedirect('/admin/pages/');
				} catch (Exception $e) {
					Messages::Error($e->getMessage());            	
            	}					
				
			}
        }
        
        if (!$form) $form = new $formClass($formData);
		$templateArr['addchange_form'] = $form->as_list();	
        
    	render_to_response('pages/admin/addchange.html', $templateArr);
    }	
    
    function admin_delete($request, $id = false) {
    	
        $post = Doctrine::getTable('Post')->find($id);
        
        if ($post) {
        	
        	try {
	        	$post->unlink('PostMeta');
	        	$post->save();
	            $post->delete();	            
        		Messages::Info(pjango_gettext('Item moved to the Trash.'));
        	} catch (Exception $e) {
        		Messages::Info($e->getMessage());
        	}
        }
        
        HttpResponseRedirect('/admin/pages/'); 	               
    }
    
    function admin_changestatus($request, $id = false) {
    	
        $post = Doctrine::getTable('Post')->find($id);
        
        if ($post) {        	
        	try {
        		$post->status = $_POST['value'];
	        	$post->save();
	        	echo pjango_gettext("post.status ".$post->status);
        	} catch (Exception $e) {
        		//YOKBİŞE
        	}
        }	               
    }
	
}