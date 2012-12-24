<?php 
use Pjango\Phorm,
    Pjango\Phorm\Fields\HiddenField,
    Pjango\Phorm\Fields\TextField,
    Pjango\Phorm\Fields\EmailField,
    Pjango\Phorm\Fields\BooleanField,
    Pjango\Phorm\Fields\DropDownField,
    Pjango\Phorm\Fields\PasswordField;


class PermissionForm extends Phorm {
	protected function define_fields(){
		$this->id = new HiddenField();
    	$this->name = new TextField(pjango_gettext('Name'), 35, 255, array('RequiredValidator'));
    	$this->codename = new TextField(pjango_gettext('Code Name'), 35, 255, array('RequiredValidator'));
    	
    	$contentTypeChoices = ContentType::findAllAsChoice();
    	$this->content_type_id = new DropDownField('Content Type', $contentTypeChoices); 
    	
    	
    	
  	}
}

class GroupForm extends Phorm {
	protected function define_fields(){
		$this->id = new HiddenField();
    	$this->name = new TextField(pjango_gettext('Name'), 35, 255);
  	}
}



class RegistrationForm extends Phorm {
	protected function define_fields(){
	    $this->displayname = new TextField(pjango_gettext('Full name'), 40, 255);
	    $this->username = new TextField(pjango_gettext('User name'), 15, 255, array('RequiredValidator'),array("class" => "requiredField"));
    	$this->email = new TextField(pjango_gettext('Email'), 40, 255, array('RequiredValidator'),array("class" => "requiredField email"));
    	$this->phone = new TextField(pjango_gettext('Phone'), 25, 255, array('RequiredValidator'));    	
    	$this->email = new EmailField(pjango_gettext('Email'), 40, 255, array('RequiredValidator'));
    	$this->email_confirm = new EmailField(pjango_gettext('Confirm email'), 35, 255, array('RequiredValidator'));
    	$this->password = new PasswordField(pjango_gettext('Password'), 10, 255, 'User::getEncryptedPassword', array('RequiredValidator'));
    	$this->password_confirm = new PasswordField(pjango_gettext('Confirm password'), 10, 255, 'User::getEncryptedPassword', array('RequiredValidator'));
    	
    	$genderChoices = array('male' => pjango_gettext('Male'),
    			'female' => pjango_gettext('Female'));
    	
    	$this->gender = new DropDownField(pjango_gettext('Gender'), $genderChoices);
  	}
}

class PasswordChangeForm extends Phorm {
	protected function define_fields(){
    	$this->password = new PasswordField('Şifre', 10, 255, 'User::getEncryptedPassword', array('RequiredValidator'));
    	$this->password_confirm = new PasswordField('Şifre (Tekrar)', 10, 255, 'User::getEncryptedPassword', array('RequiredValidator')); 
  	}
}