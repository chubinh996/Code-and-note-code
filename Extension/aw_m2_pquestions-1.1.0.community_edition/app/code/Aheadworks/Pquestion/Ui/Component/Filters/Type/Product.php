<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Pquestion\Ui\Component\Filters\Type;

class Product extends \Magento\Ui\Component\Filters\Type\Input
{
    /**
     * @return void
     */
    protected function applyFilter()
    {
        if (isset($this->filterData[$this->getName()])) {
            $value = $this->filterData[$this->getName()];

            if (!empty($value)) {
                $filter = $this->filterBuilder->setConditionType('like')
                    ->setField($this->getName())
                    ->setValue(sprintf('%s%%', $value))
                    ->create()
                ;
                $this->getContext()->getDataProvider()->addFilter($filter);
                $filter = $this->filterBuilder->setConditionType('eq')
                    ->setField('sharing_type')
                    ->setValue(\Aheadworks\Pquestion\Model\Source\Question\Sharing\Type::ORIGINAL_PRODUCT_VALUE)
                    ->create()
                ;
                $this->getContext()->getDataProvider()->addFilter($filter);
            }
        }
    }
}
