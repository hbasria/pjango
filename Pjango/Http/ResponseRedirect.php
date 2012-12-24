<?php
namespace Pjango\Http;

class ResponseRedirect {

    public function __construct($redirect_to, $status = 302) {
    	if ( !$redirect_to ) return false;
    	$parsedUrl = parse_url($redirect_to);
    	
    	if(!isset($parsedUrl['scheme'])){
    		$redirect_to = $GLOBALS['SETTINGS']['SITE_URL'].$redirect_to;
    	}

        if (!headers_sent()){
        	header("Location: $redirect_to", true, $status);
        }else{
        	echo '<script type="text/javascript">';
        	echo 'window.location.href="'.$redirect_to.'";';
        	echo '</script>';
        	echo '<noscript>';
        	echo '<meta http-equiv="refresh" content="0;url='.$redirect_to.'" />';
        	echo '</noscript>'; exit;
        }      
    }

}