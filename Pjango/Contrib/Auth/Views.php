<?php

use Pjango\Util\Messages;

class AuthViews {

	function login($request) {
		$templateArr = array();
		$modelClass = 'User';
		
		$formData = array();
		
		if($request->user->is_authenticated()){
		    HttpResponseRedirect(pjango_ini_get('LOGIN_REDIRECT_URL'));
		}		

		if ($request->POST){
			$form = new Pjango\Contrib\Auth\Forms\LoginForm($request->POST);
			
			try {
			    if (!$form->is_valid()) throw new Exception(pjango_gettext('There are some errors, please correct them below.'));
			    $formData = $form->cleaned_data();
			
			    $user = User::authenticate($formData['username'], $formData['password']);
			    
			    HttpResponseRedirect(pjango_ini_get('LOGIN_REDIRECT_URL'));
			} catch (Exception $e) {
			    Messages::Error($e->getMessage());
			}			
		}

		if (!$form) $form = new Pjango\Contrib\Auth\Forms\LoginForm($formData);
		
		$templateArr['addchange_form'] = $form->as_list();

		render_to_response('auth/login.html', $templateArr);
	}

	function logout($request) {
		$pt = PjangoToken::check_token($_COOKIE['rme']);

		if ($pt){
			$pt->status = PjangoToken::STATUS_USED;
			$pt->save();
		}

		unset($_COOKIE['rme']);
		setcookie('rme', '', time() - 3600);
		session_destroy();
		HttpResponseRedirect('/');
	}
	
	function admin_user_addchange($request, $id = false) {
	    $templateArr = array('current_admin_menu'=>'Auth', 
	    		'current_admin_submenu'=>'Auth',
	            'current_admin_submenu2'=>'User',
	    		'title'=>'Auth User Add/Change');
	    
	    $modelClass = 'User';
	    $formClass = 'Pjango\Contrib\Auth\Forms\UserForm';
	    $formData = array();
	
	    if(!$request->user->has_perm('Auth.can_change_User')){
	        Messages::Error(__('Do not have permission to do this.'));
	        HttpResponseRedirect($_SERVER['HTTP_REFERER']);
	    }
	
	    if ($id){
	        $modelObj = Doctrine_Query::create()
		        ->from($modelClass.' u')
		        ->leftJoin('u.UserGroups')
		        ->leftJoin('u.UserPermissions')
		        ->where('u.id = ?', $id)
		        ->fetchOne();
	        
	        if ($modelObj) {
	            $formData = $modelObj->toArray();
	            unset($formData['password']);	       
	            
	            foreach ($modelObj->UserGroups as $userGroup) {
	            	$formData['groups'][] = $userGroup->group_id;
	            }
	            
	            foreach ($modelObj->UserPermissions as $userPermission) {
	            	$formData['permissions'][] = $userPermission->permission_id;
	            }
	        }
	    }
	
	    if ($request->POST){
	        $form = new $formClass($request->POST);
	        
	        try {
	            if (!$form->is_valid()) throw new Exception('There are incomplete required fields. Please complete them.');
	            $formData = $form->cleaned_data();
	            
// 	            şifre girilmemiş ise $formData dan temizle
	            if (strlen(trim($request->POST['password']))<1){
	                unset($formData['password']);
	            }	            
	            
	            if(!$modelObj) {
	                $modelObj = new $modelClass();
	                
	                if (class_exists('Contact')) {
	                    $modelObj->Contact = new Contact();
	                    $modelObj->Contact->is_person = true;
	                    $modelObj->Contact->is_active = true;	
	                    $modelObj->Contact->created_by = $request->user->id;
	                    $modelObj->Contact->updated_by = $request->user->id;
	                }
	                
	                
	            }
	            
	            $modelObj->fromArray($formData);
	            $modelObj->save();
	            
	            if (class_exists('Contact')) {    	            
    	            $modelObj->Contact->set_title($modelObj->displayname);
    	            
    	            if (isset($formData['types'])){
    	                $modelObj->Contact->link('Types', $formData['types']);
    	            }
	            }
	            
	            
	            $modelObj->unlink('Permissions');
	            $modelObj->unlink('Groups');
	            $modelObj->link('Permissions', $request->POST['permissions']);
	            $modelObj->link('Groups', $request->POST['groups']);	
	            $modelObj->site_id = SITE_ID;
	            $modelObj->save();
	            
	            Messages::Info(pjango_gettext('The operation completed successfully'));
	            HttpResponseRedirect('/admin/Auth/User/');	            

            } catch (Exception $e) {
                Messages::Error($e->getMessage());
            }	            	
	    }
	
	    if (!$form) $form = new $formClass($formData);
	    $templateArr['addchange_form'] = $form;
	    render_to_response('admin/addchange.html', $templateArr);
	}

	function admin_group_addchange($request, $id = false) {
	    $templateArr = array('current_admin_menu'=>'Auth',
    		'current_admin_submenu'=>'Auth',
            'current_admin_submenu2'=>'Group',
	    	'title'=>'Auth Group Add/Change');
	     
	    $modelClass = 'Group';
	    $formClass = 'Pjango\Contrib\Auth\Forms\GroupForm';
	    $formData = array();
	
	    if(!$request->user->has_perm('Auth.can_change_Group')){
	        Messages::Error(__('Do not have permission to do this.'));
	        HttpResponseRedirect($_SERVER['HTTP_REFERER']);
	    }
	
	    if ($id){
	        $modelObj = Doctrine::getTable($modelClass)->find($id);
	        if ($modelObj) {
	            $formData = $modelObj->toArray();
	            $templateArr['group_permissions'] = $modelObj->GroupPermissions;
	        }
	    }
	
	    if ($request->POST){
	        $form = new $formClass($request->POST);
	         
	        try {
	            if (!$form->is_valid()) throw new Exception('There are incomplete required fields. Please complete them.');
	            $formData = $form->cleaned_data();
	             
	            if(!$modelObj) {
	                $modelObj = new $modelClass();
	            }
	             
	            $modelObj->fromArray($formData);
	            $modelObj->save();
	             
	            $modelObj->unlink('Permissions');
	            $modelObj->link('Permissions', $request->POST['permissions']);
	            $modelObj->site_id = SITE_ID;
	            $modelObj->save();
	             
	            Messages::Info(pjango_gettext('The operation completed successfully'));
	            HttpResponseRedirect('/admin/Auth/Group/');
	
	        } catch (Exception $e) {
	            Messages::Error($e->getMessage());
	        }
	
	
	    }
	
	    $templateArr['permissions'] = Doctrine_Query::create()
    	    ->from('Permission')
    	    ->execute();
	
	    if (!$form) $form = new $formClass($formData);
	
	    $templateArr['addchange_form'] = $form;
	    render_to_response('admin/addchange.html', $templateArr);
	}	
}
