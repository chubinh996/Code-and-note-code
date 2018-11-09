<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/


namespace Aheadworks\Pquestion\Model\Source\Email;

class Template extends \Magento\Config\Model\Config\Source\Email\Template
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [['value' => '', 'label' => __('Do not send')]];
        return array_merge($options, parent::toOptionArray());
    }
}
