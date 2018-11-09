<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Pquestion\Controller;

/**
 * Class Question
 * @package Aheadworks\Pquestion\Controller
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class Question extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_coreDate;

    /**
     * @var \Aheadworks\Pquestion\Helper\Config
     */
    protected $_configHelper;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $_session;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $_formKeyValidator;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_dateTime;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Customer $customer
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
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Framework\Stdlib\DateTime\DateTime $coreDate,
        \Aheadworks\Pquestion\Helper\Config $configHelper,
        \Magento\Framework\Session\SessionManagerInterface $session,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
    ) {
        parent::__construct($context);
        $this->_customerSession = $customerSession;
        $this->_customer = $customer;
        $this->_coreDate = $coreDate;
        $this->_configHelper = $configHelper;
        $this->_session = $session;
        $this->_storeManager = $storeManager;
        $this->_product = $product;
        $this->_formKeyValidator = $formKeyValidator;
        $this->_dateTime = $dateTime;
    }

    /**
     * @return \Aheadworks\Pquestion\Model\Question
     */
    protected function _initQuestion()
    {
        /** @var \Aheadworks\Pquestion\Model\Question $questionModel */
        $questionModel = $this->_objectManager->create(\Aheadworks\Pquestion\Model\Question::class);
        $productId = (int)$this->getRequest()->getParam('product_id', 0);
        $content = $this->getRequest()->getParam('content', '');
        $isPrivate = $this->getRequest()->getParam('is_private', false);

        if ($this->_customerSession->isLoggedIn()) {
            $this->_customer->load($this->_customerSession->getCustomerId());
            $authorName = $this->getRequest()->getParam('author_name', $this->_customer->getName());
            $authorEmail = $this->_customer->getEmail();
            $customerId = $this->_customer->getId();
        } else {
            $authorName = $this->getRequest()->getParam('author_name', null);
            $authorEmail = $this->getRequest()->getParam('author_email', null);
            $customerId = 0;
        }

        $visibility = \Aheadworks\Pquestion\Model\Source\Question\Visibility::PUBLIC_VALUE;
        if ($isPrivate) {
            $visibility = \Aheadworks\Pquestion\Model\Source\Question\Visibility::PRIVATE_VALUE;
        }

        $createdAt = $this->_coreDate->gmtDate();

        $questionModel
            ->setAuthorName($authorName)
            ->setAuthorEmail($authorEmail)
            ->setCustomerId($customerId)
            ->setContent($content)
            ->setVisibility($visibility)
            ->setStatus(\Aheadworks\Pquestion\Model\Source\Question\Status::PENDING_VALUE)
            ->setSharingType(\Aheadworks\Pquestion\Model\Source\Question\Sharing\Type::ORIGINAL_PRODUCT_VALUE)
            ->setProductId($productId)
            ->setSharingValue([$productId])
            ->setHelpfulness(0)
            ->setShowInStoreIds($this->_storeManager->getStore()->getId()) //Current Store
            ->setStoreId($this->_storeManager->getStore()->getId())
            ->setCreatedAt($createdAt)
        ;

        $this->_validate($questionModel);

        $this->_objectManager->get(\Magento\Framework\Registry::class)
            ->register('current_question', $questionModel, true)
        ;
        return $questionModel;
    }

    /**
     * @param mixed $questionModel
     *
     * @return void
     * @throws \Exception
     */
    protected function _validate($questionModel)
    {
        $authorName = $questionModel->getAuthorName();
        if (!is_string($authorName) || strlen($authorName) <= 0) {
            throw new \Exception(__("Author name not specified."));
        }

        $authorEmail = $questionModel->getAuthorEmail();
        if (!is_string($authorEmail) || strlen($authorEmail) <= 0) {
            throw new \Exception(__("Author email not specified."));
        }

        $content = $questionModel->getContent();
        if (!is_string($content) || strlen($content) <= 0) {
            throw new \Exception(__("Question not specified."));
        }

        $productModel = $this->_product->load($questionModel->getProductId());
        if (!$productModel->getId()) {
            throw new \Exception(__("Product not found."));
        }
    }

    /**
     * Retrieve whether customer can vote
     *
     * @return bool
     */
    protected function _isCustomerCanVoteQuestion()
    {
        return $this->_customerSession->isLoggedIn()
            || $this->_configHelper->isAllowGuestRateHelpfulness()
        ;
    }

    /**
     * Validate Form Key
     *
     * @return bool
     */
    protected function _validateFormKey()
    {
        return $this->_formKeyValidator->validate($this->getRequest());
    }
}
