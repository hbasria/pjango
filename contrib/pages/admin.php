<?php
require_once 'pjango/contrib/admin/sites.php';

class PagesAdmin extends ModelAdmin {
	
    public function __construct(){
    	
    	
    	$this->admin_menu = array('pages', pjango_gettext('Pages'), '/admin/pages/',
    								array('pages', pjango_gettext('Pages'), '/admin/pages/',
    									array('categories', pjango_gettext('Categories'), '/admin/pages/categories/')));
    								
        $this->row_actions = array('edit', 'delete');    								
    }

}


$site->register('Pages', 'PagesAdmin');