<?php 

function login_required(){
    $user = User::get_current();
    
    if(!$user->is_authenticated()){
        HttpResponseRedirect(pjango_ini_get('LOGIN_URL'));
        exit();
    }    
}

function permission_required($permissions = array()){
	$user = User::get_current();
	
	if (is_string($permissions)) {
		$permissions = array($permissions);
	}
	
	foreach ($permissions as $value) {
		if(!$user->has_perm($value)){
			Pjango\Util\Messages::Error(__('You don\'t have the permission for this operation'));
			HttpResponseRedirect($_SERVER['HTTP_REFERER']);
			exit();
		}		
	}
}

