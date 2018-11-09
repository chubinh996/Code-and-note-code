<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Pquestion\Controller\Question;

/**
 * Class Like
 * @package Aheadworks\Pquestion\Controller\Question
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Like extends \Aheadworks\Pquestion\Controller\Question
{
    /**
     * @var \Aheadworks\Pquestion\Model\Question
     */
    protected $_question;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;

    /**
     * @var \Magento\Customer\Model\Visitor
     */
    protected $_visitor;

    /**
     * @param \Aheadworks\Pquestion\Model\Question $question
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\Customer\Model\Visitor $visitor
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $coreDate
     * @param \Aheadworks\Pquestion\Helper\Config $configHelper
     * @param \Magento\Framework\Session\SessionManagerInterface $session
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Aheadworks\Pquestion\Model\Question $question,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Customer\Model\Visitor $visitor,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime\DateTime $coreDate,
        \Aheadworks\Pquestion\Helper\Config $configHelper,
        \Magento\Framework\Session\SessionManagerInterface $session,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
    ) {
        parent::__construct(
            $customerSession,
            $customer,
            $coreDate,
            $configHelper,
            $session,
            $storeManager,
            $product,
            $formKeyValidator,
            $context,
            $dateTime
        );
        $this->_question = $question;
        $this->_customer = $customer;
        $this->_visitor = $visitor;
    }

    /**
     * @return $this
     */
    public function execute()
    {
        $result = [
            'success'  => true,
            'messages' => [],
        ];

        if ($this->_isCustomerCanVoteQuestion()) {
            $questionId = (int)$this->getRequest()->getParam('question_id', 0);
            $questionModel = $this->_question->load($questionId);
            if ($questionModel->getId()) {
                if ($this->_customerSession->isLoggedIn()) {
                    $customer = $this->_customer->load($this->_customerSession->getCustomerId());
                } else {
                    $customer = $this->_visitor;
                }
                $value = $this->getRequest()->getParam('value', 1);
                try {
                    $questionModel->addHelpful($customer, $value);
                } catch (\Exception $e) {
                    $result['success'] = false;
                    $result['messages'][] = __($e->getMessage());
                }
            } else {
                $result['success'] = false;
                $result['messages'][] = __("Question not found.");
            }
        } else {
            $result['success'] = false;
            $result['messages'][] = __('Product Questions disabled');
        }

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        return $resultJson->setData($result);
    }
}
