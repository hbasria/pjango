<?php 
namespace Pjango\Contrib\Auth\Forms;
use Pjango\Phorm,
    Pjango\Phorm\Fields\TextField,
	Pjango\Phorm\Fields\DropDownField;

class PermissionForm extends Phorm {
	protected function define_fields(){
		$this->name = new TextField(pjango_gettext('Name'), 35, 255, array('validate_required'));
		$this->codename = new TextField(pjango_gettext('Code Name'), 35, 255, array('validate_required'));

		$contentTypeChoices = \ContentType::findAllAsChoice();
		$this->content_type_id = new DropDownField('Content Type', $contentTypeChoices);
		 
		 
		 
	}
}