<?php
require_once 'pjango/contrib/admin/admin.php';
require_once 'pjango/contrib/admin/sites.php';

class UserAdmin extends ModelAdmin {

	public function __construct(){
		$this->list_display = array('username', 'displayname', 'email', 'is_staff', 'is_active', 'last_login', 'date_joined');
		$this->list_display_links = array('username');
		$this->row_actions = array('edit',
		array('name'=>'delete', 'url'=>'/admin/delete/User/'));
		$this->search_fields = array('username', 'displayname', 'email');
		
		$this->list_filter = array('is_staff', 'is_active');

		$user = get_user();
		if($user->has_perm('post.show_Post')){
			$this->admin_menu = array('auth', pjango_gettext('Users'), '/admin/user/',
			array('user', pjango_gettext('Users'), '/admin/user/'),
			array('group', pjango_gettext('Groups'), '/admin/group/'),
			array('permission', pjango_gettext('Permissions'), '/admin/permission/')
			);
		}



	}

}

class GroupAdmin extends ModelAdmin {
	public function __construct(){
		$this->list_display = array('name');
		$this->row_actions = array(
        	'edit', 
		array('name'=>'delete', 'url'=>'/admin/delete/Group/'));
	}
}

class PermissionAdmin extends ModelAdmin {
	public function __construct(){
		$this->list_display = array('name', 'codename', 'ContentType__name');
		$this->list_display_links = array('name');
		$this->row_actions = array(
		array('name'=>'edit', 'url'=>'/admin/addchange/Permission/'),
		array('name'=>'delete', 'url'=>'/admin/delete/Permission/'));
	}
}


$site = AdminSite::getInstance();
$site->register('User', 'UserAdmin');
$site->register('Group', 'GroupAdmin');
$site->register('Permission', 'PermissionAdmin');