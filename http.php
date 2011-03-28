<?php 

class HttpResponseRedirect {
	
  public function __construct($redirect_to, $status = 302) {
  	$is_IIS = false;
  	
  	
  	if ( substr($redirect_to, 0, 7) != 'http://' ){
  		$redirect_to = $GLOBALS['SITE_URL'].$redirect_to;
  	}
  	
	if ( !$redirect_to ) // allows the wp_redirect filter to cancel a redirect
		return false;

	if ( $is_IIS ) {
		header("Refresh: 0;url=$redirect_to");
	} else {
		//if ( php_sapi_name() != 'cgi-fcgi' )
		//	status_header($status); // This causes problems on IIS and some FastCGI setups
		header("Location: $redirect_to", true, $status);
	}
  }
	
}

function HttpResponseRedirect($redirect_to) {
	$httpRes = new HttpResponseRedirect($redirect_to);
	exit();
}