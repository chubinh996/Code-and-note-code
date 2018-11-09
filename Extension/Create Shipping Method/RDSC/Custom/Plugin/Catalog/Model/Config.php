<?php

namespace RDSC\Custom\Plugin\Catalog\Model;

class Config {

    public function afterGetAttributeUsedForSortByArray(
    \Magento\Catalog\Model\Config $catalogConfig, $options
    ) {
        unset($options['position']);
        unset($options['name']);
        unset($options['price']);
        $options['best_selling'] = __('Best Selling');
        $options['a_to_z'] = __('Alphabetically, A-Z');
        $options['z_to_a'] = __('Alphabetically, Z-A');
        $options['low_to_high'] = __('Price, low to high');
        $options['high_to_low'] = __('Price, high to low');
        $options['new_to_old'] = __('Date, new to old');
        $options['old_to_new'] = __('Date, old to new');
        return $options;
    }

}
