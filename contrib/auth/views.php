<?php
require_once 'pjango/shortcuts.php';
require_once 'pjango/contrib/admin/util.php';
require_once 'pjango/http.php';

class AuthViews {
	
	function admin_users() {
		$templateArr = array('current_admin_menu' => 'users', 
								'current_admin_submenu' => 'users',
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
	
	function admin_user_addchange($id = false) {
		$templateArr = array('current_admin_menu' => 'users', 'current_admin_submenu' => 'users'); 
		
		if (isset($_POST['save'])){
			$user = Doctrine::getTable('User')->find($_POST['id']);
			$isNew = false;
			
			if(!$user){
        		$user = new User();
        		$isNew = true;
        	}		
        	
			try {
        		$user->fromArray($_POST);
        		$user->password = md5($_POST['pass1']);        		
        		$user->save();
        		
        		if ($isNew){
        			Messages::Info(pjango_gettext('New user created.'));	
        		}else {
        			Messages::Info(pjango_gettext('User updated.'));        			
        		}
        		
        		HttpResponseRedirect('/admin/users/');        
        	} catch (Exception $e) {
        		Messages::Error($e->getMessage());
        	}     
        	        	
		}
		
		if ($id){
            $q = Doctrine_Query::create()
                ->from('User u')
                ->where('u.id = ?',array($id));
                
            $templateArr['user_obj'] = $q->fetchOne();
		}		
		
		render_to_response('admin/user_addchange.html', $templateArr);		
	}
	
    function admin_user_delete($id) {
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
	
	function login() {
		if(isset($_POST['uname']) && isset($_POST['pwd'])){
			
			$q = Doctrine_Query::create()
                ->from('User u')
                ->addWhere('u.username = ? AND u.password = ? AND u.is_active = ?', array($_POST['uname'], md5($_POST['pwd']),1));     
            $user = $q->fetchOne(); 
            
            if($user){
            	$user->last_login = date('Y-m-d H:i:s');
            	$user->save();
            	$_SESSION['user'] = $user->toArray();
            	HttpResponseRedirect('/admin/');
            }else {
            	
            }
            
		}
		
		render_to_response('admin/login.html', array());
	}
	
	function logout() {
		session_destroy();
        HttpResponseRedirect('/');
	}
	

}
