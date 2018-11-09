<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/


namespace Aheadworks\Pquestion\Model\ResourceModel\Question;

use Magento\Catalog\Api\Data\CategoryInterface;

/**
 * Class Collection
 *
 * @package Aheadworks\Pquestion\Model\ResourceModel\Question
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\ConfigFactory
     */
    protected $_catalogConfFactory;

    /**
     * @var \Magento\Catalog\Model\Entity\AttributeFactory
     */
    protected $_catalogAttrFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_dateTime;

    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    protected $metadataPool;

    /**
     * Collection constructor.
     *
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager
     * @param \Magento\Catalog\Model\ResourceModel\ConfigFactory           $catalogConfFactory
     * @param \Magento\Catalog\Model\Entity\AttributeFactory               $catalogAttrFactory
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface    $entityFactory
     * @param \Psr\Log\LoggerInterface                                     $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface                    $eventManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime                  $dateTime
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null          $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null    $resource
     * @param \Magento\Framework\EntityManager\MetadataPool                $metadataPool
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\ConfigFactory $catalogConfFactory,
        \Magento\Catalog\Model\Entity\AttributeFactory $catalogAttrFactory,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_storeManager = $storeManager;
        $this->_catalogConfFactory = $catalogConfFactory;
        $this->_catalogAttrFactory = $catalogAttrFactory;
        $this->_dateTime = $dateTime;
        $this->metadataPool = $metadataPool;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Aheadworks\Pquestion\Model\Question::class,
            \Aheadworks\Pquestion\Model\ResourceModel\Question::class
        );
        $this->addFilterToMap('store_id', 'main_table.store_id');
        $this->addFilterToMap('entity_id', 'main_table.entity_id');
    }

    /**
     * @return $this
     */
    public function joinPendingAnswerCount()
    {
        if (!$this->getFlag('answer_count_joined')) {
            $pendingStatus = \Aheadworks\Pquestion\Model\Source\Question\Status::PENDING_VALUE;
            $this->getSelect()->joinLeft(
                new \Zend_Db_Expr(
                    "(SELECT COUNT(entity_id) as pending_answers, question_id"
                    . " FROM {$this->getTable('aw_pq_answer')}"
                    . " WHERE  status={$pendingStatus}"
                    . " GROUP BY question_id)"
                ),
                "main_table.entity_id = t.question_id",
                ['pending_answers' => "IFNULL(t.pending_answers, 0)"]
            );
            $this->addFilterToMap('pending_answers', 't.pending_answers');
            $this->setFlag('answer_count_joined', true);
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function joinTotalAnswerCount()
    {
        if (!$this->getFlag('answer_total_count_joined')) {
            $this->getSelect()->joinLeft(
                new \Zend_Db_Expr(
                    "(SELECT COUNT(entity_id) as total_answers, question_id"
                    . " FROM {$this->getTable('aw_pq_answer')}"
                    . " GROUP BY question_id)"
                ),
                "main_table.entity_id = t_2.question_id",
                ['total_answers' => "IFNULL(t_2.total_answers, 0)"]
            );
            $this->addFilterToMap('total_answers', 't_2.total_answers');
            $this->setFlag('answer_total_count_joined', true);
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function joinProductName()
    {
        $linkField = $this->metadataPool->getMetadata(CategoryInterface::class)->getLinkField();
        if (!$this->getFlag('product_name_joined')) {
            $entityTypeId = $this->_catalogConfFactory->create()->getEntityTypeId();
            /** @var \Magento\Catalog\Model\Entity\Attribute $attribute */
            $attribute = $this->_catalogAttrFactory->create()->loadByCode($entityTypeId, 'name');

            $storeId = $this->_storeManager->getStore(\Magento\Store\Model\Store::ADMIN_CODE)->getId();

            $this->getSelect()->joinLeft(
                ['product_name' => $attribute->getBackendTable()],
                "product_name.{$linkField}=main_table.product_id" .
                ' AND product_name.store_id=' .
                $storeId .
                ' AND product_name.attribute_id=' .
                $attribute->getId(),
                ['product_name' => 'product_name.value']
            );
            $this->addFilterToMap('product_name', 'product_name.value');
            $this->setFlag('product_name_joined', true);
        }
        return $this;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return $this
     */
    public function addFilterByProduct(\Magento\Catalog\Model\Product $product)
    {
        $_productId = $product->getId();
        if (null === $_productId) {
            $_productId = 0;
        }
        $this
            ->getSelect()
            ->where(
                'sharing_type = ' .
                \Aheadworks\Pquestion\Model\Source\Question\Sharing\Type::ALL_PRODUCTS_VALUE
                . ' OR sharing_type = ' .
                \Aheadworks\Pquestion\Model\Source\Question\Sharing\Type::SPECIFIED_PRODUCTS_VALUE
                . ' OR (sharing_type = ' .
                \Aheadworks\Pquestion\Model\Source\Question\Sharing\Type::ORIGINAL_PRODUCT_VALUE
                . ' AND product_id = ' . intval($_productId) . ')'
            )
        ;
        return $this;
    }

    /**
     * @param int|string|Mage_Customer_Model_Customer $customer
     *
     * @return $this
     */
    public function addFilterByCustomer($customer)
    {
        $customerValue = $this->_getCustomerFilteredValue($customer);
        if (is_string($customerValue)) {
            return $this->addFieldToFilter('author_email', $customerValue);
        }
        return $this->addFieldToFilter('customer_id', $customerValue);
    }

    /**
     * int customerId | string customerEmail
     * @param int |string|\Magento\Customer\Model\Customer $customer
     *
     * @return int|string
     */
    protected function _getCustomerFilteredValue($customer)
    {
        if (is_string($customer)) {
            return $customer;
        }

        $customerId = $customer;
        $customerEmail = '';
        if ($customer instanceof \Magento\Customer\Model\Customer) {
            $customerEmail = $customer->getEmail();
            $customerId    = (int)$customer->getId();
            if (!$customerId && empty($customerEmail)) {
                $customerId = -1; //empty collection should be returned
            }
        }

        if ($customerId) {
            return $customerId;
        }
        return $customerEmail;
    }

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function addShowInStoresFilter($storeId)
    {
        $this
            ->getSelect()
            ->where("FIND_IN_SET(0, show_in_store_ids) OR FIND_IN_SET({$storeId}, show_in_store_ids)")
        ;
        return $this;
    }

    /**
     * @param int $visibility
     *
     * @return $this
     */
    public function addVisibilityFilter($visibility)
    {
        $this->addFieldToFilter('visibility', $visibility);
        return $this;
    }

    /**
     * @return $this
     */
    public function addPublicFilter()
    {
        return $this->addVisibilityFilter(\Aheadworks\Pquestion\Model\Source\Question\Visibility::PUBLIC_VALUE);
    }

    /**
     * @return $this
     */
    public function addPrivateFilter()
    {
        return $this->addVisibilityFilter(\Aheadworks\Pquestion\Model\Source\Question\Visibility::PRIVATE_VALUE);
    }

    /**
     * @param mixed $status
     *
     * @return $this
     */
    public function addStatusFilter($status)
    {
        $this->addFieldToFilter('status', $status);
        return $this;
    }

    /**
     * @return $this
     */
    public function addApprovedStatusFilter()
    {
        return $this->addStatusFilter(\Aheadworks\Pquestion\Model\Source\Question\Status::APPROVED_VALUE);
    }

    /**
     * @return $this
     */
    public function addCreatedAtLessThanNowFilter()
    {
        $dateFormat = $this->_dateTime->gmtDate();
        return $this->addFieldToFilter(
            'created_at',
            ['lteq' => $dateFormat]
        );
    }

    /**
     * @param number|null $from
     * @param number|null $to
     *
     * @return $this
     */
    public function addPendingAnswerFilter($from, $to)
    {
        $this->joinPendingAnswerCount();
        if (null !== $from) {
            $this->getSelect()->where("IFNULL(t.pending_answers, 0) >= ?", $from);
        }
        if (null !== $to) {
            $this->getSelect()->where("IFNULL(t.pending_answers, 0) <= ?", $to);
        }
        return $this;
    }

    /**
     * @param number|null $from
     * @param number|null $to
     *
     * @return $this
     */
    public function addTotalAnswerFilter($from, $to)
    {
        $this->joinTotalAnswerCount();
        if (null !== $from) {
            $this->getSelect()->where("IFNULL(t_2.total_answers, 0) >= ?", $from);
        }
        if (null !== $to) {
            $this->getSelect()->where("IFNULL(t_2.total_answers, 0) <= ?", $to);
        }
        return $this;
    }

    /**
     * @param string $sort
     *
     * @return $this
     */
    public function sortByHelpfull($sort = \Magento\Framework\DB\Select::SQL_DESC)
    {
        return $this->setOrder('helpfulness', $sort);
    }

    /**
     * @param string $sort
     *
     * @return $this
     */
    public function sortByDate($sort = \Magento\Framework\DB\Select::SQL_DESC)
    {
        return $this->setOrder('created_at', $sort);
    }
}
