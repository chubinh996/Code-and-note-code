<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Pquestion\Block\Customer\Answer;

class Grid extends \Magento\Framework\View\Element\Template implements \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $_currentCustomer;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;

    /**
     * @var \Aheadworks\Pquestion\Model\Source\Question\Status
     */
    protected $_sourceQuestionStatus;

    /**
     * @var mixed
     */
    protected $_questionCollection;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Aheadworks\Pquestion\Model\Source\Question\Status $sourceQuestionStatus
     * @param \Aheadworks\Pquestion\Model\ResourceModel\Answer\Collection $answerCollection
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Customer\Model\Customer $customer,
        \Aheadworks\Pquestion\Model\Source\Question\Status $sourceQuestionStatus,
        \Aheadworks\Pquestion\Model\ResourceModel\Answer\Collection $answerCollection,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_currentCustomer = $currentCustomer;
        $this->_customer = $customer;
        $this->_sourceQuestionStatus = $sourceQuestionStatus;
        $this->_answerCollection = $answerCollection;
        $this->_productFactory = $productFactory;

        $this->_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $this->_customer->load(
            $this->_currentCustomer->getCustomerId()
        );
        $this->_answerCollection->addFilterByCustomer($this->_customer);
        $this->setCollection($this->_answerCollection);
        return $this;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->getCollection()->load();
        return $this;
    }

    /**
     * @param \Aheadworks\Pquestion\Model\Answer $answer
     *
     * @return string
     */
    public function getProductUrlByAnswer(\Aheadworks\Pquestion\Model\Answer $answer)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->_productFactory->create()->load($answer->getProductId());
        if (null === $product->getId()) {
            return '';
        }
        return $product->getProductUrl();
    }

    /**
     * @param \Aheadworks\Pquestion\Model\Answer $answer
     *
     * @return string
     */
    public function getProductNameByAnswer(\Aheadworks\Pquestion\Model\Answer $answer)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->_productFactory->create()->load($answer->getProductId());
        if (null === $product->getId()) {
            return '';
        }
        return $product->getName();
    }

    /**
     * @param \Aheadworks\Pquestion\Model\Answer $answer
     *
     * @return string
     */
    public function getStatusLabelByAnswer(\Aheadworks\Pquestion\Model\Answer $answer)
    {
        return $this->_sourceQuestionStatus->getOptionByValue($answer->getStatus());
    }

    /**
     * @param mixed $value
     * @param int $length
     * @param string $etc
     * @param string $remainder
     * @param bool $breakWords
     * @return string
     */
    public function truncateString($value, $length = 80, $etc = '...', &$remainder = '', $breakWords = true)
    {
        return $this->filterManager->truncate(
            $value,
            ['length' => $length, 'etc' => $etc, 'remainder' => $remainder, 'breakWords' => $breakWords]
        );
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [
            \Aheadworks\Pquestion\Model\Answer::CACHE_TAG,
        ];
    }
}
