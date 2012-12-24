<?php 
namespace Pjango\Core\Forms;
use Pjango\Phorm,    
    Pjango\Phorm\Fields\TextField,
	Pjango\Phorm\Fields\DropDownField;


class SiteForm extends Phorm {
    protected function define_fields(){
    	$this->parent_id = new DropDownField(pjango_gettext("Parent Site"), \Site::findAllAsChoice());
    	
        $this->domain = new TextField(__("Site Domain"), 35, 255);
        $this->name = new TextField(__("Site Name"), 35, 255);        
    }
}