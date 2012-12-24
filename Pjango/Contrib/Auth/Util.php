<?php 

function login_required(){
    $user = User::get_current();
    
    if(!$user->is_authenticated()){
        HttpResponseRedirect(pjango_ini_get('LOGIN_URL'));
        exit();
    }
    
}

