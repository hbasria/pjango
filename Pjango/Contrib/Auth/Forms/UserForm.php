<?php 
namespace Pjango\Contrib\Auth\Forms;
use Pjango\Phorm,
    Pjango\Phorm\Fields\HiddenField,
    Pjango\Phorm\Fields\TextField,
    Pjango\Phorm\Fields\EmailField,
    Pjango\Phorm\Fields\PasswordField,
    Pjango\Phorm\Fields\BooleanField;

class UserForm extends Phorm {
    protected function define_fields(){
        $this->id = new HiddenField();
        $this->username = new TextField(pjango_gettext('User name'), 35, 255, array('validate_required'));
        $this->displayname  = new TextField(pjango_gettext('Display name'), 35, 255);
        $this->email = new EmailField(pjango_gettext('Email'), 35, 255, array('validate_required'));
        $this->password = new PasswordField(pjango_gettext('Password'), 35, 255, 'User::getEncryptedPassword');
        $this->is_active = new BooleanField(pjango_gettext('Is active'));
        $this->is_staff = new BooleanField(pjango_gettext('Is staff'));
        $this->is_superuser = new BooleanField(pjango_gettext('Is superuser'));
    }
}