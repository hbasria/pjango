<?php 
require_once('pjango/forms.php');

class PhoneForm extends Form {
	protected function define_fields(){
		
		$phoneTypes = PhoneType::findAllAsChoice();
		
		$this->id = new HiddenField();
		$this->phone_type_id = new MultipleChoiceField('Phone types', $phoneTypes);       
		$this->country_code = new TextField(pjango_gettext("Country code"), 5, 3);
        $this->phone_number = new TextField(pjango_gettext("Phone number"), 20, 15);
        $this->is_default = new BooleanField(pjango_gettext("Is default"));
  	}
}

class EmailForm extends Form {
	protected function define_fields(){
		
		$emailTypes = EmailType::findAllAsChoice();
		
		$this->id = new HiddenField();
		$this->email_type_id = new MultipleChoiceField('Email types', $emailTypes);       
		$this->email = new TextField(pjango_gettext("Email"), 25, 100);
        $this->is_default = new BooleanField(pjango_gettext("Is default"));
  	}
}


class PjangoListForm extends Form {
	protected function define_fields(){
		$this->id = new HiddenField();
		
		$this->parent_id = new DropDownField(pjango_gettext("PostCategory.parent"),
				PjangoList::findAllAsChoice());

		$this->name = new TextField(pjango_gettext("PjangoList.Name"), 35, 255);
		$this->slug = new TextField(pjango_gettext("PjangoList.Slug"), 35, 255);
	}
}

class PjangoImageForm extends Form {
	protected function define_fields(){
		$this->id = new HiddenField();
		$this->content_type_id = new HiddenField();
		$this->object_id = new HiddenField();
		
		$this->image = new TextField(pjango_gettext("PjangoImage image"), 35, 255);
// 		$this->content_type_id = new DropDownField(pjango_gettext("PjangoImage content_type"),
// 				ContentType::findAllAsChoice());

		$this->title = new TextField(pjango_gettext("PjangoImage title"), 35, 255);
		$this->description = new LargeTextField(pjango_gettext("PjangoImage description"), 3, 35);
		
// 		$this->object_id = new TextField(pjango_gettext("PjangoImage object_id"), 5, 255);
		$this->is_default = new BooleanField(pjango_gettext("PjangoImage is_default"));
		$this->is_active = new BooleanField(pjango_gettext("PjangoImage is_active"));
		
	}
}