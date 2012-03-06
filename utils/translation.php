<?php 
require_once 'pjango/utils/poparser.php';

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
	    $languageCode = pjango_ini_get('LANGUAGE_CODE');
	    $siteId = pjango_ini_get('SITE_ID');
	    $isDebug = pjango_ini_get('DEBUG');
	    $cacheFile = sprintf('%s/cache/lang_%s_%s.cache',SITE_PATH, $siteId, $languageCode);
	    
		if(is_file($cacheFile) && $isDebug == false){
			$content = file_get_contents($cacheFile);
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
			
			file_put_contents($cacheFile, serialize($GLOBALS['SETTINGS']['LANG']));   
		}		
	}
}


function pjango_gettext($message) {
	return Translation::gettext($message);
}


function pjango_ngettext($param) {
	return Translation::gettext($message);
}