<?php
require_once 'pjango/shortcuts.php';
require_once 'pjango/contrib/admin/util.php';
require_once 'pjango/http.php';
require_once 'pjango/core/mail.php';

class AuthViews {

	function admin_users($request) {
		$templateArr = array('current_admin_menu' => 'auth',
								'current_admin_submenu' => 'user',
								'title' => pjango_gettext('Users')); 

		$q = Doctrine_Query::create()
		    ->from('User u')
		    ->where('u.site_id = ?', pjango_ini_get('SITE_ID'));		

		if($request->user->is_superuser != 1){
			$q->addWhere("u.id = ?", $request->user->is_superuser);
		}

		$cl = new ChangeList($q);
		$templateArr['cl'] = $cl;

		render_to_response('admin/change_list.html', $templateArr);

	}

	function admin_user_addchange($request, $id = false) {
		$templateArr = array('current_admin_menu'=>'auth', 'current_admin_submenu'=>'user');
		$modelClass = 'User';
		$formClass = 'UserForm';
		$formData = array();

		if(!$request->user->has_perm('auth.can_change_User')){
			Messages::Error('Bu işlemi yapmak için yetkiniz yok.');
			HttpResponseRedirect($_SERVER['HTTP_REFERER']);
		}
		
		if ($id){
		    $modelObj = Doctrine::getTable($modelClass)->find($id);
		    if ($modelObj) {
		        $formData = $modelObj->toArray();
		        unset($formData['password']);
		        $templateArr['change_obj'] = $modelObj;
		        $templateArr['user_permissions'] = $modelObj->UserPermissions;
		        $templateArr['user_groups'] = $modelObj->UserGroups;
		    }
		}		

		if ($request->POST){
			$form = new $formClass($request->POST);
				
			if ($form->is_valid()){
				$modelObj = Doctrine::getTable($modelClass)->find($_POST['id']);

				if(!$modelObj) {
					$modelObj = new $modelClass();
				}

				try {
					$formData = $form->cleaned_data();
						
					if (strlen(trim($request->POST['password']))<1){
						unset($formData['password']);
					}
						
					$modelObj->fromArray($formData);
						
					$modelObj->save();

					$modelObj->unlink('Permissions');
					$modelObj->unlink('Groups');
					$modelObj->save();
					$modelObj->link('Permissions', $request->POST['permissions']);
					$modelObj->link('Groups', $request->POST['groups']);
					$modelObj->save();


					Messages::Info('İşlem başarıyla tamamlandı');
					HttpResponseRedirect('/admin/user/');
				} catch (Exception $e) {
					Messages::Error($e->getMessage());
				}

			}
		}

		

		$templateArr['permissions'] = Doctrine_Query::create()
    		->from('Permission')
    		->execute();

		$templateArr['groups'] = Doctrine_Query::create()
    		->from('Group g')
		    ->where('g.site_id = ?', pjango_ini_get('SITE_ID'))
    		->execute();

		if (!$form) $form = new $formClass($formData);

		$templateArr['addchange_form'] = $form->as_list();
		render_to_response('auth/admin/user_addchange.html', $templateArr);
	}

	function admin_user_delete($request, $id) {
		$templateArr = array('current_admin_menu' => 'users', 'current_admin_submenu' => 'users');

		$o = Doctrine::getTable('User')->find($id);

		if($o){
			 
			if(!$request->user->is_superuser){
				Messages::Error('Kullanıcıları sadece yetkili kullanıcı silebilir.');
			}else if($o->is_superuser){
				Messages::Error('Bu kullanıcıyı Silemezsiniz.');
			}else {
				try {
					$o->delete();
					Messages::Info('1 kayıt silindi.');
				} catch (Exception $e) {
					Messages::Error($e->getMessage());
				}
			}
		}

		HttpResponseRedirect('/admin/users/');
	}

	function admin_groups($request) {
		$templateArr = array('current_admin_menu'=>'auth', 'current_admin_submenu'=>'group');

		$q = Doctrine_Query::create()
    		->from('Group o');

		$cl = new ChangeList($q);
		$templateArr['cl'] = $cl;

		render_to_response('admin/change_list.html', $templateArr);
	}

	function admin_group_addchange($request, $id=false) {
		$templateArr = array('current_admin_menu'=>'auth', 'current_admin_submenu'=>'group');

		if(!$request->user->has_perm('auth.can_change_Group')){
			Messages::Error('Bu işlemi yapmak için yetkiniz yok.');
			HttpResponseRedirect($_SERVER['HTTP_REFERER']);
		}

		if ($request->POST){
			$form = new GroupForm($request->POST);
				
			if ($form->is_valid()){
				$group = Doctrine::getTable('Group')->find($_POST['id']);

				if(!$group) {
					$group = new Group();
				}

				try {
					$group->fromArray($form->cleaned_data());
					$group->save();

					$group->unlink('Permissions');
					$group->save();
					$group->link('Permissions', $request->POST['permissions']);
					$group->save();


					Messages::Info('İşlem başarıyla tamamlandı');
					HttpResponseRedirect('/admin/group/');
				} catch (Exception $e) {
					Messages::Error($e->getMessage());
				}

			}
		}

		$formData = array();

		if ($id){
			$group = Doctrine::getTable('Group')->find($id);
			if ($group) {
				$formData = $group->toArray();
				$templateArr['group_permissions'] = $group->GroupPermissions;
			}
		}

		$permissions = Doctrine_Query::create()
		->from('Permission')
		->execute();
		$templateArr['permissions'] = $permissions;


		if (!$form) $form = new GroupForm($formData);

		$templateArr['form'] = $form->as_list();
		render_to_response('auth/admin/group_addchange.html', $templateArr);
	}

	function admin_permissions($request) {
		$templateArr = array('current_admin_menu'=>'auth',
				'current_admin_submenu'=>'permission',
				'title'=>pjango_gettext('Permissions'),
				'addchange_url'=>'/admin/addchange/Permission/'); 

		$q = Doctrine_Query::create()
		->from('Permission o');

		$cl = new ChangeList($q);
		$templateArr['cl'] = $cl;

		render_to_response('admin/change_list.html', $templateArr);
	}

	function login($request) {
		$templateArr = array();
		$modelClass = 'User';
		$formClass = 'LoginForm';
		
		if($request->user->is_authenticated()){
		    HttpResponseRedirect(pjango_ini_get('LOGIN_REDIRECT_URL'));
		}		

		if ($request->POST){
			$form = new $formClass($request->POST);
			
			try {
			    if (!$form->is_valid()) throw new Exception(pjango_gettext('There are some errors, please correct them below.'));
			    $formData = $form->cleaned_data();
			
			    User::authenticate($formData['username'], $formData['password']);
			    HttpResponseRedirect(pjango_ini_get('LOGIN_REDIRECT_URL'));
			} catch (Exception $e) {
			    Messages::Error($e->getMessage());
			}			
		}

		if (!$form) $form = new $formClass($formData);
		$templateArr['addchange_form'] = $form->as_list();

		render_to_response('auth/login.html', $templateArr);
	}

	function logout($request) {
		$pt = PjangoToken::check_token($_COOKIE['rme']);

		if ($pt){
			$pt->status = PjangoToken::STATUS_USED;
		}

		unset($_COOKIE['rme']);
		setcookie('rme', '', -1);
		session_destroy();
		HttpResponseRedirect('/');
	}
}
