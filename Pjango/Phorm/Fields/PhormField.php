<?php
namespace Pjango\Phorm\Fields;

require_once 'Pjango/Core/Validators.php';
/**
 * PhormField
 *
 * Abstract class from which all other field classes are derived.
 * @author Jeff Ober
 * @package Fields
 **/
abstract class PhormField
{
    /**
     * The field's text label.
     **/
    private $label;
    /**
     * Store's the field's value. Set during validation.
     **/
    private $value;
    /**
     * Array of callbacks used to validate field data. May be either a string
     * denoting a function or an array of array(instance, string method) to use
     * a class instance method.
     **/
    private $validators;
    /**
     * Associative array of key/value pairs representing HTML attributes of the field.
     **/
    private $attributes;
    /**
     * Array storing errors generated during field validation.
     **/
    private $errors;
    /**
     * Storage of the "cleaned" field value.
     **/
    private $imported;
    /**
     * Help text for the field. This is printed out with the field HTML.
     **/
    private $help_text = "";
    /**
     * If true, this field uses multiple field widgets.
     * @see widgets.php
     **/
    public $multi_field = false;
    /**
     * Stores the result of field validation to prevents double-validation.
     **/
    private $valid;

    /**
     * @author Jeff Ober
     * @param string $label the field's label
     * @param array $validators callbacks used to validate field data
     * @param array $attributes an assoc of key/value pairs representing HTML attributes
     * @return null
     **/
    public function __construct($label, array $validators=array(), array $attributes=array())
    {
        $this->label = (string)$label;
        $this->attributes = $attributes;
        $this->validators = $validators;
    }

    /**
     * Assigns help text to the field.
     * @author Jeff Ober
     * @param string $text the help text
     * @return null
     **/
    public function set_help_text($text)
    {
        $this->help_text = $text;
    }

    /**
     * Sets the value of the field.
     * @author Jeff Ober
     * @param mixed $value the field's value
     * @return null
     **/
    public function set_value($value)
    {
        $this->value = $value;
    }

    /**
     * Returns the "cleaned" value of the field.
     * @author Jeff Ober
     * @return mixed the field's "cleaned" value
     **/
    public function get_value()
    {
        return $this->imported;
    }

    /**
     * Sets an HTML attribute of the field.
     * @author Jeff Ober
     * @param string $key the attribute name
     * @param string $value the attribute's value
     * @return null
     **/
    public function set_attribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Returns the value of an HTML attribute or null if not set.
     * @author Jeff Ober
     * @param string $key the attribute name to look up
     * @return string|null the attribute's value or null if not set
     **/
    public function get_attribute($key)
    {
        if (array_key_exists($key, $this->attributes))
        return $this->attributes[$key];
        return null;
    }

    /**
     * Returns a list of errors generated during validation. If the field is not
     * yet validated, returns null.
     * @author Jeff Ober
     * @return array|null
     **/
    public function get_errors()
    {
        return $this->errors;
    }

    /**
     * Returns an HTML string containing the field's help text.
     * @author Jeff Ober
     * @return string the HTML help text paragraph
     **/
    public function help_text()
    {
        return sprintf('<span class="help-inline">%s</span>', htmlentities($this->help_text, ENT_QUOTES, 'UTF-8' ));
    }

    /**
     * Returns the HTML field label.
     * @author Jeff Ober
     * @return string the HTML label tag
     **/
    public function label()
    {
        return sprintf('<label class="control-label" for="%s">%s</label>', (string)$this->get_attribute('id'), $this->label);
    }

    /**
     * Returns the field's tag as HTML.
     * @author Jeff Ober
     * @return string the field as HTML
     **/
    public function html()
    {
        $widget = $this->get_widget();
        $attr = $this->attributes;
        
        if((method_exists($this,'export_value'))){
            $export_value = $this->export_value($this->value);
        }else {
            $export_value = $this->value;
        }
        
        return $widget->html($export_value, $this->attributes);
    }

    /**
     * Returns the field's errors as an unordered list with the class "phorm_error".
     * @author Jeff Ober
     * @return string the field errors as an unordered list
     **/
    public function errors()
    {
        $elts = array();
        
        if (is_array($this->errors) && count($this->errors) > 0)
        foreach ($this->errors as $error)
        $elts[] = sprintf('<li>%s</li>', $error);
        return sprintf('<ul class="phorm_error">%s</ul>', implode($elts));
    }

    /**
     * Serializes the field to HTML.
     * @author Jeff Ober
     * @return string the field's complete HTMl representation.
     **/
    public function __toString()
    {
        return $this->label(). $this->html() . $this->help_text() . $this->errors();
    }

    /**
     * On the first call, calls each validator on the field value, and returns
     * true if each returned successfully, false if any raised a
     * ValidationError. On subsequent calls, returns the same value as the
     * initial call. If $reprocess is set to true (default: false), will
     * call each of the validators again. Stores the "cleaned" value of the
     * field on success.
     * @author Jeff Ober
     * @param boolean $reprocess if true, ignores memoized result of initial call
     * @return boolean true if the field's value is valid
     * @see PhormField::$valid,PhormField::$imported,PhormField::$validators,PhormField::$errors
     **/
    public function is_valid($reprocess=false)
    {
        if ( $reprocess || is_null($this->valid) )
        {
            // Pre-process value
            $value = $this->prepare_value($this->value);

            $this->errors = array();
            $v = $this->validators;

            foreach($v as $f)
            {
                try { 
                    call_user_func($f, $value);
                }catch (ValidationError $e) {
                    $this->errors[] = $e->getMessage();
                }
            }

            if ( $value !== '' )
            {
                try { 
                    $this->validate($value);
                }catch (Exception $e) {
                    $this->errors[] = $e->getMessage();
                }
            }

            if ( $this->valid = ( count($this->errors) === 0 ) )
            $this->imported = $this->import_value($value);
        }
        return $this->valid;
    }

    /**
     * Pre-processes a value for validation, handling magic quotes if used.
     * @author Jeff Ober
     * @param string $value the value from the form array
     * @return string the pre-processed value
     **/
    protected function prepare_value($value)
    {
        return $value;
//         return ( get_magic_quotes_gpc() ) ? stripslashes($value) : $value;
    }

    /**
     * Defined in derived classes; must return an instance of PhormWidget.
     * @return PhormWidget the field's widget
     * @see PhormWidget
     **/
    abstract protected function get_widget();

    /**
     * Raises a ValidationError if $value is invalid.
     * @param string|mixed $value (may be mixed if prepare_value returns a non-string)
     * @throws ValidationError
     * @return null
     * @see ValidationError
     **/
    abstract protected function validate($value);

    /**
     * Returns the field's "imported" value, if any processing is required. For
     * example, this function may be used to convert a date/time field's string
     * into a unix timestamp or a numeric string into an integer or float.
     * @param string|mixed $value the pre-processed string value (or mixed if prepare_value returns a non-string)
     * @return mixed
     **/
    abstract public function import_value($value);
}