<?php
namespace Pjango\Phorm\Fields;
/**
* CharWidget
*
* A basic text input field.
* @author Jeff Ober
* @package Widgets
**/
class CharWidget extends PhormWidget
{
    /**
     * Returns the field as serialized HTML.
     * @author Jeff Ober
     * @param mixed $value the form widget's value attribute
     * @param array $attributes key=>value pairs corresponding to HTML attributes' name=>value
     * @return string the serialized HTML
     **/
    protected function serialize($value, array $attributes=array())
    {
        $attributes['type'] = 'text';
        return parent::serialize($value, $attributes);
    }
}