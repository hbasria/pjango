<?php 
class AnonymousUser {
	
    function is_authenticated() {
        return false;
    }
    
    function has_perm() {
        return false;
    }    
	
}


function get_user() {
    if (is_string($_SESSION['user'])){
        $user = unserialize($_SESSION['user']);
    }
    
    if ($user instanceof User){
        return $user;
    }
          
    if(is_array($_SESSION['user']) && isset($_SESSION['user']['id'])){
        $user = Doctrine_Query::create()
            ->from('User u')
            ->addWhere('u.id = ?', $_SESSION['user']['id'])
            ->fetchOne();      
        $_SESSION['user'] = serialize($user);
    }elseif (isset($_COOKIE['rme'])){
        $pt = PjangoToken::check_token($_COOKIE['rme']);
            
        if ($pt){
            $user = User::authenticate(false, false, $pt->object_id);
        }
    }
    
    if ($user instanceof User){
        return $user;
    }else {
        return new AnonymousUser();
    }    
}

function login_required(){
    $user = get_user();
    
    if(!$user->is_authenticated()){
        HttpResponseRedirect(pjango_ini_get('LOGIN_URL'));
        exit();
    }
    
}

