<?php
namespace Pjango\Phorm\Fields;


/**
 * DecimalField
 *
 * A field that accepts only decimals of a specified precision.
 * @author Jeff Ober
 * @package Fields
 **/
class DecimalField extends PhormField
{
    /**
     * The maximum precision of the field's value.
     **/
    private $precision;

    /**
     * @author Jeff Ober
     * @param string $label the field's text label
     * @param int $precision the maximum number of decimals permitted
     * @param array $validators a list of callbacks to validate the field data
     * @param array $attributes a list of key/value pairs representing HTML attributes
     **/
    public function __construct($label, $precision, array $validators=array(), array $attributes=array())
    {
        $attributes['size'] = 20;
        parent::__construct($label, $validators, $attributes);
        $this->precision = $precision;
    }

    /**
     * Returns a new CharWidget.
     * @author Jeff Ober
     * @return CharWidget
     **/
    public function get_widget()
    {
        return new CharWidget();
    }

    /**
     * Validates that the value is parsable as a float.
     * @author Jeff Ober
     * @param string value
     * @return null
     * @throws ValidationError
     **/
    public function validate($value)
    {
        if (!is_numeric($value))
        throw new ValidationError("Invalid decimal value.");
    }

    /**
     * Returns the parsed float, rounded to $this->precision digits.
     * @author Jeff Ober
     * @param string $value
     * @return float the parsed value
     **/
    public function import_value($value)
    {
        return round((float)(html_entity_decode($value)), $this->precision);
    }
}