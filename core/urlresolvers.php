<?php

function get_callable($lookup_view, $can_fail=false){
	$parts = explode('.', $lookup_view);			
	$partsSize = sizeof($parts);
		
	//FIXME if ($partsSize < 3){
	
	$methodName = $parts[$partsSize-1];
	$className = ucwords(strtolower($parts[$partsSize-3])).ucwords(strtolower($parts[$partsSize-2]));
	$path = implode("/", array_slice($parts, 0, $partsSize-1)).'.php';
	
	@require_once($path);
	
	$callable = array( $className, $methodName ); 
	
	if( @is_callable( $callable ) === true ){ 
		return $callable;
	}else {
		return false;
	}
	//call_user_func_array(array('MyClass', 'myFunction'), array(0, 10));
	//echo $className;
}

class RegexURLPattern {
	private $name = false;
	private $regex = false;
	private $default_args = array();
	private $_callback = false;
	private $_callback_str = '';
	
	public function __construct($regex, $callback, $default_args=false, $name=false) {
		$this->regex = $regex;
		
		if (is_callable($callback)){
			//FIXME callback gelen fonksiyonu yüklemek gerekebilir.
			$this->_callback = $callback;
		}else {
			$this->_callback = false;
            $this->_callback_str = $callback;
		}
		
		$this->default_args = $default_args;
        $this->name = $name;		
	}
	
	public function __repr__() {
		return sprintf('{%s %s %s}', get_class($this), $this->name, $this->regex);
	}
	
	public function add_prefix($prefix = '') {
		
		if(strlen($prefix) < 1){
			return;
		}
		
		$this->_callback_str = $prefix.'.'.$this->_callback_str;
	}
	
	public function resolve($path = '') {
		$siteUrl = pjango_ini_get('SITE_URL');
		
		$pos = strrpos($path, '?');
        if ($pos !== false) {
        	$path = substr($path, 0, strrpos($path, '?'));
        }
        
        if(strlen($siteUrl)>0){
            $path = '/'.$path; 
            $path = str_replace($siteUrl.'/', '', $path);
        }

        //url nin sonunda / yoksa arka ekleyerek kontrol et
        if (strlen($path)>1){
        	$lastChar = substr($path, -1,1);
        	if ($lastChar != "/") $path = $path."/";
        }        
		
		//$match = preg_match('/'.str_replace('/', '\/', $this->regex).'/', $path, $params, PREG_OFFSET_CAPTURE, 3);
		$match = preg_match_all('/'.str_replace('/', '\/', $this->regex).'/', $path, $params, PREG_SET_ORDER);
		
		if($match){
			$matches = array();
			
			foreach ($params[0] as $key => $value) {
				if(is_string($key)) $matches[] = $value;
			}
			
			$this->default_args = $matches;
			return true;
		}else{
			//echo "eslesme yok  - ".$this->regex." - ".$path."<br/>";
		}
		
		return false;
		
	}
	
	public function _get_callback() {
		if ($this->_callback){
			return $this->_callback;
		}else {
			
			$this->_callback = get_callable($this->_callback_str);
			
			if ($this->_callback === false){
				throw new ViewDoesNotExist(sprintf("Could not import %s.", $this->_callback_str));
			}
			
			return $this->_callback;
		}
		
	}
	
	public function get_default_args() {
		return $this->default_args;
	}
	
}


function reverse($viewname = '') {
    $path = explode('.', $viewname);  
    $path = implode("/", $path);
    
    $returnPath = false;
    
    $tmpPath = APPLICATION_PATH.'/lib/'.$path;
    if(is_dir($tmpPath)){       
        $returnPath = $tmpPath;
    }
    
    if(is_file($tmpPath.'.php')){       
        $returnPath = $tmpPath.'.php';
    }
    
    $tmpPath = APPLICATION_PATH.'/apps/'.$path;
    if(is_dir($tmpPath)){       
        $returnPath = $tmpPath;
    }
    
    if(is_file($tmpPath.'.php')){       
        $returnPath = $tmpPath.'.php';
    }
    
    return $returnPath;	
}
