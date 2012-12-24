<?php
namespace Pjango\Phorm\Fields;


/**
 * EmailField
 *
 * A text field that only accepts a valid email address.
 * @author Jeff Ober
 * @package Fields
 **/
class EmailField extends TextField
{
    /**
     * Validates that the value is a valid email address.
     * @author Jeff Ober
     * @param string $value
     * @return null
     * @throws ValidationError
     **/
    public function validate($value)
    {
        parent::validate($value);
        if ( !preg_match('@^([-_\.a-zA-Z0-9]+)\@(([-_\.a-zA-Z0-9]+)\.)+[-_\.a-zA-Z0-9]+$@', $value) )
        throw new ValidationError(__("Invalid email address."));
    }
}