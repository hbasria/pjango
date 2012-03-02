<?php
require_once 'pjango/contrib/admin/sites.php';

class PostCategoryAdmin extends ModelAdmin {
	public function __construct(){
		$lng = pjango_ini_get('LANGUAGE_CODE');

		$this->list_display = array('get_name');
		$this->ordering = array('lft');
		$this->row_actions = array('edit', 'delete');
	}

}

class PostAdmin extends ModelAdmin {
	
    public function __construct(){
    	$this->list_display = array('get_title', 'get_slug', 'status', 'created_at', 'updated_at');
    	$this->list_filter = array('status');
    	$this->search_fields = array('status','t.title');
    	
    	$user = get_user();
    	if($user->has_perm('post.show_Post')){
    		$this->admin_menu = array('post', pjango_gettext('Post'), '/admin/post/',
    			array('post', pjango_gettext('Post'), '/admin/post/',
    			array('categories', pjango_gettext('Categories'), '/admin/post/categories/'))
    		);
    		
    	}
    								
        $this->row_actions = array('edit', 'delete');    								
    }

}

$site->register('Post', 'PostAdmin');
$site->register('PostCategory', 'PostCategoryAdmin');