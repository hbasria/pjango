<?php
namespace Pjango\Phorm\Fields;


/**
 * URLField
 *
 * A text field that only accepts a reasonably-formatted URL. Supports HTTP(S)
 * and FTP. If a value is missing the HTTP(S)/FTP prefix, adds it to the final
 * value.
 * @author Jeff Ober
 * @package Fields
 **/
class URLField extends TextField
{
    /**
     * Prepares the value by inserting http:// to the beginning if missing.
     * @author Jeff Ober
     * @param string $value
     * @return string
     **/
    public function prepare_value($value)
    {
        if (!preg_match('@^(http|ftp)s?://@', $value))
        return sprintf('http://%s', $value);
        else
        return $value;
    }

    /**
     * Validates the the value is a valid URL (mostly).
     * @author Jeff Ober
     * @param string $value
     * @return null
     * @throws ValidationError
     **/
    public function validate($value)
    {
        parent::validate($value);
        if ( !preg_match('@^(http|ftp)s?://(\w+(:\w+)?\@)?(([-_\.a-zA-Z0-9]+)\.)+[-_\.a-zA-Z0-9]+(\w*)@', $value) )
        throw new ValidationError("Invalid URL.");
    }
}