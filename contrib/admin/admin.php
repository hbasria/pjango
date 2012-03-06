<?php
require_once 'pjango/contrib/admin/sites.php';

class SettingsAdmin extends ModelAdmin {

	public function __construct(){
		$user = get_user();
		if($user->has_perm('admin.show_Settings')){
			
			$this->admin_menu = array('settings', pjango_gettext('Settings'), '/admin/settings/GENERAL/',
				array('GENERAL', pjango_gettext('General'), '/admin/settings/GENERAL/')
			);

// 			Genel settings menüsünü oluştur
			$settingsCategories = Doctrine_Query::create()
				->select('s.category, COUNT(s.category) AS count')
				->from('Settings s')
				->groupBy('s.category')
				->execute();			
			
			foreach ($settingsCategories as $categoryItem) {
				$this->admin_menu[3][] = array(
						strtoupper($categoryItem->category), 
						pjango_gettext('Settings.'.$categoryItem->category).' ('.$categoryItem->count.')', 						
						'/admin/settings/'.strtoupper($categoryItem->category).'/');				
			}
			
			$this->admin_menu[] = array('pjangolist', pjango_gettext('Pjango List'), '/admin/settings/pjangolist/');
			$this->admin_menu[] = array('pjangolocation', pjango_gettext('Pjango Location'), '/admin/settings/pjangolocation/');
			$this->admin_menu[] = array('pagelayout', pjango_gettext('Page Layouts'), '/admin/settings/pagelayout/',
					array('pagelayout', pjango_gettext('Page Layouts'), '/admin/settings/pagelayout/'),
					array('pagelayoutcategory', pjango_gettext('Categories'), '/admin/settings/pagelayout/category/')
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