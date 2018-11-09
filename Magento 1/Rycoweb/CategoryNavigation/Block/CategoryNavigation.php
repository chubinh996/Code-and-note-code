<?php
class Rycoweb_CategoryNavigation_Block_CategoryNavigation extends Mage_Core_Block_Template
{
    public function getParentCategories()
    {
        $categories = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('level', 2)
            ->addAttributeToFilter('is_active', 1)
            ->addAttributeToSort('name','ASC')
        ;
        return $categories;
    }
}


