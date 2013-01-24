<?php 
namespace Pjango\Core\Forms;
use Pjango\Phorm,    
    Pjango\Phorm\Fields\TextField,
	Pjango\Phorm\Fields\DropDownField;


class SiteForm extends Phorm {
    protected function define_fields(){
    	$this->parent_id = new DropDownField(pjango_gettext("Parent Site"), \Site::findAllAsChoice());    	
    	$this->site_type = new DropDownField(__("Site Type"), \Site::getTypeChoices());
        $this->domain = new TextField(__("Site Domain"), 35, 255);
        $this->name = new TextField(__("Site Name"), 35, 255);        
        $this->status = new DropDownField(__("Site Status"), \Site::getStatusChoices());
    }
}