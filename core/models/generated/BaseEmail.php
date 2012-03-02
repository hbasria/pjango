<?php

/**
 * BaseEmail
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $email
 * @property boolean $is_default
 * @property integer $email_type_id
 * @property EmailType $EmailType
 * 
 * @package    {P}jango
 * @subpackage ##SUBPACKAGE##
 * @author     Hasan Basri Ateş <hbasria@4net.com.tr>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseEmail extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('pjango_email');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('email', 'string', 100, array(
             'type' => 'string',
             'length' => '100',
             ));
        $this->hasColumn('is_default', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('email_type_id', 'integer', null, array(
             'type' => 'integer',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('EmailType', array(
             'local' => 'email_type_id',
             'foreign' => 'id'));
    }
}