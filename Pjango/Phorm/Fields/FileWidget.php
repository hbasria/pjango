<?php
namespace Pjango\Phorm\Fields;
/**
 * FileWidget
 *
 * A file upload field. Requires that the form have enctype="multipart/form-data"
 * set to function.
 * @author Jeff Ober
 * @package Widgets
 **/
class FileWidget extends PhormWidget
{
    /**
     * Stores an array of valid mime types.
     **/
    private $types;

    /**
     * @author Jeff Ober
     * @param array $valid_mime_types e.g. array("image/jpeg", "image/jpg", "image/png", "image/gif")
     * @return null
     **/
    public function __construct(array $valid_mime_types)
    {
        $this->types = $valid_mime_types;
    }

    /**
     * Returns the field as serialized HTML.
     * @author Jeff Ober
     * @param mixed $value the form widget's value attribute
     * @param array $attributes key=>value pairs corresponding to HTML attributes' name=>value
     * @return string the serialized HTML
     **/
    protected function serialize($value, array $attributes=array())
    {
        $attributes['type'] = 'file';
        $attributes['accept'] = implode(',', $this->types);
        return parent::serialize($value, $attributes);
    }
}