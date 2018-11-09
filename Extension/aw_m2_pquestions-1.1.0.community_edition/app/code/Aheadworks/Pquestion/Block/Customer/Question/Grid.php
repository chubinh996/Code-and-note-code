<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Pquestion\Block\Customer\Question;

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
     * @var \Aheadworks\Pquestion\Model\ResourceModel\Question\Collection
     */
    protected $_questionCollection;

    /**
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Aheadworks\Pquestion\Model\Source\Question\Status $sourceQuestionStatus
     * @param \Aheadworks\Pquestion\Model\ResourceModel\Question\Collection $questionCollection
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Customer\Model\Customer $customer,
        \Aheadworks\Pquestion\Model\Source\Question\Status $sourceQuestionStatus,
        \Aheadworks\Pquestion\Model\ResourceModel\Question\Collection $questionCollection,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_currentCustomer = $currentCustomer;
        $this->_customer = $customer;
        $this->_sourceQuestionStatus = $sourceQuestionStatus;
        $this->_questionCollection = $questionCollection;
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
        $this->_questionCollection->addFilterByCustomer($this->_customer);
        $this->setCollection($this->_questionCollection);
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
     * @param \Aheadworks\Pquestion\Model\Question $question
     *
     * @return string
     */
    public function getIsAnsweredLabelForQuestion(\Aheadworks\Pquestion\Model\Question $question)
    {
        $answerCount = $question->getApprovedAnswerCollection()->getSize();
        if ($answerCount < 1) {
            return __('Not yet');
        }
        if ($answerCount === 1) {
            return __('Yes (1 answer)');
        }
        return __('Yes (%1 answers)', $answerCount);
    }

    /**
     * @param \Aheadworks\Pquestion\Model\Question $question
     *
     * @return string
     */
    public function getStatusLabelByQuestion(\Aheadworks\Pquestion\Model\Question $question)
    {
        return $this->_sourceQuestionStatus->getOptionByValue($question->getStatus());
    }

    /**
     * @param \Aheadworks\Pquestion\Model\Question $question
     *
     * @return string
     */
    public function getProductUrlByQuestion(\Aheadworks\Pquestion\Model\Question $question)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $question->getProduct();
        if (null === $product->getId()) {
            return '';
        }
        return $product->getProductUrl();
    }

    /**
     * @param \Aheadworks\Pquestion\Model\Question $question
     *
     * @return string
     */
    public function getProductNameByQuestion(\Aheadworks\Pquestion\Model\Question $question)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $question->getProduct();
        if (null === $product->getId()) {
            return '';
        }
        return $product->getName();
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
            \Aheadworks\Pquestion\Model\Question::CACHE_TAG,
        ];
    }
}
