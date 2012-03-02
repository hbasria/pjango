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
    	$this->password = new PasswordField(pjango_gettext('Password'), 35, 255, 'md5');
    	$this->is_active = new BooleanField(pjango_gettext('Is active'));
    	$this->is_staff = new BooleanField(pjango_gettext('Is staff'));
    	$this->is_superuser = new BooleanField(pjango_gettext('Is superuser'));
  	}
}

class LoginForm extends Form {
	protected function define_fields(){
    	$this->username = new TextField(pjango_gettext('User name'), 25, 255, array('RequiredValidator'));
    	$this->password = new PasswordField(pjango_gettext('Password'), 15, 255, 'md5', array('RequiredValidator'));
  	}
}

class RegistrationForm extends Form {
	protected function define_fields(){
    	//$this->username = new TextField(pjango_gettext('User name'), 15, 255, array('RequiredValidator'));
    	$this->first_name = new TextField(pjango_gettext('First name'), 40, 255, array('RequiredValidator'));
    	//$this->last_name = new TextField(pjango_gettext('Last name'), 20, 255, array('RequiredValidator'));
    	$this->phone = new TextField(pjango_gettext('Phone'), 25, 255, array('RequiredValidator'));    	
    	$this->email = new EmailField(pjango_gettext('Email'), 40, 255, array('RequiredValidator'));
    	//$this->email_confirm = new EmailField(pjango_gettext('Confirm email'), 35, 255, array('RequiredValidator'));
    	$this->password = new PasswordField(pjango_gettext('Password'), 10, 255, 'md5', array('RequiredValidator'));
    	$this->password_confirm = new PasswordField(pjango_gettext('Confirm password'), 10, 255, 'md5', array('RequiredValidator'));
    	
    	$genderChoices = array('male' => pjango_gettext('Male'),
    			'female' => pjango_gettext('Female'));
    	
    	$this->gender = new DropDownField('Content Type', $genderChoices); 
    	$this->campaign_city = new DropDownField(pjango_gettext("Cities"), 
				City::findAllAsChoice());
  	}
}


class ProfileForm extends Form {
	protected function define_fields(){
		$this->id = new HiddenField();

		$this->first_name = new TextField(pjango_gettext("First Name"), 15, 255);
		$this->last_name = new TextField(pjango_gettext("Last Name"), 15, 255);
		
		$this->campaign_city = new DropDownField(pjango_gettext("Contact types"), 
				City::findAllAsChoice());
				
		$genderChoice = array('male' => pjango_gettext('Male'),
		'female' => pjango_gettext('Female'));
				
		$this->gender = new DropDownField(pjango_gettext("Contact types"), 
						$genderChoice);		

		$this->password = new PasswordField(pjango_gettext('Password'), 10, 255, 'md5', array(), array('autocomplete'=>'off'));
    	$this->password_confirm = new PasswordField(pjango_gettext('Confirm password'), 10, 255, 'md5');						
		
		//Fatura bilgileri
    	$this->bill_name = new TextField(pjango_gettext("Bill name"), 25, 255);
    	$this->bill_identity_no = new TextField(pjango_gettext("Identity number"), 15, 255);
    	$this->bill_phone = new TextField(pjango_gettext("Phone number"), 15, 255);
    	$this->bill_zipcode = new TextField(pjango_gettext("Zip code"), 15, 255);
    	$this->bill_address = new TextField(pjango_gettext("Address"), 35, 255);

	}
}

class ProfileCompanyForm extends Form {
	protected function define_fields(){
		$this->company_name = new TextField(pjango_gettext("Company name"), 65, 255);
		$this->company_web = new TextField(pjango_gettext("Web"), 25, 255);
		$this->company_email = new TextField(pjango_gettext("Email"), 25, 255);
		$this->company_phone = new TextField(pjango_gettext("Phone"), 15, 255);
		$this->company_fax = new TextField(pjango_gettext("Fax Number"), 15, 255);
		$this->company_address = new LargeTextField(pjango_gettext("Address"), 5, 65);
		//$this->company_logo = new FileField(pjango_gettext("Company logo"), array('image/jpeg'), (1024*1024), array(), array());
	}
}

class OrderRegistrationForm1 extends Form {
	protected function define_fields(){
    	$genderChoices = array('male' => pjango_gettext('Male'),
    			'female' => pjango_gettext('Female'));
    			
		$this->gender = new DropDownField('Cinsiyet', $genderChoices); 
    	$this->campaign_city = new DropDownField('Şehir', 
				City::findAllAsChoice());
		$this->email = new EmailField('E-Posta', 20, 255, array('RequiredValidator'));		
    	$this->first_name = new TextField('Adınız Soyadınız', 20, 255, array('RequiredValidator'));
    	$this->ssn = new TextField('TC Kimlik No', 20, 255, array('RequiredValidator'));
    	$this->password = new PasswordField('Şifre', 10, 255, 'md5', array('RequiredValidator'));
    	$this->password_confirm = new PasswordField('Şifre (Tekrar)', 10, 255, 'md5', array('RequiredValidator'));    	
    	$this->phone = new TextField('Telefon', 20, 255, array('RequiredValidator'));   
    	$this->address = new TextField('Adres', 20, 255, array('RequiredValidator'));    	
  	}
}

class OrderRegistrationForm2 extends Form {
	protected function define_fields(){
    	$this->campaign_city = new DropDownField('Şehir', 
				City::findAllAsChoice());
    	$this->first_name = new TextField('Adınız Soyadınız', 20, 255, array('RequiredValidator'));
    	$this->ssn = new TextField('TC Kimlik No', 20, 255, array('RequiredValidator'));
    	$this->phone = new TextField('Telefon', 20, 255, array('RequiredValidator'));   
    	$this->address = new TextField('Adres', 20, 255, array('RequiredValidator'));    	
  	}
}


class PasswordChangeForm extends Form {
	protected function define_fields(){
    	$this->password = new PasswordField('Şifre', 10, 255, 'md5', array('RequiredValidator'));
    	$this->password_confirm = new PasswordField('Şifre (Tekrar)', 10, 255, 'md5', array('RequiredValidator')); 
  	}
}