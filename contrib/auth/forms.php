<?php 
require_once('pjango/forms.php');

class PermissionForm extends Form {
	protected function define_fields(){
		$this->id = new HiddenField();
    	$this->name = new TextField(pjango_gettext('Name'), 35, 255, array('RequiredValidator'));
    	$this->codename = new TextField(pjango_gettext('Code Name'), 35, 255, array('RequiredValidator'));
    	
    	$contentTypeChoices = ContentType::findAllAsChoice();
    	$this->content_type_id = new DropDownField('Content Type', $contentTypeChoices); 
    	
    	
    	
  	}
}

class GroupForm extends Form {
	protected function define_fields(){
		$this->id = new HiddenField();
    	$this->name = new TextField(pjango_gettext('Name'), 35, 255, array('RequiredValidator'));
  	}
}

class UserForm extends Form {
	protected function define_fields(){
		$this->id = new HiddenField();
    	$this->username = new TextField(pjango_gettext('User name'), 35, 255, array('RequiredValidator'));
    	$this->displayname  = new TextField(pjango_gettext('Display name'), 35, 255);
    	$this->email = new EmailField(pjango_gettext('Email'), 35, 255, array('RequiredValidator'));
    	$this->password = new PasswordField(pjango_gettext('Password'), 35, 255, 'User::getEncryptedPassword');
    	$this->is_active = new BooleanField(pjango_gettext('Is active'));
    	$this->is_staff = new BooleanField(pjango_gettext('Is staff'));
    	$this->is_superuser = new BooleanField(pjango_gettext('Is superuser'));
  	}
}

class LoginForm extends Form {
	protected function define_fields(){
    	$this->username = new TextField(pjango_gettext('User name'), 25, 255, array('RequiredValidator'));
    	$this->password = new PasswordField(pjango_gettext('User password'), 15, 255, 'User::getEncryptedPassword', array('RequiredValidator'));
  	}
}

class RegistrationForm extends Form {
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

class PasswordChangeForm extends Form {
	protected function define_fields(){
    	$this->password = new PasswordField('Şifre', 10, 255, 'User::getEncryptedPassword', array('RequiredValidator'));
    	$this->password_confirm = new PasswordField('Şifre (Tekrar)', 10, 255, 'User::getEncryptedPassword', array('RequiredValidator')); 
  	}
}