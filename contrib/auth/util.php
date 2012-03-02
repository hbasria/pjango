<?php 



class AnonymousUser {
	
	function get_available_bonuses() {
		return "0";
	}
	
}


function get_user() {

	if ($_SESSION['user']['id']){
		$user = Doctrine_Query::create()
				->from('User u')		
	            ->addWhere('u.id = ?', $_SESSION['user']['id'])
	            ->fetchOne(); 		
		return $user;
	}elseif (isset($_COOKIE['rme'])){

		$pt = PjangoToken::check_token($_COOKIE['rme']);
			
		if ($pt){
			$user = authenticate(false, false, $pt->object_id);
		}else {
			$user =  new AnonymousUser();
		}
		
		return $user;
		
	}else {
		return new AnonymousUser();
	}
}

function authenticate($username = false, $password = false, $userId = false){
	
	if ($userId){
		$q = Doctrine_Query::create()
	    		->from('User u')
	            ->where('u.id = ? AND u.is_active = ?', array($userId,1));		
	}else {
		$q = Doctrine_Query::create()
	    		->from('User u')
	            ->where('(u.email = ? OR u.username = ?) AND u.password = ? AND u.is_active = ?', array($username,$username, md5($password),1));  		
	}
	
	$user = $q->fetchOne(); 
	
	if (!$user) throw new Exception(pjango_gettext("Invalid username/password Error"));
	
	//cooki set et beni hatırla için
	if (isset($_POST['remember_me'])){
		$user->setCookie();
	}	
	
	$user->last_login = date('Y-m-d H:i:s');
	$user->save();
	$_SESSION['user'] = $user->toArray();
	
	return $user;
}

