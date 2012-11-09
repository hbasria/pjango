<?php
namespace Pjango\Phorm\Fields;
/**
 * ImageField
 *
 * A FileField that is pre-configured for images. Valid types are PNG, GIF, and
 * JPG. Returns an Image instance instead of a File instance. Identical to the
 * FileField in all other ways.
 * @author Jeff Ober
 * @package Fields
 * @see FileField,Image
 **/
class ImageField extends FileField
{
    /**
     * @author Jeff Ober
     * @param string $label the field's string label
     * @param int $max_size the maximum upload size in bytes
     * @param array $validators a list of callbacks to validate the field data
     * @param array $attributes a list of key/value pairs representing HTML attributes
     **/
    public function __construct($label, $max_size, array $validators=array(), array $attributes=array())
    {
        parent::__construct($label, array('image/png', 'image/gif', 'image/jpg', 'image/jpeg'), $max_size, $validators, $attributes);
    }

    /**
     * Returns a new Image.
     * @author Jeff Ober
     * @return Image
     **/
    protected function get_file()
    {
        return new \Pjango\Phorm\Types\Image( $this->get_file_data() );
    }
}