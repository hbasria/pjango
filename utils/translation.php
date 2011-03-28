<?php 

function encode_lang_key($str) {    
    return base64_encode(pack('H*',sha1($str)));
}

function pjango_gettext($message) {
	$lang_key = encode_lang_key($message);
	$retVal = $message;
	
	if (isset($GLOBALS['LANG'][$lang_key])){
    	$retVal = $GLOBALS['LANG'][$lang_key]['msgstr'];
    }
    
    return $retVal;
}


function pjango_ngettext($param) {
	$lang_key = encode_lang_key($message);
	$retVal = $message;
	
	if (isset($GLOBALS['LANG'][$lang_key])){
    	$retVal = $GLOBALS['LANG'][$lang_key]['msgstr'];
    }
    
    return $retVal;	
}

