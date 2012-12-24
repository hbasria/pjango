<?php
namespace Pjango\Core\Models;

use Pjango\Core\ModelAdmin;

class SiteAdmin extends ModelAdmin {

	public function __construct(){
		$this->list_display = array('site_type' ,'domain', 'name', 'status');
		$this->row_actions = array('edit', 'delete');
		
		$this->admin_menu = array('Core', pjango_gettext('Site'), '/admin/Core/Site/',
				array('Site', __('Site'), '/admin/Core/Site/',
						array('Site', __('Site List'), '/admin/Core/Site/')
						 
				)
		);
	}
}

$site = \Pjango\Contrib\Admin\AdminSite::getInstance();
$site->register('Site', 'Pjango\Core\Models\SiteAdmin');