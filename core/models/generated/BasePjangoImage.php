<?php

/**
 * BasePjangoImage
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $title
 * @property string $image
 * @property string $description
 * @property boolean $is_default
 * @property boolean $is_active
 * @property integer $object_id
 * @property integer $content_type_id
 * @property ContentType $ContentType
 * 
 * @package    {P}jango
 * @subpackage ##SUBPACKAGE##
 * @author     Hasan Basri Ateş <hbasria@4net.com.tr>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BasePjangoImage extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('pjango_image');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('title', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('image', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('description', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('is_default', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('is_active', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('object_id', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('content_type_id', 'integer', null, array(
             'type' => 'integer',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('ContentType', array(
             'local' => 'content_type_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}