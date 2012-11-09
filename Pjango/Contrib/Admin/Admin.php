<?php
namespace Pjango\Contrib\Admin\Models;
use Pjango\Core\ModelAdmin,
    \Doctrine_Query,
    \User;


class PjangoMediaAdmin extends ModelAdmin {
    public function __construct(){
        $this->list_display = array('get_thumb_elem', 'file_name', 'file_type', 'is_default', 'is_active', 'created_at', 'updated_at');
        $this->row_actions = array('edit', 'delete');
        $this->ordering = '-created_at';
    }
     	
}


$site = \Pjango\Contrib\Admin\AdminSite::getInstance();
$site->register('PjangoMedia', 'Pjango\Contrib\Admin\Models\PjangoMediaAdmin');