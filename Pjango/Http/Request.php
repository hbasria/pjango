<?php
namespace Pjango\Http;
use \User;

require_once 'Pjango/Http.php';

class Request {
	const REQUEST_METHOD_GET    = 'get';
	const REQUEST_METHOD_POST   = 'post';
	const REQUEST_METHOD_PUT    = 'put';
	const REQUEST_METHOD_DELETE = 'delete';
	
	public $_request_method;
	public $_request_data;
	public $_request_uri;
	
	public $POST = false;
	public $GET = array();
	public $user = false;
	public $is_mobile = false;
	
	public function __construct(){
		$this->_request_method = strtolower($_SERVER['REQUEST_METHOD']);
		
		if ($this->_request_method == self::REQUEST_METHOD_GET){
			$this->_request_data = $_GET;
		}elseif ($this->_request_method == self::REQUEST_METHOD_POST){
			$this->_request_data = $_POST;
		}elseif ($this->_request_method == self::REQUEST_METHOD_PUT){
			$this->_request_data = file_get_contents('php://input'); 
		}
		
		$this->_request_uri = isset($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : '';
		$this->_request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $this->_request_uri;
		
		if (get_magic_quotes_gpc()) {
			$_POST = array_map('stripslashes_deep', $_POST);
			$_GET = array_map('stripslashes_deep', $_GET);
			$_COOKIE = array_map('stripslashes_deep', $_COOKIE);
			$_REQUEST = array_map('stripslashes_deep', $_REQUEST);
		}		
		
		$this->GET = $_GET;
		if (isset($_POST) && count($_POST)>0){
			$this->POST = $_POST;
		}
		
		$this->is_mobile = is_mobile();
	}
	
	public function request(){
		global $urlpatterns;
		$requestUri = $this->_request_uri;
		
		$this->user = User::get_current();	
		
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
			$callbackArr = $regexURLPattern->_get_callback();
			
			call_user_func_array(array(new $callbackArr[0], $callbackArr[1]), $param_arr);			
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