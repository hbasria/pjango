<?php
require_once 'pjango/contrib/admin/sites.php';

class FlatPageAdmin extends ModelAdmin {
	
    public function __construct(){
    	
        $this->list_display = array('t.title', 't.slug', 'o.status', 'o.template_name', 'o.created_at');
        $this->list_display_links = array('t.title');
        $this->list_filter = array('o.created_at');
        $this->date_hierarchy = '-o.created_at'; 
        $this->search_fields = array('t.title', 't.content');
        $this->row_actions = array('edit', 'delete');
    }

}


$site->register('FlatPage', 'FlatPageAdmin');