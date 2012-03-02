<?php 
require_once 'POParser.php';



class Translation {
	
	public static function encode_lang_key($str) {    
	    return base64_encode(pack('H*',sha1($str)));
	}	
	
	public static function gettext($message) {
		$lang_key = Translation::encode_lang_key($message);
		$retVal = $message;
		
		if (isset($GLOBALS['SETTINGS']['LANG'][$lang_key])){
	    	$retVal = $GLOBALS['SETTINGS']['LANG'][$lang_key]['msgstr'];
	    }
	    
	    return $retVal;
	}	
	
	public static function trans() {
		if(is_file(APPLICATION_PATH.'/cache/lang_'.$GLOBALS['SETTINGS']['LANGUAGE_CODE'].'.cache') && $GLOBALS['SETTINGS']['DEBUG'] == false){
			$content = file_get_contents(APPLICATION_PATH.'/cache/lang_'.$GLOBALS['SETTINGS']['LANGUAGE_CODE'].'.cache');
			$GLOBALS['SETTINGS']['LANG'] = unserialize($content);
		}else {
			$pp = new POParser();
			$GLOBALS['SETTINGS']['LANG'] = array();
			foreach ($GLOBALS['SETTINGS']['LOCALE_PATHS'] as $langFile) {
			    $res = $pp->parse($langFile);
			    
			    
			    
			    foreach ($res[1] as $entry) {	        
			        if (isset($entry['msgid'])){
			            $GLOBALS['SETTINGS']['LANG'][Translation::encode_lang_key($entry['msgid'])] = $entry;
			        }       
			    }
			}
			
			file_put_contents(APPLICATION_PATH.'/cache/lang_'.$GLOBALS['SETTINGS']['LANGUAGE_CODE'].'.cache', serialize($GLOBALS['SETTINGS']['LANG']));   
		}		
	}
}


function pjango_gettext($message) {
	return Translation::gettext($message);
}


function pjango_ngettext($param) {
	return Translation::gettext($message);
}