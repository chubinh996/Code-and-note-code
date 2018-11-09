<?php
/**
 * Installer/upgrade script
 *
 * @category    Rycoweb
 * @package     Rycoweb_SpecialPromotions
 */
/** @var $installer Mage_Sales_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();


/**
 * Add bulk_price attribute to quote_item and order_item table
 */
$installer->getConnection()
    ->addColumn($installer->getTable('sales/quote_item'), 'bulk_price', array(
        'type'    => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'comment' => 'Quantity Price',
        'scale'     => 4,
        'precision' => 12,
    ));

$installer->getConnection()
    ->addColumn($installer->getTable('sales/order_item'), 'bulk_price', array(
        'type'    => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'comment' => 'Quantity Price',
        'scale'     => 4,
        'precision' => 12,
    ));

$installer->endSetup();