<?php
namespace Pjango\Phorm\Fields;
use Pjango\Phorm\Types\File;
/**
 * FileField
 *
 * A field representing a file upload input.
 * @author Jeff Ober
 * @package Fields
 * @see File
 **/
class FileField extends PhormField
{
    /**
     * Stores the valid types for this field.
     **/
    private $types;
    /**
     * Stores the maximum size boundary in bytes.
     **/
    private $max_size;

    /**
     * @author Jeff Ober
     * @param string $label the field's string label
     * @param array $mime_types a list of valid mime types
     * @param int $max_size the maximum upload size in bytes
     * @param array $validators a list of callbacks to validate the field data
     * @param array $attributes a list of key/value pairs representing HTML attributes
     **/
    public function __construct($label, array $mime_types, $max_size, array $validators=array(), array $attributes=array())
    {
        $this->types = $mime_types;
        $this->max_size = $max_size;
        parent::__construct($label, $validators, $attributes);
    }

    /**
     * Returns true if the file was uploaded without an error.
     * @author Jeff Ober
     * @return boolean
     **/
    protected function file_was_uploaded()
    {
        $file = $this->get_file_data();
        return !$file['error'];
    }

    /**
     * Returns an error message for a file upload error code.
     * @author Jeff Ober
     * @param int $errno the error code (from $_FILES['name']['error'])
     * @return string the error message
     **/
    protected function file_upload_error($errno)
    {
        switch ($errno)
        {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return "The file sent was too large.";

            case UPLOAD_ERR_PARTIAL:
                return "There was an error uploading the file; please try again.";

            case UPLOAD_ERR_NO_FILE:
                return "The file was not sent; please try again.";

            case UPLOAD_ERR_NO_TMP_DIR:
            case UPLOAD_ERR_CANT_WRITE:
            case UPLOAD_ERR_EXTENSION:
                return "There was a system error during upload; please contact the webmaster (error number {$errno}).";

                case UPLOAD_ERR_OK:
                default:
                    return false;
        }
    }

    /**
     * Returns a FileWidget.
     * @author Jeff Ober
     * @return FileWidget
     * @see FileWidget,FileField::$types
     **/
    protected function get_widget()
    {
        return new FileWidget($this->types);
    }

    /**
     * Returns an array of file upload data.
     * @author Jeff Ober
     * @return array file upload data
     **/
    protected function get_file_data()
    {
        $data = $_FILES[ $this->get_attribute('name') ];
        $data['error'] = $this->file_upload_error($data['error']);
        return $data;
    }

    /**
     * Returns a new File instance for this field's data.
     * @author Jeff Ober
     * @return File a new File instance
     * @see File
     **/
    protected function get_file()
    {
        return new File( $this->get_file_data() );
    }

    /**
     * On a successful upload, returns a new File instance.
     * @author Jeff Ober
     * @param array $value the file data from $_FILES
     * @return File a new File instance
     * @see File
     **/
    public function import_value($value)
    {
        if ( $this->file_was_uploaded() )
        return $this->get_file();
    }

    /**
     * Returns the file's $_FILES data array or false if the file was not
     * uploaded.
     * @author Jeff Ober
     * @param mixed $value
     * @return boolean|File
     **/
    public function prepare_value($value)
    {
        if ( $this->file_was_uploaded() )
        return $this->get_file();
        else
        return false;
    }

    /**
     * Throws a ValidationError if the file upload resulted in an error, if
     * the file was not a valid type, or if the file exceded the maximum size.
     * @author Jeff Ober
     * @param mixed $value
     * @return null
     * @throws ValidationError
     **/
    protected function validate($value)
    {
        $file = $this->get_file_data();

        /*
        if ($file['error'])
        throw new ValidationError($file['error']);

        if (is_array($this->types) && !in_array($file['type'], $this->types))
        throw new ValidationError("Files of type ${file['type']} are not accepted.");

        if ($file['size'] > $this->max_size)
        throw new ValidationError(sprintf("Files are limited to %s bytes.", number_format($this->max_size)));
        */
}
}