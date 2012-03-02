<?php

/**
 * ContentType
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class ContentType extends BaseContentType
{
	public static function findAllAsChoice() {
		$choicesArr = Doctrine_Query::create()
		    ->from('ContentType o')
		    ->fetchArray();
		    
		$choices = array('-' => '----------');
		foreach ($choicesArr as $value) {
			$choices[$value['id']] =  $value['app_label'].'.'.$value['model'];
		}
		
		
		return $choices;
	}
		
	public static function get_for_model($model) {
		
		$ct = Doctrine_Query::create()
	            ->from('ContentType o')          
	            ->addWhere('o.model = ?', array($model))
	            ->fetchOne(); 
	            
		if(!$ct){
			$ct = new ContentType();
			$ct->model = $model;
			$ct->save();
		}	            
	            
		return $ct;		
	}
	
	public static function get_for_id($id) {
		
		$ct = Doctrine_Query::create()
	            ->from('ContentType o')          
	            ->where('o.id = ?',$id)
	            ->fetchOne(); 
	    return $ct;		
	}	
	
	public function get_object_for_this_type($id) {
		$obj = Doctrine_Query::create()
	            ->from($this->model.' m')          
				->where('m.id = ?',$id)
	            ->fetchOne(); 		
		if ($obj) return $obj;
		else return false;		
	}
}