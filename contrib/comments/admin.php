<?php
require_once 'pjango/contrib/admin/admin.php';
require_once 'pjango/contrib/admin/sites.php';

class CommentAdmin extends ModelAdmin {
	
    public function __construct(){
        $this->list_display = array('submit_date', 'comment');
        $this->list_display_links = array('submit_date');
        $this->ordering = '-submit_date';
        $this->row_actions = array('edit', 'delete');    
    }

}

$site->register('Comment', 'CommentAdmin');