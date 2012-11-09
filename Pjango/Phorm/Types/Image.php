<?php
namespace Pjango\Phorm\Types;

/**
* Image
*
* Adds a few additional properties specific for images to the File class.
* @author Jeff Ober
* @see ImageField
**/
class Image extends File
{
    /**
     * The image's width in pixels.
     **/
    public $width;
    /**
     * The image's height in pixels.
     **/
    public $height;

    public function __construct($file_data)
    {
        parent::__construct($file_data);
        list($this->width, $this->height) = getimagesize($this->tmp_name);
    }
}