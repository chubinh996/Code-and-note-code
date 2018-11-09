<?php

namespace RDSC\Custom\Plugin\Catalog\Block;

class Toolbar {

    /**
     * Plugin
     *
     * @param \Magento\Catalog\Block\Product\ProductList\Toolbar $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\Data\Collection $collection
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function aroundSetCollection(
    \Magento\Catalog\Block\Product\ProductList\Toolbar $subject, \Closure $proceed, $collection
    ) {
        $currentOrder = $subject->getCurrentOrder();
        $result = $proceed($collection);
        if ($currentOrder) {
            if ($currentOrder == 'high_to_low') {
                $subject->getCollection()->setOrder('price', 'desc');
            } elseif ($currentOrder == 'low_to_high') {
                $subject->getCollection()->setOrder('price', 'asc');
            } elseif ($currentOrder == 'z_to_a') {
                $subject->getCollection()->setOrder('name', 'desc');
            } elseif ($currentOrder == 'a_to_z') {
                $subject->getCollection()->setOrder('name', 'asc');
            } elseif ($currentOrder == 'new_to_old') {
                $subject->getCollection()->setOrder('created_at', 'desc');
            } elseif ($currentOrder == 'old_to_new') {
                $subject->getCollection()->setOrder('created_at', 'asc');
            } elseif ($currentOrder == 'best_selling') {
                $collection->getSelect()->joinLeft(
                                'sales_order_item', 'e.entity_id = sales_order_item.product_id', array('qty_ordered' => 'SUM(sales_order_item.qty_ordered)'))
                        ->group('e.entity_id')
                        ->order('qty_ordered desc');
            }
        }
        return $result;
    }

}
