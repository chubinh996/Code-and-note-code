<?php
class Rycoweb_AllProducts_Block_AllProducts extends Mage_Catalog_Block_Product_List
{
    public function getAllProductsCollection()
    {
        $_productCollection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('*')
            ->addAttributeToSelect('dimensions')
            ->addAttributeToSelect('model_number')
            ->addAttributeToFilter('visibility', 4)
            ->load();
        return $_productCollection;
    }
    public function getCategoryName($categoryId)
    {
        $category = Mage::getModel('catalog/category')->setStoreId(Mage::app()->getStore()->getId())->load($categoryId);
        $categoryName = $category->getName();
        return $categoryName;
    }
}


