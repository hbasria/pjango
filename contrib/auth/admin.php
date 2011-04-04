<?php
require_once 'pjango/contrib/admin/admin.php';
require_once 'pjango/contrib/admin/sites.php';

class UserAdmin extends ModelAdmin {
	
    public function __construct(){
        $this->list_display = array('username', 'displayname', 'email', 'is_staff', 'is_active', 'last_login', 'date_joined');
        $this->list_display_links = array('username');
        $this->row_actions = array('edit', 'delete');
    }

}

$site->register('User', 'UserAdmin');