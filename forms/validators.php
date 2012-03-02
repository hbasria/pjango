<?php





function EmailValidator($value){
	
}

function RequiredValidator($value){
	if ($value == '' || is_null($value))
    	throw new ValidationError(pjango_gettext('This field is required'));
}