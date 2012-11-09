<?php
namespace Pjango\Phorm\Fields;
/**
* HiddenField
*
* A hidden text field that does not print a label.
* @author Jeff Ober
* @package Fields
**/
class HiddenField extends TextField
{
    /**
     * @author Jeff Ober
     * @param array $validators a list of callbacks to validate the field data
     * @param array $attributes a list of key/value pairs representing HTML attributes
     **/
    public function __construct(array $validators=array(), array $attributes=array())
    {
        parent::__construct('', 255, $validators, $attributes);
    }

    /**
     * Does not print out a label.
     * @author Jeff Ober
     * @return string an empty string
     **/
    public function label()
    {
        return '';
    }

    /**
     * Does not print out the help text.
     * @author Jeff Ober
     * @return string an empty string.
     **/
    public function help_text()
    {
        return '';
    }

    /**
     * Returns a new HiddenWidget.
     * @author Jeff Ober
     * @return HiddenWidget
     **/
    protected function get_widget()
    {
        return new HiddenWidget();
    }
}