<?php
namespace Pjango\Phorm\Fields;


/**
 * ScanField
 *
 * Akin to the RegexField, but instead using sscanf() for more rigid matching
 * and type-cast values.
 * @author Jeff Ober
 * @package Fields
 * @see RegexField
 **/
class ScanField extends TextField
{
    /**
     * The sscanf() format.
     **/
    private $format;
    /**
     * The error message on match failure.
     **/
    private $message;
    /**
     * Storage for the matched values to prevent calling sscanf twice.
     **/
    private $matched;

    /**
     * @author Jeff Ober
     * @param string $label the field's text label
     * @param string $format the sscanf format used to validate and parse the field
     * @param string $error_msg the message thrown on a mismatch
     * @param array $validators a list of callbacks to validate the field data
     * @param array $attributes a list of key/value pairs representing HTML attributes
     **/
    public function __construct($label, $format, $error_msg, array $validators=array(), array $attributes=array())
    {
        parent::__construct($label, 25, 100, $validators, $attributes);
        $this->format = $format;
        $this->message = $error_msg;
    }

    /**
     * Validates that the value matches the sscanf format.
     * @author Jeff Ober
     * @param string $value
     * @return null
     * @throws ValidationError
     **/
    public function validate($value)
    {
        parent::validate($value);
        $this->matched = sscanf($value, $this->format);
        if ( count($this->matched) === 0 )
        throw new ValidationError($this->message);
    }

    /**
     * Returns the parsed matches that were captured in validate().
     * @param string $value
     * @return array the captured values
     **/
    public function import_value($value)
    {
        return $this->matched;
    }
}