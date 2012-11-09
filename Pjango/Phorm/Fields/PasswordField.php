<?php
namespace Pjango\Phorm\Fields;
/**
 * PasswordField
 *
 * A password field that uses a user-specified hash function to import values.
 * @author Jeff Ober
 * @package Fields
 **/
class PasswordField extends TextField
{
    /**
     * The hash function to encode the user-submitted value.
     **/
    private $hash_function;

    /**
     * @author Jeff Ober
     * @param string $label the field's text label
     * @param int $size the field's size attribute
     * @param int $max_length the maximum size in characters
     * @param callback $hash_function a (string) function or array (instance, string method) callback
     * @param array $validators a list of callbacks to validate the field data
     * @param array $attributes a list of key/value pairs representing HTML attributes
     **/
    public function __construct($label, $size, $max_length, $hash_function, array $validators=array(), array $attributes=array())
    {
        $this->max_length = $max_length;
        $this->hash_function = $hash_function;
        $attributes['size'] = $size;
        parent::__construct($label, $validators, $attributes);
    }

    /**
     * Returns a PasswordWidget.
     * @author Jeff Ober
     * @return PasswordWidget
     **/
    public function get_widget()
    {
        return new PasswordWidget();
    }

    /**
     * Returns a hash-encoded value.
     * @author Jeff Ober
     * @param string $value
     * @return string the encoded value
     **/
    public function import_value($value)
    {
        return call_user_func($this->hash_function, array($value));
    }
}