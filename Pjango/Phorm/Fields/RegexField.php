<?php
namespace Pjango\Phorm\Fields;


/**
 * RegexField
 *
 * A text field that validates using a regular expression and imports to an
 * array of captured values.
 * @author Jeff Ober
 * @package Fields
 **/
class RegexField extends TextField
{
    /**
     * The (pcre) regular expression.
     **/
    private $regex;
    /**
     * The error message thrown when unmatched.
     **/
    private $message;
    /**
     * Storage for matches during validation so that the expression needn't run twice.
     **/
    private $matches;

    /**
     * @author Jeff Ober
     * @param string $label the field's text label
     * @param string $regex the (pcre) regex used to validate and parse the field
     * @param string $error_msg the message thrown on a regex mismatch
     * @param array $validators a list of callbacks to validate the field data
     * @param array $attributes a list of key/value pairs representing HTML attributes
     **/
    public function __construct($label, $regex, $error_msg, array $validators=array(), array $attributes=array())
    {
        parent::__construct($label, 25, 100, $validators, $attributes);
        $this->regex = $regex;
        $this->message = $error_msg;
    }

    /**
     * Validates that the value matches the regular expression.
     * @author Jeff Ober
     * @param string $value
     * @return null
     * @throws ValidationError
     **/
    public function validate($value)
    {
        parent::validate($value);
        if (!preg_match($this->regex, $value, $this->matches))
        throw new ValidationError($this->message);
    }

    /**
     * Returns the captured values that were parsed inside validate().
     * @author Jeff Ober
     * @param string $value
     * @return array the captured matches
     **/
    public function import_value($value)
    {
        return $this->matches;
    }
}