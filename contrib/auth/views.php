<?php
require_once 'pjango/shortcuts.php';

class AuthViews {
	
	function login() {
		
		if(isset($_POST['uname']) && isset($_POST['pwd'])){
			
			$q = Doctrine_Query::create()
                ->from('User u')
                ->addWhere('u.username = ? AND u.password = ? AND u.is_active = ?', array($_POST['uname'], md5($_POST['pwd']),1));     
            $result = $q->fetchOne(); 
            
            if($result){
            	$_SESSION['user'] = $result->toArray();
            	
            	echo '<meta http-equiv="Refresh" content="0;URL='.$GLOBALS['SITE_URL'].'/admin/">';
            	
            }else {
            	
            }
            
          
			
		}

		
		render_to_response('admin/login.html', array());
	}
	
	function logout() {
		session_destroy();
        echo '<meta http-equiv="Refresh" content="0;URL='.$GLOBALS['SITE_URL'].'/">';
	}
	

}
