<?php 
namespace Pjango\Contrib\Auth\Forms;
use Pjango\Phorm,
    Pjango\Phorm\Fields\TextField,
    Pjango\Phorm\Fields\PasswordField;

class LoginForm extends Phorm {
    protected function define_fields(){
        $this->username = new TextField(pjango_gettext('User name'), 25, 255);
        $this->password = new PasswordField(pjango_gettext('User password'), 15, 255, 'User::getEncryptedPassword');
    }
}