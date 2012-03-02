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
		->from('User u');

		if($_SESSION['user']['is_superuser'] != 1){
			$q->addWhere("u.id = ?", $_SESSION['user']['id']);
		}

		$cl = new ChangeList($q);
		$templateArr['cl'] = $cl;

		render_to_response('admin/change_list.html', $templateArr);

	}

	function admin_user_addchange($request, $id = false) {
		$templateArr = array('current_admin_menu'=>'auth', 'current_admin_submenu'=>'user');
		$modelClass = 'User';
		$formClass = 'UserForm';

		if(!$request->user->has_perm('auth.change_User')){
			Messages::Error('Bu işlemi yapmak için yetkiniz yok.');
			HttpResponseRedirect($_SERVER['HTTP_REFERER']);
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

		$formData = array();

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

		$permissions = Doctrine_Query::create()
		->from('Permission')
		->execute();
		$templateArr['permissions'] = $permissions;

		$groups = Doctrine_Query::create()
		->from('Group')
		->execute();
		$templateArr['groups'] = $groups;


		if (!$form) $form = new $formClass($formData);

		$templateArr['addchange_form'] = $form->as_list();
		render_to_response('auth/admin/user_addchange.html', $templateArr);
	}

	function admin_user_delete($request, $id) {
		$templateArr = array('current_admin_menu' => 'users', 'current_admin_submenu' => 'users');

		$o = Doctrine::getTable('User')->find($id);

		if($o){
			 
			if($_SESSION['user']['is_superuser'] != 1){
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

	function registration($request) {
		$templateArr = array();
		$modelClass = 'User';
		$formClass = 'RegistrationForm';

		$formData = array();

		if ($request->POST){
			$form = new $formClass($request->POST);
				
			if ($form->is_valid()){
				$user = new User();

				try {
					$formData = $form->cleaned_data();
						
					$user->saveWithContact($formData);
					$user->setAllMeta(array('campaign_city', 'gender'), $formData);
						
					//eğer referans ile gelmişse bonus kaydı yap  ?
					$referrer = Doctrine_Query::create()
					->from('User u')
					->where('u.username = ?', array($_GET['ref']))
					->fetchOne();
					 

					 
					if ($referrer){
						$bonusTxn = new CampaignBonusTxn();
						$bonusTxn->status = CampaignBonusTxn::STATUS_NEW;
						$bonusTxn->amount = 6;
						$bonusTxn->description = $formData['first_name'];
						$bonusTxn->user_id = $referrer->id;
						$bonusTxn->referrer_id = $user->id;
						$bonusTxn->save();
					}
					 
						
					//Kayıt maillerini gönder
					try {
						$mail = get_mailer('smtp');

						$defaultFrom = pjango_ini_get('DEFAULT_FROM_EMAIL');
						$defaultSubjectPrefix = pjango_ini_get('EMAIL_SUBJECT_PREFIX');

						$mail->From = $defaultFrom;
						$mail->Subject    = $defaultSubjectPrefix." Registration";
						$mail->MsgHTML(render_to_response('mail/registration.html', $formData, true));

						$mail->AddAddress($formData['email'], sprintf("%s %s", $formData['first_name'], $formData['last_name']));
						$mail->Send();
						 
						Messages::Info(pjango_gettext("An email has been sent and you must follow the link to verify your account before you can login."));
					} catch (Exception $ex) {
						Messages::Error($ex->getMessage());
					}
						
					//kullanıcıyı login et
					$user->last_login = date('Y-m-d H:i:s');
					$user->save();
					$_SESSION['user'] = $user->toArray();
						
						
						
					HttpResponseRedirect('/');
				} catch (Exception $e) {
					unset($formData['password']);
					unset($formData['password_confirm']);
						
					Messages::Error('Bu e-posta adresi ile kayıtlı üyemiz bulunmaktadır.');
				}

			}
		}

		if (!$form) $form = new $formClass($formData);
		$templateArr['addchange_form'] = $form;



		render_to_response('auth/registration.html', $templateArr);
	}

	function login($request) {
		$templateArr = array();
		$modelClass = 'User';
		$formClass = 'LoginForm';

		if ($request->POST){
			$form = new $formClass($request->POST);
				
			if ($form->is_valid()){
				
				try {
					$user = authenticate($_POST['username'], $_POST['password']);
					HttpResponseRedirect('/');
				} catch (Exception $e) {
					Messages::Error($e->getMessage());
				}
			}
		}

		if (!$form) $form = new $formClass($formData);
		$templateArr['addchange_form'] = $form->as_list();

		render_to_response('auth/login.html', $templateArr);
	}

	function logout($request) {

// 		$pt = PjangoToken::check_token($_COOKIE['rme']);

// 		if ($pt){
// 			$pt->status = PjangoToken::STATUS_USED;
// 		}

		unset($_COOKIE['rme']);
		setcookie('rme', '', -1);
		session_destroy();
		HttpResponseRedirect('/');
	}

	function profile($request) {
		$templateArr = array();

		$formClass = 'ProfileForm';
		$formData = array();
		$profile = get_user();

		if ($request->POST){
			$form = new $formClass($request->POST);
				
			if ($form->is_valid()){
				$formData = $form->cleaned_data();

				try {
					if ($request->POST['password'] != $request->POST['password_confirm']){
						throw new Exception('Girdiğiniz şifreler uyuşmuyor. Şifrenizi değiştirmek istemiyorsanız boş bırakın.');
					}
						
					$profile->saveWithContact($formData, true);
					$profile->setAllMeta(array('campaign_city', 'gender', 'bill_name', 'bill_identity_no',
		            		'bill_phone', 'bill_zipcode', 'bill_address'), $request->POST);

					if (strlen($request->POST['password'])>0 && $request->POST['password'] == $request->POST['password_confirm']){
						$profile->password = $formData['password'];
						$profile->save();

						Messages::Info("Yeni şifrenizle giriş yapınız.");
						HttpResponseRedirect('/auth/logout/');

					}

					Messages::Info(pjango_gettext('The operation completed successfully'));
				} catch (Exception $e) {
					Messages::Error($e->getMessage());
				}
			}else {
				Messages::Error(implode('<br>', Messages::HandleFormErrors($form->get_errors())));
			}
				
		}

		if ($profile && $profile instanceof User){
			$templateArr['profile'] = $profile;
			$templateArr['profile_meta'] = $profile->getAllMeta();
				
			$formData = array_merge($formData, $profile->toArray());
			$formData = array_merge($formData, $templateArr['profile_meta']);

		}else {
			HttpResponseRedirect('/auth/login/');
		}



		unset($formData['password']);
		unset($formData['password_confirm']);

		if (!$form) $form = new $formClass($formData);
		$templateArr['addchange_form'] = $form;

		render_to_response('auth/profile.html', $templateArr);
	}




	function lostpasword($request) {
		$templateArr = array();

		render_to_response('auth/lostpasword.html', $templateArr);
	}


	function password_change($request) {
		$templateArr = array();
		$formClass = 'PasswordChangeForm';
		$formData = array();

		if ($request->POST){
			$form = new $formClass($request->POST);
				
			if ($form->is_valid()){
				$formData = $form->cleaned_data();

				try {
						
					if ($formData['password'] != $formData['password_confirm'])
					throw new Exception('Şifreleriniz uyuşmuyor...');
						
					$request->user->changePassword($request->POST['password']);
					$templateArr['success_message'] = 'Şifreniz başarıyla değiştirildi.';
				} catch (Exception $e) {
					$templateArr['error_message'] = $e->getMessage();
				}
			}
		}

		if (!$form) $form = new $formClass($formData);
		$templateArr['addchange_form'] = $form;

		render_to_response('auth/password_change.html', $templateArr);
	}


	function password_reset($request) {
		$templateArr = array();

		$checkToken = isset($_GET['token']) ? $_GET['token'] : false;

		if ($checkToken){
			$token = PjangoToken::check_token($checkToken);
			$templateArr['check_token'] = $token;
		}

		if (isset($request->POST['send_token'])){
				
			$user = Doctrine_Query::create()
			->from('User u')
			->where('u.email = ?', array($request->POST['email']))
			->fetchOne();

			if ($user){

				try {
					$token = PjangoToken::make_token('User', $user->id);
						
					$templateArr['displayname'] = $user->displayname;
					$templateArr['token'] = $token->token;
						
					$mail = get_mailer('smtp');
					$mail->MsgHTML(render_to_response('mail/password_reset.html', $templateArr, true));
					$mail->AddAddress($user->email, 'Fırsatavcıları');
					$mail->Send();

					$templateArr['success_message'] = 'Şifre değiştirme linkiniz sistemde bulunan mail adresine gönderilmiştir.';
				} catch (Exception $ex) {
					$templateArr['error_message'] = 'Doğrulama kodunuz gönderilirken hata oluştu.';
						
				}



			}else {
				$templateArr['error_message'] = 'Sistemde bu eposta adresiye kayıtlı kullanıcı bulunamadı.';
			}

		}

		if (isset($request->POST['change_password'])){
				
			if (strlen($request->POST['password'])>0 && $request->POST['password'] == $request->POST['password_confirm']){

				if ($token){
					$ctype = $token->ContentType;
					$obj = $ctype->get_object_for_this_type($token->object_id);

					if ($obj instanceof User){
						$obj->changePassword($request->POST['password']);
						$templateArr['success_message'] = 'Şifre Başarıyla değiştirildi.';
						$token->status = PjangoToken::STATUS_USED;
						$token->save();
					}
						
				}else {
					$templateArr['error_message'] = 'token yok veya süresi geçmiş.';
				}


			}else {
				$templateArr['error_message'] = 'Girdiğiniz şifeler uyuşmuyor.';
			}
				
		}





		render_to_response('auth/password_reset.html', $templateArr);
	}






}
