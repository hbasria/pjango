<?php

/**
 * BaseSite
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $site_type
 * @property string $domain
 * @property string $name
 * @property string $status
 * @property Doctrine_Collection $Settings
 * @property Doctrine_Collection $PjangoMeta
 * @property Doctrine_Collection $PjangoToken
 * @property Doctrine_Collection $PjangoMedia
 * @property Doctrine_Collection $Group
 * @property Doctrine_Collection $User
 * @property Doctrine_Collection $PhoneType
 * @property Doctrine_Collection $Phone
 * @property Doctrine_Collection $EmailType
 * @property Doctrine_Collection $Email
 * @property Doctrine_Collection $AddressType
 * @property Doctrine_Collection $Address
 * @property Doctrine_Collection $Bonus
 * @property Doctrine_Collection $PageLayoutCategory
 * @property Doctrine_Collection $PageLayout
 * @property Doctrine_Collection $Contact
 * @property Doctrine_Collection $Tax
 * @property Doctrine_Collection $TaxRate
 * @property Doctrine_Collection $ProductCategory
 * @property Doctrine_Collection $ProductGroup
 * @property Doctrine_Collection $ProductType
 * @property Doctrine_Collection $ProductOption
 * @property Doctrine_Collection $ProductOptionValue
 * @property Doctrine_Collection $Product
 * @property Doctrine_Collection $PriceList
 * @property Doctrine_Collection $PriceItem
 * @property Doctrine_Collection $PriceItemDetail
 * @property Doctrine_Collection $PaymentActionType
 * @property Doctrine_Collection $PaymentMethod
 * @property Doctrine_Collection $Order
 * @property Doctrine_Collection $OrderItem
 * @property Doctrine_Collection $Invoice
 * @property Doctrine_Collection $InvoiceItem
 * @property Doctrine_Collection $ShippingMethod
 * @property Doctrine_Collection $ExpenseType
 * @property Doctrine_Collection $AppointmentTimeBlock
 * @property Doctrine_Collection $AppointmentTimeOff
 * @property Doctrine_Collection $AppointmentService
 * @property Doctrine_Collection $Appointment
 * @property Doctrine_Collection $PostCategory
 * @property Doctrine_Collection $Post
 * @property Doctrine_Collection $GuestBook
 * @property Doctrine_Collection $Poll
 * @property Doctrine_Collection $Menu
 * @property Doctrine_Collection $PForm
 * @property Doctrine_Collection $ShoppingCart
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseSite extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('pjango_site');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('site_type', 'string', 35, array(
             'type' => 'string',
             'length' => '35',
             ));
        $this->hasColumn('domain', 'string', 100, array(
             'type' => 'string',
             'length' => '100',
             ));
        $this->hasColumn('name', 'string', 100, array(
             'type' => 'string',
             'length' => '100',
             ));
        $this->hasColumn('status', 'string', 35, array(
             'type' => 'string',
             'length' => '35',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Settings', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('PjangoMeta', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('PjangoToken', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('PjangoMedia', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('Group', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('User', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('PhoneType', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('Phone', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('EmailType', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('Email', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('AddressType', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('Address', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('Bonus', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('PageLayoutCategory', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('PageLayout', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('Contact', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('Tax', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('TaxRate', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('ProductCategory', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('ProductGroup', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('ProductType', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('ProductOption', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('ProductOptionValue', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('Product', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('PriceList', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('PriceItem', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('PriceItemDetail', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('PaymentActionType', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('PaymentMethod', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('Order', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('OrderItem', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('Invoice', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('InvoiceItem', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('ShippingMethod', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('ExpenseType', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('AppointmentTimeBlock', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('AppointmentTimeOff', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('AppointmentService', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('Appointment', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('PostCategory', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('Post', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('GuestBook', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('Poll', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('Menu', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('PForm', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $this->hasMany('ShoppingCart', array(
             'local' => 'id',
             'foreign' => 'site_id'));

        $nestedset0 = new Doctrine_Template_NestedSet(array(
             'hasManyRoots' => true,
             'rootColumnName' => 'root_id',
             ));
        $this->actAs($nestedset0);
    }
}