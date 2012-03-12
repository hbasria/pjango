<?php
require_once 'pjango/contrib/admin/sites.php';

class PagesAdmin extends ModelAdmin {
	
    public function __construct(){

        $user = get_user();
        if($user->has_perm('post.show_Pages')){
    	    $this->admin_menu = array('pages', pjango_gettext('Pages'), '/admin/pages/Post/',
    			array('pages', pjango_gettext('Pages'), '/admin/pages/Post/',
    	            array('Post', pjango_gettext('Pages'), '/admin/pages/Post/'),
    			    array('PostCategory', pjango_gettext('Categories'), '/admin/pages/PostCategory/')));
        }        

        $this->row_actions = array('edit', 'delete');    								
    }

}


$site->register('Pages', 'PagesAdmin');