<?php 
namespace Pjango\Contrib\Auth\Forms;

use Pjango\Phorm,
    Pjango\Phorm\Fields\TextField,
    Pjango\Phorm\Fields\EmailField,
    Pjango\Phorm\Fields\PasswordField,
    Pjango\Phorm\Fields\BooleanField,
    Pjango\Phorm\Fields\MultipleChoiceField;

class UserForm extends Phorm {
    protected function define_fields(){

    	$multiCheckboxWidgetOptions = array(
    			'widget_class'=>'Pjango\Phorm\Fields\MultiSelectWidget',
    			'validate_choices'=>false
    	);    	
    	
        $this->username = new TextField(__('User name'), 35, 255, array('validate_required'));
        $this->displayname  = new TextField(__('User Display name'), 35, 255);
        $this->email = new EmailField(__('User Email'), 35, 255, array('validate_required'));
        $this->password = new PasswordField(__('User Password'), 35, 255, 'User::getEncryptedPassword');
        $this->is_active = new BooleanField(__('Is active'));
        $this->is_staff = new BooleanField(__('Is staff'));
        $this->is_superuser = new BooleanField(__('Is superuser'));
        $this->groups = new MultipleChoiceField(__('User Groups'), \Group::findAllAsChoice(),
        		array(),array(), $multiCheckboxWidgetOptions);
        
        $this->permissions = new MultipleChoiceField(__('User Permissions'), \Permission::findAllAsChoice(),
        		array(),array(), $multiCheckboxWidgetOptions);        
        
        
        
    }
}