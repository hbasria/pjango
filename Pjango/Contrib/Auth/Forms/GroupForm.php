<?php 
namespace Pjango\Contrib\Auth\Forms;
use Pjango\Phorm,
    Pjango\Phorm\Fields\MultipleChoiceField,
    Pjango\Phorm\Fields\TextField;

class GroupForm extends Phorm {
    protected function define_fields(){
    	
    	$multiCheckboxWidgetOptions = array(
    	    			'widget_class'=>'Pjango\Phorm\Fields\MultiSelectWidget',
    	    			'validate_choices'=>false
    	);
    	    	
        $this->name = new TextField(__('Name'), 35, 255, array('validate_required'));
        $this->permissions = new MultipleChoiceField(__('User Permissions'), \Permission::findAllAsChoice(),
        		array(),array(), $multiCheckboxWidgetOptions);        
    }
}