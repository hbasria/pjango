<?php

/**
 * City
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class City extends BaseCity
{
	public static function findAllAsChoice() {
		$choicesArr = Doctrine_Query::create()
			->select('o.id, o.name')
		    ->from('City o')
		    ->orderBy('o.weight')
		    ->fetchArray();
		    
		$choices = array();
		foreach ($choicesArr as $value) {
			$choices[$value['id']] =  $value['name'];
		}
		
		return $choices;
	}
}