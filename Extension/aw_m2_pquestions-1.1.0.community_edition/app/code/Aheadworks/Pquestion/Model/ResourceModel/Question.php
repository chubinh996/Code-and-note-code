<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/


namespace Aheadworks\Pquestion\Model\ResourceModel;

use Magento\Framework\DataObject;

/**
 * Class Question
 *
 * @package Aheadworks\Pquestion\Model\ResourceModel
 */
class Question extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var array
     */
    protected $_serializableFields = ['sharing_value' => [null, []]];

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_pq_question', 'entity_id');
    }

    /**
     * {@inheritdoc}
     */
    protected function _unserializeField(DataObject $object, $field, $defaultValue = null)
    {
        $value = $object->getData($field);
        if ($value && is_string($value)) {
            $value = unserialize($object->getData($field));
            if (empty($value)) {
                $object->setData($field, $defaultValue);
            } else {
                $object->setData($field, $value);
            }
        } else {
            $object->setData($field, $defaultValue);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _serializeField(DataObject $object, $field, $defaultValue = null, $unsetEmpty = false)
    {
        $value = $object->getData($field);
        if (empty($value) && $unsetEmpty) {
            $object->unsetData($field);
        } else {
            $object->setData($field, serialize($value ?: $defaultValue));
        }

        return $this;
    }
}
