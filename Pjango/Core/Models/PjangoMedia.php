<?php

/**
 * PjangoMedia
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class PjangoMedia extends BasePjangoMedia
{
	
	public function get_absolute_url() {
		if(strlen($this->file_path)>0){
			$image = preg_replace('/(.*?)media\//i', '/media/', $this->file_path);
			return pjango_ini_get('SITE_URL').$image;
       	}
       	return pjango_ini_get('MEDIA_URL').'/img/no-image.jpg';
	}
	
	public function thumb_url() {
		return $this->get_thumb_url('thumb');
	}
	
	public function square_url() {
		return $this->get_thumb_url('square');
	}		
	
	public function small_url() {
		return $this->get_thumb_url('small');
	}	
	
	public function medium_url() {
		return $this->get_thumb_url('medium');
	}	
	
	public function large_url() {
		return $this->get_thumb_url('large');
	}

	public function original_url() {
		return $this->get_thumb_url('original');
	}	
		
    public function get_thumb_url($size = 'original') {
    	$youtubeEmbed = strpos($this->description, '<iframe');
    	
    	if ($youtubeEmbed === false) {
    		if(strlen($this->file_path)>0){
    			$url = preg_replace('/(.*?)media\//i', '/media/', $this->file_path);
    			$urlArr = explode('/', pjango_ini_get('SITE_URL').$url);
    			
    			$cacheFile = sprintf('%s/cache/images/%s/%s',SITE_PATH, $size, implode('-', $urlArr));
    			
    			if (is_file($cacheFile)){
    				return sprintf('%s/cache/images/%s/%s',pjango_ini_get('SITE_URL'), $size, implode('-', $urlArr));
    			}else {
    				$lastItem = $urlArr[count($urlArr)-1];
    				$urlArr[count($urlArr)-1] = $size;
    				$urlArr[] = $lastItem;
    				return pjango_ini_get('SITE_URL').implode('/', $urlArr);
    			}
    		}
    	}else {
    		preg_match('/[^\s]*youtube\.com[^\s]*?embed\/([-\w]+)[^\s]*/', $this->description, $matches);
    		$videoId = false;
    		if (isset($matches[1])){
    			$videoId = $matches[1];
    		}
    	
    		if ($videoId){
    			return sprintf('http://img.youtube.com/vi/%s/default.jpg',$videoId);
    		}
    	}

    	return pjango_ini_get('MEDIA_URL').'/img/no-image.jpg';
    }
    
    public function get_thumb_elem($width=false, $height=32) {
    	$widthStr = ($width) ? sprintf(' width="%d" ', $width) : '';
    	$heightStr = ($height) ? sprintf(' height="%d" ', $height) : '';
    	
        return sprintf('<img src="%s" %s %s />', $this->thumb_url(), $widthStr, $heightStr);
                    
    }    
}