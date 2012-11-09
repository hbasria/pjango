<?php 
namespace Pjango\Contrib\Auth\Forms;
use Pjango\Phorm,
    Pjango\Phorm\Fields\HiddenField,
    Pjango\Phorm\Fields\TextField;

class GroupForm extends Phorm {
    protected function define_fields(){
        $this->id = new HiddenField();
        $this->name = new TextField(pjango_gettext('Name'), 35, 255, array('validate_required'));
    }
}