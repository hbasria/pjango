<?php

/**
 * Permission
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Permission extends BasePermission
{
	public static function findAllAsChoice() {
		$choicesArr = Doctrine_Query::create()
		->select('o.id, o.name')
		->from('Permission o')
		->fetchArray();
	
		$choices = array();
		foreach ($choicesArr as $value) {
			$choices[$value['id']] =  $value['name'];
		}
	
		return $choices;
	}
	
	public static function get_or_create($name, $codename, $contentType) {	
		$q = Doctrine_Query::create()
			->from('Permission o')
			->where('o.codename = ? AND o.content_type_id = ?', array($codename, $contentType));
	
		$permission = $q->fetchOne();
	
		if(!$permission){
			$permission = new Permission();
			$permission->content_type_id = $contentType;
			$permission->codename = $codename;
			$permission->name = $name;
			$permission->save();
		}
		 
		return $permission;
	}	

}