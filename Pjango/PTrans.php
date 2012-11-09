<?php 

namespace Pjango;

class PTrans {
	
	public static function encode_lang_key($str) {    
	    return base64_encode(pack('H*',sha1($str)));
	}	
	
	public static function gettext($message) {
		$lang_key = PTrans::encode_lang_key($message);
		$retVal = $message;
		
		if (isset($GLOBALS['SETTINGS']['LANG'][$lang_key])){
	    	$retVal = $GLOBALS['SETTINGS']['LANG'][$lang_key]['msgstr'];
	    }
	    
	    return $retVal;
	}	
	
	public static function init($cacheFile, $isDebug = false) {
		if(is_file($cacheFile) && $isDebug == false){
			$content = file_get_contents($cacheFile);
			$GLOBALS['SETTINGS']['LANG'] = unserialize($content);
		}else {
			$pp = new POParser();
			$GLOBALS['SETTINGS']['LANG'] = array();
			
			foreach ($GLOBALS['SETTINGS']['LOCALE_PATHS'] as $langFile) {
				//dosya yoksa devam et
				if(!is_file($langFile)){
					continue;
				}
				
			    $res = $pp->parse($langFile);

			    foreach ($res[1] as $entry) {	        
			        if (isset($entry['msgid'])){
			            $GLOBALS['SETTINGS']['LANG'][self::encode_lang_key($entry['msgid'])] = $entry;
			        }       
			    }
			}
			
			if(is_writable($cacheFile)){
				file_put_contents($cacheFile, serialize($GLOBALS['SETTINGS']['LANG']));
			}						 
		}		
	}
}