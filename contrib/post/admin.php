<?php
require_once 'pjango/contrib/admin/sites.php';

class PostCategoryAdmin extends ModelAdmin {
	public function __construct(){
		$lng = pjango_ini_get('LANGUAGE_CODE');

		$this->list_display = array('get_name');
		$this->ordering = array('lft');
		$this->row_actions = array('edit', array('name'=>'delete', 'url'=>'/admin/delete/PostCategory/'));
	}

}

class PostAdmin extends ModelAdmin {
	
    public function __construct(){
    	$this->list_display = array('get_category_names', 'get_title', 'get_slug', 'status', 'created_at', 'updated_at');
    	$this->list_filter = array('status');
    	$this->search_fields = array('status','t.title');
    	
    	$user = get_user();
    	if($user->has_perm('post.show_Post')){
    		$this->admin_menu = array('post', pjango_gettext('Post'), '/admin/post/Post/',
    			array('post', pjango_gettext('Post'), '/admin/post/Post/',
    		        array('Post', pjango_gettext('Posts'), '/admin/post/Post/'),
    			    array('PostCategory', pjango_gettext('Categories'), '/admin/post/PostCategory/'))
    		);
    		
    	}
    								
        $this->row_actions = array('edit', 'delete');    								
    }

}

$site->register('Post', 'PostAdmin');
$site->register('PostCategory', 'PostCategoryAdmin');