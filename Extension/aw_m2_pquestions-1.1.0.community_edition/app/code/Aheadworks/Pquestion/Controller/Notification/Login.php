<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Pquestion\Controller\Notification;

class Login extends \Aheadworks\Pquestion\Controller\Notification
{
    /**
     * @var \Aheadworks\Pquestion\Helper\Notification
     */
    protected $_notificationHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;

    /**
     * @param \Aheadworks\Pquestion\Helper\Notification $notificationHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Aheadworks\Pquestion\Helper\Notification $notificationHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Framework\App\Action\Context $context
    ) {
        parent::__construct($context);
        $this->_notificationHelper = $notificationHelper;
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
        $this->_customer = $customer;
    }

    /**
     * @return $this
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Forward $resultForward */
        $resultForward = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_FORWARD);

        $key = $this->getRequest()->getParam('key', null);
        if (null === $key) {
            return $resultForward->forward('noRoute');
        }
        $key = $this->_notificationHelper->decrypt($key);
        list($email, $redirectUrl, $storeId) = @explode('|', $key);
        if (empty($email) || empty($redirectUrl) || empty($storeId)) {
            return $resultForward->forward('noRoute');
        }
        if (!$this->_customerSession->isLoggedIn()) {
            $store = $this->_storeManager->getStore($storeId);
            $this->_customer->setWebsiteId($store->getWebsiteId())->loadByEmail($email);
            if (null !== $this->_customer->getId()) {
                $this->_customerSession->setCustomerAsLoggedIn($this->_customer);
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setUrl($redirectUrl);
    }
}
