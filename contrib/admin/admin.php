<?php
require_once 'pjango/contrib/admin/sites.php';

class SettingsAdmin extends ModelAdmin {

	public function __construct(){
		$user = get_user();
		if($user->has_perm('admin.show_Settings')){
			$this->admin_menu = array('settings', pjango_gettext('Settings'), '/admin/settings/GENERAL/',
			array('general', pjango_gettext('General'), '/admin/settings/GENERAL/'),
			array('email', pjango_gettext('Email'), '/admin/settings/EMAIL/'),
			array('pjangolist', pjango_gettext('Pjango List'), '/admin/settings/pjangolist/')
			);			
		}

	}

}

class PjangoImageAdmin extends ModelAdmin {

	public function __construct(){
		 
		$this->list_display = array('get_thumb_elem', 'title', 'is_default', 'is_active', 'created_at');
		$this->row_actions = array('edit', array('name'=>'delete', 'url'=>'/admin/delete/PjangoImage/'));
	}

}

$site = AdminSite::getInstance();

$site->register('Settings', 'SettingsAdmin');
$site->register('PjangoImage', 'PjangoImageAdmin');

