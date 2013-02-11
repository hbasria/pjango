<?php
namespace Pjango\Phorm\Fields;
/**
* PhormWidget
*
* The base class of all HTML form widgets.
* @author Jeff Ober
* @package Widgets
**/
class PhormWidget
{
    /**
     * Serializes an array of key=>value pairs as an HTML attributes string.
     * @author Jeff Ober
     * @param array $attributes key=>value pairs corresponding to HTML attributes' name=>value
     * @return string the serialized HTML
     **/
    protected function serialize_attributes(array $attributes=array())
    {
        $attr = array();
        foreach($attributes as $key => $val)
        $attr[] = sprintf('%s="%s"', $key, $val);
        return implode(' ', $attr);
    }

    /**
     * Serializes the widget as an HTML form input.
     * @author Jeff Ober
     * @param string $value the form widget's value
     * @param array $attributes key=>value pairs corresponding to HTML attributes' name=>value
     * @return string the serialized HTML
     **/
    protected function serialize($value, array $attributes=array())
    {
        return sprintf('<input value="%s" %s />', $value, $this->serialize_attributes($attributes));
    }

    /**
     * Casts a value to a string and encodes it for HTML output.
     * @author Jeff Ober
     * @param mixed $str
     * @return a decoded string
     **/
    protected function clean_string($str)
    {
        return htmlentities((string)$str, ENT_QUOTES, 'UTF-8' );
    }

    /**
     * Returns the field as serialized HTML.
     * @author Jeff Ober
     * @param mixed $value the form widget's value attribute
     * @param array $attributes key=>value pairs corresponding to HTML attributes' name=>value
     * @return string the serialized HTML
     **/
    public function html($value, array $attributes=array())
    {

//         FIXME: geçici çözüm
        if (is_object($value) && get_class($value) == 'DateTime'){
            $value = $value->format('d/m/Y');
        }
        
        $value = htmlentities( (string)$value, ENT_QUOTES, 'UTF-8' );
        
        foreach ($attributes as $key => $val)
        $attributes[htmlentities( (string)$key , ENT_QUOTES, 'UTF-8')] = htmlentities( (string)$val, ENT_QUOTES, 'UTF-8');
        return $this->serialize($value, $attributes);
    }
}