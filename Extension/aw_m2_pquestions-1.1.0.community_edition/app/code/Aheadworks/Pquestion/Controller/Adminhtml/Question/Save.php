<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/


namespace Aheadworks\Pquestion\Controller\Adminhtml\Question;

use \Aheadworks\Pquestion\Model\Source\Question\Sharing\Type as SharingType;

/**
 * Class Save
 *
 * @package Aheadworks\Pquestion\Controller\Adminhtml\Question
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends \Aheadworks\Pquestion\Controller\Adminhtml\Question
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $dateTime;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Filter\FilterManager $filterManager
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $dateTime
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $dateTime
    ) {
        $this->dateTime = $dateTime;
        parent::__construct($context, $filterManager);
    }

    /**
     * Saving edited question information
     *
     * @return $this
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($formData = array_filter($this->getRequest()->getPostValue())) {
            $preparedData = $this->getPreparedData($formData['data']);
            $questionModel = $this->_initQuestion();
            try {
                $questionModel->addData($preparedData);
                $questionModel->save();

                $this->saveAnswerList($formData, $questionModel->getId());

                $this->messageManager->addSuccess(__('Question %1 saved successfully.', $this->getQuestionEditLink()));
                $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setPQFormData(null);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        ['id' => $questionModel->getId(), 'tab' => $this->getRequest()->getParam('tab', null)]
                    );
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_getSession()->setPQFormData($formData);
                return $resultRedirect->setPath(
                    '*/*/edit',
                    [
                        'id'          => $this->getRequest()->getParam('id', null),
                        'customer_id' => $this->getRequest()->getParam('customer_id', null),
                        'product_id'  => $this->getRequest()->getParam('product_id', null),
                        'tab'         => $this->getRequest()->getParam('tab', null)
                    ]
                );
            }
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Saving edited answer information
     *
     * @param mixed $data
     * @param mixed $questionId
     *
     * @return $this
     */
    private function saveAnswerList(&$data, $questionId)
    {
        if (array_key_exists('answer', $data)) {
            foreach ($data['answer'] as $answerId => $answerData) {
                $answerModel = $this->_objectManager->create(\Aheadworks\Pquestion\Model\Answer::class);
                $answerModel->load($answerId);
                if (null === $answerModel) {
                    continue;
                }
                $answerModel->addData($answerData);
                $answerModel->save();
            }
            unset($data['answer']);
        }
        if (array_key_exists('new_answer', $data) && array_key_exists('content', $data['new_answer'])
            && !empty(trim($data['new_answer']['content'])) ) {
            $answerModel = $this->_objectManager->create(\Aheadworks\Pquestion\Model\Answer::class);
            $adminSessionUser = $this->_objectManager->get(\Magento\Backend\Model\Auth\Session::class)->getUser();
            $answerModel->setIsAdmin(true);
            $currentDate = new \Zend_Date;
            $answerModel
                ->setAuthorName(
                    trim($adminSessionUser->getFirstname() . ' ' . $adminSessionUser->getLastname())
                )
                ->setStatus(\Aheadworks\Pquestion\Model\Source\Question\Status::APPROVED_VALUE)
                ->setCreatedAt($currentDate->toString(\Magento\Framework\Stdlib\DateTime::DATETIME_INTERNAL_FORMAT))
                ->setAuthorEmail($adminSessionUser->getEmail())
                ->setCustomerId(0)
                ->setHelpfulness(0)
            ;
            $answerModel->addData($data['new_answer']);
            $answerModel->setQuestionId($questionId);
            $answerModel->save();
            unset($data['new_answer']);
        }
        return $this;
    }

    /**
     * Prepare data before save
     *
     * @param array $data
     * @return array
     */
    private function getPreparedData($data)
    {
        if (isset($data['created_at'])) {
            $locale = new \Zend_Locale($this->_localeResolver->getLocale());
            $date = new \Zend_Date(null, null, $locale);
            $format = $locale->getTranslation(null, 'datetime', $locale);
            $date->setDate($data['created_at'], $format);
            $date->setTime($data['created_at'], $format);

            $data['created_at'] = $this->dateTime->convertConfigTimeToUtc($date->toString('YYYY-MM-dd H:m:s'));
        }
        if ($statusId = $this->_request->getParam('status_id', false)) {
            $data['status'] = $statusId;
        }
        if ($data['sharing_type'] == SharingType::SPECIFIED_PRODUCTS_VALUE) {
            $data['sharing_value'] = $this->getRequest()->getParam('parameters', []);
            $data['product_id'] = "";
        } else if ($data['sharing_type'] == SharingType::ORIGINAL_PRODUCT_VALUE) {
            $data['sharing_value'] = $data['product_id'] ? [$data['product_id']] : [];
        } else if ($data['sharing_type'] == SharingType::ALL_PRODUCTS_VALUE) {
            $data['product_id'] = "";
        }
        unset($data['entity_id']);
        return $data;
    }
}
