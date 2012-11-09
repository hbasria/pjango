<?php
namespace Pjango\Core\Validators;

class RequiredValidator {
    function __construct($value){
        echo $value."HHHHHHHHHHHHH\n";
        if ($value == '' || is_null($value)) throw new ValidationError(pjango_gettext('This field is required'));
    }
}

