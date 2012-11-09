<?php 
namespace Pjango\Core\Forms;
use Pjango\Phorm,
    Pjango\Phorm\Fields\HiddenField,
    Pjango\Phorm\Fields\TextField,
    Pjango\Phorm\Fields\LargeTextField,
    Pjango\Phorm\Fields\BooleanField,
    Pjango\Phorm\Fields\PasswordField;


class PjangoMediaForm extends Phorm {
    protected function define_fields(){
        $this->id = new HiddenField();
        $this->content_type_id = new HiddenField();
        $this->object_id = new HiddenField();

        $this->file_path = new TextField(pjango_gettext("File Path"), 35, 255);
        $this->file_name = new TextField(pjango_gettext("File Name"), 35, 255);        
        $this->description = new LargeTextField(pjango_gettext("Description or Embed Code"), 3, 35);
        $this->is_default = new BooleanField(pjango_gettext("Is Default"));
        $this->is_active = new BooleanField(pjango_gettext("Is Active"));
        
        $this->meta_link_title = new TextField(pjango_gettext('Link Title'), 25, 200);
        $this->meta_link_alt = new TextField(pjango_gettext('Link Alt'), 25, 200);        
    }
}