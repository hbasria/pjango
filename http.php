<?php 
require_once 'pjango/contrib/auth/util.php';

function getRequestUri() {
 	$retVal = isset($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : '';
	$retVal = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
	return $retVal;
}

class HttpRequest {
	const REQUEST_METHOD_GET    = 'get';
	const REQUEST_METHOD_POST   = 'post';
	const REQUEST_METHOD_PUT    = 'put';
	const REQUEST_METHOD_DELETE = 'delete';
	
	public $_request_method;
	public $_request_data;
	
	
	public $POST = false;
	public $user = false;
	
	public function __construct(){
		$this->_request_method = strtolower($_SERVER['REQUEST_METHOD']);
		
		if ($this->_request_method == HttpRequest::REQUEST_METHOD_GET){
			$this->_request_data = $_GET;
		}elseif ($this->_request_method == HttpRequest::REQUEST_METHOD_POST){
			$this->_request_data = $_POST;
		}elseif ($this->_request_method == HttpRequest::REQUEST_METHOD_PUT){
			$this->_request_data = file_get_contents('php://input'); 
		}
		
		if (isset($_POST) && count($_POST)>0){
			$this->POST = $_POST;
		}
		
	}
	
	public function request(){
		global $urlpatterns;
		$requestUri = getRequestUri();
		
		$this->user = get_user();
		
		$parts = explode('/', $requestUri);
		array_shift($parts);

		$requestUri = implode('/', $parts);
		$adminPageRegex = 'admin\/?';
		
		$match1 = preg_match('/'.$adminPageRegex.'/', $requestUri, $params1);
		
		if($match1){
			if ($this->user->is_staff != 1){
			    HttpResponseRedirect(pjango_ini_get('LOGIN_URL'));
				exit();
			}			
		}
		
		$regexURLPattern = NULL;
		


		foreach ($urlpatterns as $urlpattern){		
			if($urlpattern->resolve($requestUri)){
				$regexURLPattern = $urlpattern;
				break;
			}
		}
		
		if ($regexURLPattern !== NULL) {
			$param_arr = array($this);
			$param_arr = array_merge($param_arr, $regexURLPattern->get_default_args());
			
			call_user_func_array($regexURLPattern->_get_callback(), $param_arr);
		}else{
			//FIXME redirect 404
			echo "SAYFA YOK";
		}		
		
	}
	
	function is_ajax() {
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
	}
}

class HttpResponseRedirect {
	
  public function __construct($redirect_to, $status = 302) {
  	$is_IIS = false;
  	
  	
  	//FIXME https kontrol et
  	if ( substr($redirect_to, 0, 7) != 'http://' ){
  		$redirect_to = $GLOBALS['SETTINGS']['SITE_URL'].$redirect_to;
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