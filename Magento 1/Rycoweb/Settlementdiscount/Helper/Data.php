<?php
/**
 *
 * @category   Rycoweb
 * @package    Rycoweb Settlementdiscount
 */
class Rycoweb_Settlementdiscount_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getProductPrice($_productId)
    {
        $_productObj = Mage::getModel('catalog/product');
        $_product = $_productObj->load($_productId);
        return $_product->getPrice();
    }
}