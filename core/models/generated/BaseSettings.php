<?php

/**
 * BaseSettings
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $name
 * @property string $value
 * @property string $description
 * @property string $category
 * 
 * @package    {P}jango
 * @subpackage ##SUBPACKAGE##
 * @author     Hasan Basri Ateş <hbasria@4net.com.tr>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseSettings extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('settings');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('name', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('value', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('description', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('category', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        
    }
}