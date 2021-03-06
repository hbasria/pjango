<?php

/**
 * BaseGroup
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $name
 * @property integer $site_id
 * @property Site $Site
 * @property Doctrine_Collection $Permissions
 * @property Doctrine_Collection $GroupPermissions
 * @property Doctrine_Collection $Users
 * @property Doctrine_Collection $GroupUsers
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseGroup extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('auth_group');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('name', 'string', 100, array(
             'type' => 'string',
             'length' => '100',
             ));
        $this->hasColumn('site_id', 'integer', null, array(
             'type' => 'integer',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Site', array(
             'local' => 'site_id',
             'foreign' => 'id'));

        $this->hasMany('Permission as Permissions', array(
             'refClass' => 'GroupPermission',
             'local' => 'group_id',
             'foreign' => 'permission_id'));

        $this->hasMany('GroupPermission as GroupPermissions', array(
             'local' => 'id',
             'foreign' => 'group_id'));

        $this->hasMany('User as Users', array(
             'refClass' => 'UserGroup',
             'local' => 'group_id',
             'foreign' => 'user_id'));

        $this->hasMany('UserGroup as GroupUsers', array(
             'local' => 'id',
             'foreign' => 'group_id'));
    }
}