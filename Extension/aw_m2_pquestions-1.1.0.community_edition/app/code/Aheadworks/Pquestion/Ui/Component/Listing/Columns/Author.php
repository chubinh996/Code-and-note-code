<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Pquestion\Ui\Component\Listing\Columns;

class Author extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        foreach ($dataSource['data']['items'] as & $item) {
            $authorName = $item['author_name'];
            if ($item['customer_id'] > 0) {
                $url = $this->context->getUrl('customer/index/edit', ['id' => $item['customer_id']]);

                $authorName = '<a onclick="setLocation(this.href)" href="' . $url . '">' . $authorName . '</a>';
            }
            $item['author_name'] = $authorName . '<br>' . $item['author_email'];
        }

        return $dataSource;
    }
}
