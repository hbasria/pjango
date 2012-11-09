<?php
namespace Pjango\Contrib\Pages\Models;
use Pjango\Core\ModelAdmin,
\Doctrine_Query,
\User;

class PagesAdmin extends ModelAdmin {
	
    public function __construct(){

        $user = User::get_current();
//         if($user->has_perm('post.show_Pages')){
    	    $this->admin_menu = array('Pages', pjango_gettext('Pages'), '/admin/Pages/Post/',
    			array('Post', pjango_gettext('Pages'), '/admin/Pages/Post/',
    	            array('Post', pjango_gettext('Pages'), '/admin/Pages/Post/'),
    			    array('PostCategory', pjango_gettext('Categories'), '/admin/Pages/PostCategory/')));
//         }        

        $this->row_actions = array('edit', 'delete');    								
    }

}

$site = \Pjango\Contrib\Admin\AdminSite::getInstance();
$site->register('Pages', 'Pjango\Contrib\Pages\Models\PagesAdmin');