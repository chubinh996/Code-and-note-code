<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Pquestion\Controller\Question;

class Add extends \Aheadworks\Pquestion\Controller\Question
{
    /**
     * @var \Aheadworks\Pquestion\Helper\Notification
     */
    protected $_notificationHelper;

    /**
     * @var mixed
     */
    protected $_storeManager;

    /**
     * @param \Aheadworks\Pquestion\Helper\Notification $notificationHelper
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
        \Aheadworks\Pquestion\Helper\Notification $notificationHelper,
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
        $this->_notificationHelper = $notificationHelper;
    }

    /**
     * @return $this
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!$this->_validateFormKey()) {
            return $resultRedirect->setRefererUrl();
        }

        try {
            $questionModel = $this->_initQuestion();
            $questionModel->save();
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
            return $resultRedirect->setRefererUrl();
        }

        $_isSubscribed = $this->_notificationHelper->isCanNotifyCustomer(
            $questionModel->getAuthorEmail(),
            \Aheadworks\Pquestion\Model\Source\Notification\Type::QUESTION_AUTO_RESPONDER
        );
        if (!$_isSubscribed) {
            if ($questionModel->getCustomerId()) {
                $successMessage = "Your question has been received."
                    . " You can track all your questions and answers <a href='%1'>here</a>.";
                $this->messageManager->addSuccess(
                    __(
                        $successMessage,
                        $this->_url->getUrl(
                            'productquestion/customer/index',
                            ['_secure' => $this->_storeManager->getStore(true)->isCurrentlySecure()]
                        )
                    )
                );
            } else {
                $this->messageManager->addSuccess(__('Your question has been received.'));
            }
        } else {
            if ($questionModel->getCustomerId()) {
                $successMessage = "Your question has been received. A notification will be sent once the answer "
                    . "is published. You can see all your questions and answers <a href='%1'>here</a>";
                $this->messageManager->addSuccess(
                    __(
                        $successMessage,
                        $this->_url->getUrl(
                            'productquestion/customer/index',
                            ['_secure' => $this->_storeManager->getStore(true)->isCurrentlySecure()]
                        )
                    )
                );
            } else {
                $this->messageManager->addSuccess(
                    __('Your question has been received. A notification will be sent once the answer is published.')
                );
            }
        }
        return $resultRedirect->setRefererUrl();
    }
}
