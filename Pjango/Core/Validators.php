<?php
use Pjango\Phorm\Fields\ValidationError;


function validate_required($value){
    if ($value == '' || is_null($value))
        throw new ValidationError(__('This field is required'));
}

function validate_email($value){    
    if(!filter_var($value, FILTER_VALIDATE_EMAIL))
        throw new ValidationError(__('Enter a valid e-mail address.'));
}