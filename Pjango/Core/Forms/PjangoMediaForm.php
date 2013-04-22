<?php 
namespace Pjango\Core\Forms;
use Pjango\Phorm,
    Pjango\Phorm\Fields\HiddenField,
    Pjango\Phorm\Fields\TextField,
    Pjango\Phorm\Fields\LargeTextField,
    Pjango\Phorm\Fields\BooleanField,
    Pjango\Phorm\Fields\PasswordField;
use Pjango\Phorm\Fields\DropDownField;


class PjangoMediaForm extends Phorm {
    protected function define_fields(){
        $this->content_type_id = new HiddenField();
        $this->object_id = new HiddenField();
        
        $fileTypeChoices = array(
        		'auto'	=> __('Auto'),
        		'image/jpeg'		=> __('Jpeg Image'),
        		'video/embed'		=> __('Embed Video'),
        );
        

        $this->file_path = new TextField(pjango_gettext("File Path"), 35, 255, array(), array('class'=>'pjangoMedia'));
        $this->file_type = new DropDownField(__('File Type'), $fileTypeChoices);
        $this->file_name = new TextField(pjango_gettext("File Name"), 35, 255);        
        $this->description = new LargeTextField(pjango_gettext("Description or Embed Code"), 3, 35);
        $this->is_default = new BooleanField(pjango_gettext("Is Default"));
        //$this->is_active = new BooleanField(pjango_gettext("Is Active"));
        
        $this->meta_link_title = new TextField(pjango_gettext('Link Title'), 25, 200);
        $this->meta_link_alt = new TextField(pjango_gettext('Link Alt'), 25, 200);        
    }
}