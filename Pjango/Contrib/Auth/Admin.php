<?php
namespace Pjango\Contrib\Auth\Models;
use Pjango\Core\ModelAdmin,
    User;

require_once 'Pjango/Contrib/Auth/Util.php';

class UserAdmin extends ModelAdmin {

	public function __construct(){
		$this->list_display = array('username', 'displayname', 'email', 'is_staff', 'is_active', 'last_login', 'date_joined');
		$this->list_display_links = array('username');
		$this->row_actions = array('edit', 'delete');
		$this->search_fields = array('username', 'displayname', 'email');

		$user = User::get_current();
		if($user->is_active && $user->is_superuser){
			$this->admin_menu = array('Auth', pjango_gettext('Auth'), '/admin/Auth/User/',
			    //array('Auth', pjango_gettext('Auth'), '/admin/Auth/User/',
			        array('User', pjango_gettext('Users'), '/admin/Auth/User/'),
			        array('Group', pjango_gettext('Groups'), '/admin/Auth/Group/'),
			        array('Permission', pjango_gettext('Permissions'), '/admin/Auth/Permission/')
			    //),
			);
		}
	}
}

class GroupAdmin extends ModelAdmin {
	public function __construct(){
		$this->list_display = array('name');
		$this->row_actions = array('edit', 'delete');
	}
}

class PermissionAdmin extends ModelAdmin {
	public function __construct(){
		$this->list_display = array('name', 'codename', 'ContentType__name');
		$this->list_display_links = array('name');
		$this->row_actions = array('edit','delete');
	}
}