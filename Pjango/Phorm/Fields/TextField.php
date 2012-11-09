<?php
namespace Pjango\Phorm\Fields;
/**
 * TextField
 *
 * A simple text field.
 * @author Jeff Ober
 * @package Fields
 **/
class TextField extends PhormField
{
    /**
     * Stores the maximum value length in characters.
     **/
    private $max_length;

    /**
     * @author Jeff Ober
     * @param string $label the field's text label
     * @param int $size the field's size attribute
     * @param int $max_length the maximum size in characters
     * @param array $validators a list of callbacks to validate the field data
     * @param array $attributes a list of key/value pairs representing HTML attributes
     **/
    public function __construct($label, $size, $max_length, array $validators=array(), array $attributes=array())
    {
        $this->max_length = $max_length;
        $attributes['size'] = $size;
        parent::__construct($label, $validators, $attributes);
    }

    /**
     * Returns a new CharWidget.
     * @author Jeff Ober
     * @return CharWidget
     **/
    protected function get_widget()
    {
        return new CharWidget();
    }

    /**
     * Validates that the value is less than $this->max_length;
     * @author Jeff Ober
     * @return null
     * @throws ValidationError
     * @see TextField::$max_width
     **/
    protected function validate($value)
    {
        if (strlen($value) > $this->max_length)
        throw new ValidationError('Must be fewer than {$this->max_length} characters in length.');
    }

    /**
     * Imports the value by decoding HTML entities.
     * @author Jeff Ober
     * @param string $value
     * @return string the decoded value
     **/
    public function import_value($value)
    {
        return html_entity_decode((string)$value);
    }
}
