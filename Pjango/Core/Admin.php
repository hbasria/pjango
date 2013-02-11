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


class PjangoMediaAdmin extends ModelAdmin {
	public function __construct(){
		$this->list_display = array('get_thumb_elem', 'file_name', 'file_type', 'is_default', 'created_at', 'updated_at');
		$this->actions = array('add');
		$this->row_actions = array('edit', 'delete');
		$this->ordering = '-created_at';
	}
}

$site = \Pjango\Contrib\Admin\AdminSite::getInstance();
$site->register('Core', 'PjangoMedia', 'Pjango\Core\Models\PjangoMediaAdmin');