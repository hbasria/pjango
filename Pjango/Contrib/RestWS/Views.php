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
// 		$tmp = $_SERVER;
// 		$tmp['DATA'] = $request->_request_data;
// 		print_r($tmp);
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
	
	function ws_model_method($request, $model=false, $method=false, $id=false ) {
		$responseType = isset($_GET['responsetype']) ? $_GET['responsetype'] : 'xml';
		$resArr = array(); 
		
		if (class_exists($model)){			
			if (method_exists($model, $method)){				
				
				try {
					$resArr['results'] = call_user_func_array(array($model, $method), array($id));
				} catch (Exception $e) {
					$resArr['error']['code'] = $e->getCode();
					$resArr['error']['message'] = $e->getMessage();
				}				
				
				if ($resArr && count($resArr)>0){
					$resArr['message'] = count($resArr).' records found.';					
				}else {
					$resArr['message'] = 'no records found.';
				}				
			}else {
				$resArr['error']['message'] = $method.' does not exist.';
			}
		}else {
			$resArr['error']['message'] = $model.' does not exist.'; 
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