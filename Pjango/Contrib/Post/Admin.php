<?php
namespace Pjango\Contrib\Post\Models;
use Pjango\Core\ModelAdmin,
    \User;

class PostCategoryAdmin extends ModelAdmin {
	public function __construct(){
		$this->list_display = array('get_name');
		$this->ordering = array('lft');
	}

}

class PostAdmin extends ModelAdmin {
	
    public function __construct(){
    	$this->list_display = array('get_category_names', 'get_title', 'get_slug', 'status', 'created_at', 'updated_at');
    	$this->list_filter = array('status');
    	$this->search_fields = array('Translation__title','Translation__content','Translation__excerpt');
    	$this->ordering = '-created_at';
    	
    	$user = User::get_current();
     	if($user->has_perm('Post.change_Post')){
    		$this->admin_menu = array('Post', pjango_gettext('Post'), '/admin/Post/Post/',
    			array('Post', pjango_gettext('Post'), '/admin/Post/Post/',
    		        array('Post', pjango_gettext('Posts'), '/admin/Post/Post/'),
    			    array('PostCategory', pjango_gettext('Categories'), '/admin/Post/PostCategory/'),
    				array('Settings', pjango_gettext('Settings'), '/admin/Post/Post/settings/')
				),    				
    		);    		
     	}

		$this->third_level_navigation = array(
			array('key'=>'edit', 'url'=>'edit/', 'name'=>__('Post Properties')),
    		array('key'=>'files', 'url'=>'files/', 'name'=>__('Post Files'))
    	);    		
    								
        $this->row_actions = array('edit', 'delete');    								
    }

}