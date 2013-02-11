<?php

/**
 * PjangoMeta
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    {P}jango
 * @subpackage ##SUBPACKAGE##
 * @author     Hasan Basri Ateş <hbasria@4net.com.tr>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class PjangoMeta extends BasePjangoMeta
{
	public static function getMeta($contentTypeId, $objectId, $metaKey = false) {
		$q = Doctrine_Query::create()
			->from('PjangoMeta pm')
			->addWhere('pm.site_id = ? AND pm.content_type_id = ? AND pm.object_id = ?', array(SITE_ID, $contentTypeId, $objectId));
		
		if ($metaKey){
			$q = $q->addWhere('pm.meta_key = ?', $metaKey)
			->fetchOne();
		}else{
			$q = $q->execute();
		}
				
		return $q;
	}
	
	public static function getMetaValues($contentTypeId, $objectId, $metaKey = false) {
		$retVal = array();
		$metaData = PjangoMeta::getMeta($contentTypeId, $objectId);
		if($metaData && count($metaData)>0){
			if (count($metaData)>1){
				foreach ($metaData as $value) {
					$retVal[$value->meta_key] = $value->meta_value;
				}				
			}else {
				$retVal[$metaData->meta_key] = $metaData->meta_value;
			}
		}
		
		return $retVal;
	}
	
	public static function setMeta($contentTypeId, $objectId, $metaKeys = false, $metaValues = false) {
		// 		metaları bul
		if (!is_array($metaKeys)){
			$metaKeys = array();
			// 		meta_
			foreach ($metaValues as $key => $value) {
				if(substr($key, 0, 5) == 'meta_'){
					$metaKeys[] = $key;
				}
			}
		}		
		
		if (is_array($metaKeys) && is_array($metaValues)){
					
			foreach ($metaKeys as $key) {
				if (!isset($metaValues[$key])) continue;
					
				$meta = PjangoMeta::getMeta($contentTypeId, $objectId, $key);					
				if (!$meta) $meta = new PjangoMeta();
					
				$meta->content_type_id = $contentTypeId;
				$meta->site_id = SITE_ID;
				$meta->object_id = $objectId;
				$meta->meta_key = $key;
				
				if(is_array($metaValues[$key])){
				    $meta->meta_value = serialize($metaValues[$key]);
				}else {
				$meta->meta_value = $metaValues[$key];
				}				
				
				$meta->save();
			}
		}
		
	}

}