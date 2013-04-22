<?php

function array2xml($array, $xml = false){
	if($xml === false){
		$xml = new SimpleXMLElement("<?xml version=\"1.0\"?><RestWS></RestWS>");
	}
	
	foreach($array as $key => $value){
		if(is_array($value)){
			if ($key == '0') $key = 'item';
			array2xml($value, $xml->addChild($key));
		}else{
			$xml->addChild($key, $value);
		}
	}
	return $xml->asXML();
}

class RestWSViews {
	function ws_index($request) {
		$resArr = array();
// 		$tmp = $_SERVER;
 		$resArr['REQUEST'] = $request->_request_data;
 		print_r($resArr);
	}
	
	function ws_model_id($request, $model=false, $id=false ) {
		$resArr = array();
		
		if (class_exists($model)){
			
			if ($request->_request_method == HttpRequest::REQUEST_METHOD_GET){
				$obj = Doctrine::getTable($model)->find($id);
				
				if ($obj) $resArr['data'] = $obj->toArray();
				
			}elseif ($request->_request_method == HttpRequest::REQUEST_METHOD_POST){
				$obj = Doctrine::getTable($model)->find($id);
				
				if(!$obj) $obj = new $model();
				
				try {
					$obj->fromArray($request->_request_data);
					$obj->save();
					
					$resArr['data'] = $obj->toArray();
					
					$resArr['code'] = "200";
					$resArr['message'] = "OK";
					
				} catch (Exception $e) {
					$resArr['code'] = "304";
					$resArr['error']['message'] = "Not Modified";
				}
			}else {
				$resArr['code'] = "400";
				$resArr['message'] = "Bad Request";
				$resArr['error']['message'] = "Bad Request";
			}
			

		}else {
			$resArr['code'] = "404";
			$resArr['message'] = "Not Found";
			$resArr['error']['message'] = $model.' Not Found'; 
		}
		
		

		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header("Content-type: text/xml; charset=utf-8");
		if ($request->is_ajax()){
			echo json_encode($resArr);
		}else{
			require_once 'XML/Serializer.php';
			
			$serializer = new XML_Serializer();
			$serializer->setOption(XML_SERIALIZER_OPTION_INDENT, '    ');
			$serializer->setOption(XML_SERIALIZER_OPTION_ROOT_NAME, 'RestWS');			
			$serializer->setOption(XML_SERIALIZER_OPTION_XML_ENCODING, 'UTF-8');			
			$serializer->setOption(XML_SERIALIZER_OPTION_LINEBREAKS, "\n");
			$serializer->setOption(XML_SERIALIZER_OPTION_DEFAULT_TAG, 'item');
			$serializer->setOption(XML_SERIALIZER_OPTION_CDATA_SECTIONS, FALSE);

			$result = $serializer->serialize($resArr);
			$xml = $serializer->getSerializedData();
			echo $xml;
		}		
					
	}	
	
	function ws_model_method($request, $model=false, $method=false) {
		
		$arrRequestUri = parse_url($_SERVER["REQUEST_URI"]);
		$path = $arrRequestUri['path'];
		
		if(substr($path, -1) == '/') $path = substr($path, 0, -1);
		if(substr($path, -5) == '.json') {
			$responseType = 'json';
		}else if(substr($path, -4) == '.xml') {
			$responseType = 'xml';
		}else {
			if (isset($_GET['responsetype']) && $_GET['responsetype'] == 'json') {
				$responseType = 'json';				
			}else {
				$responseType = 'xml';
			}			
		}
		
		$resArr = array(); 
		
		try {
			//if(!array_key_exists($model, $arrModelMethod)) throw new Exception('Request not found1', 404);
			//if(!in_array($method, $arrModelMethod[$model])) throw new Exception('Request not found2', 404);
			if(!is_callable($model.'::'.$method)) throw new Exception('Request not found', 404);
				
			//$apiKey = Doctrine_Query::create()
			//	->from('Settings o')
			//	->where('o.category = ? AND o.site_id = ? AND o.name = ? AND o.value = ?', array('GENERAL', SITE_ID, 'API_KEY', $_GET['key']))
			//	->fetchOne();
				
			//if(!$apiKey) throw new Exception('API Key not found', 401);
			//if(strlen($apiKey->value)<30) throw new Exception('API Key not found', 401);
				
			$resArr['results'] = call_user_func_array(array($model, $method), array());
				
				
		} catch (Exception $e) {
			$resArr['error']['code'] = $e->getCode();
			$resArr['error']['message'] = $e->getMessage();
		}		
		
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');		
		if ($responseType == 'json'){
			echo json_encode($resArr);
		}else{
			header("Content-type: text/xml; charset=utf-8");
			echo array2xml($resArr);
		}		
		
	}
	

}