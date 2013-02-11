<?php
require_once 'Pjango/Util/PhpThumb.php';
class CoreViews {
    
    function thumb($request) {
        header("Content-Type: image/png");
        header("Cache-Control: private, max-age=10800, pre-check=10800");
        header("Pragma: private");
        header("Expires: " . date(DATE_RFC822,strtotime(" 1 day")));  
    	
    	$imageSizes = array(
    			'thumb'		=>'100x75',
    			'square'	=>'150x150',    			
    			'small'		=>'640x480',
    			'medium'	=>'800x600',
    			'large'		=>'1024x768',
    			'original'	=>'1024x768',
    	);
    	///media/(.*?)/(thumb|square|small|medium|large)/
    	//if (preg_match("/ell/", "Hello World!", $matches)) {
    	
    	$urlArr = explode('/', $_SERVER["REQUEST_URI"]);
    	$imageSize = $urlArr[count($urlArr)-2];    	

    	if(array_key_exists($imageSize, $imageSizes)) {
    		unset($urlArr[count($urlArr)-2]);    		
    	}else {
    		$imageSize = 'original';
    	}
    	
    	$url = preg_replace('/(.*?)media\//i', '/media/', implode('/', $urlArr));    
    	$originalFile = urldecode(SITE_PATH.$url);
    	$cacheFolder = sprintf('%s/cache/images/%s', SITE_PATH, $imageSize);
    	$cacheFile = sprintf('%s/%s',$cacheFolder, implode('-', $urlArr));
        $fileInfo = pathinfo($originalFile);
        
        if(!is_dir($cacheFolder)){
        	@mkdir($cacheFolder, 0744, true);
        }        
        
        if (is_file(sprintf('%s/media/admn/img/icons/256/%s.png', APPLICATION_PATH, $fileInfo['extension']))){        	
        	readfile(sprintf('%s/media/admn/img/icons/256/%s.png', APPLICATION_PATH, $fileInfo['extension']));        	
        }else {
        	if (!is_file($cacheFile)){
        		if (is_file($originalFile)){
        			try {
        				$options = array('jpegQuality'=> 80);
        				$thumb = new PhpThumb($originalFile);
        				$thumb->setOptions($options);
        				 
        				//$newDimensions = $thumb->getCurrentDimensions();
        				//if($imageSize != 'original'){
        				$newDimensions = explode('x', $imageSizes[$imageSize]);
        				//}
        				$thumb->resize($newDimensions[0], $newDimensions[1]);
        				$thumb->save($cacheFile, 'png');
        			} catch (RuntimeException $e) {
        			}
        			
        		}
        	}
        	
        	if (is_file($cacheFile)){
        		header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($cacheFile)) . ' GMT');
        		readfile($cacheFile);
        	}else {
        		readfile($originalFile);
        	}        	
        }           
    }
}