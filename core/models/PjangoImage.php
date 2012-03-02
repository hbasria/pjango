<?php

/**
 * PjangoImage
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class PjangoImage extends BasePjangoImage
{

	public static function getImages($contentTypeId, $objectId, $imageId=false) {
		$retVal = false;
		$q = Doctrine_Query::create()
			->from('PjangoImage pi')
			->addWhere('pi.content_type_id = ? AND pi.object_id = ?', array($contentTypeId, $objectId));
	
		if ($imageId){
			$retVal = $q->addWhere('pi.id = ?', $imageId)
				->fetchOne();
		}else{
			$retVal = $q->execute();
		}
	
		return $retVal;
	}
	
	public function get_thumb_elem() {
		return sprintf('<img src="%s" height="32">', $this->image);		
	}

}