<?php
/**
 * Installer script
 *
 * @category    Rycoweb
 * @package     Rycoweb_SpecialPromotions
 */
/** @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();


/**
 * Add bulk_price attribute to the 'eav/attribute' table
 */
$catalogInstaller = Mage::getResourceModel('catalog/setup', 'catalog_setup');
$catalogInstaller->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'bulk_price', array(
    'type'                       => 'decimal',
    'label'                      => 'Quantity Price',
    'input'                      => 'price',
    'backend'                    => 'catalog/product_attribute_backend_price',
    'required'                   => false,
    'visible'                    => true,
    'user_defined'               => true,
    'sort_order'                 => 20,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'used_in_product_listing'    => true,
    'used_for_promo_rules'       => true,
    'apply_to'                   => 'simple,configurable,virtual',
    'group'                      => 'Prices',
    'frontend_class'             => 'validate-special-price',
));

$installer->endSetup();