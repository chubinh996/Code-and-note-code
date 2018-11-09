<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Pquestion\Block\Adminhtml\Question\Edit\Tab\General\Answers;

/**
 * Class ElementRenderer
 *
 * @package Aheadworks\Pquestion\Block\Adminhtml\Question\Edit\Tab\General\Answers
 * @method \Aheadworks\Pquestion\Model\Answer getAnswer
 */
class ElementRenderer extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'Aheadworks_Pquestion::answers/element.phtml';

    /**
     * @var \Aheadworks\Pquestion\Model\Source\Question\Status
     */
    protected $_sourceStatus;

    /**
     * @param \Aheadworks\Pquestion\Model\Source\Question\Status $sourceStatus
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Aheadworks\Pquestion\Model\Source\Question\Status $sourceStatus,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_sourceStatus = $sourceStatus;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->getAnswer()->getId();
    }

    /**
     * @return string
     */
    public function getQuestionId()
    {
        return $this->getAnswer()->getQuestionId();
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->getAnswer()->getContent();
    }

    /**
     * @return string
     */
    public function getCustomerId()
    {
        return $this->getAnswer()->getCustomerId();
    }

    /**
     * @return string
     */
    public function getAuthorName()
    {
        return $this->getAnswer()->getAuthorName();
    }

    /**
     * @return string
     */
    public function getAuthorEmail()
    {
        return $this->getAnswer()->getAuthorEmail();
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return !!$this->getAnswer()->getIsAdmin();
    }

    /**
     * @return string
     */
    public function getRating()
    {
        return $this->getAnswer()->getHelpfulness();
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->_sourceStatus->getOptionByValue($this->getAnswer()->getStatus());
    }

    /**
     * @return bool
     */
    public function isCanPublish()
    {
        return $this->getAnswer()->getStatus() != \Aheadworks\Pquestion\Model\Source\Question\Status::APPROVED_VALUE;
    }

    /**
     * @return bool
     */
    public function isCanReject()
    {
        return $this->getAnswer()->getStatus() != \Aheadworks\Pquestion\Model\Source\Question\Status::DECLINE_VALUE;
    }

    /**
     * @return string
     */
    public function getCustomerUrl()
    {
        return $this->getUrl('customer/index/edit', ['id' => $this->getAnswer()->getCustomerId()]);
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('productquestion/answer/delete', ['id' => $this->getAnswer()->getId()]);
    }

    /**
     * @return string
     */
    public function getRejectUrl()
    {
        return $this->getUrl(
            'productquestion/answer/changeStatus',
            [
                'id' => $this->getAnswer()->getId(),
                'status_id' => \Aheadworks\Pquestion\Model\Source\Question\Status::DECLINE_VALUE
            ]
        );
    }

    /**
     * @return string
     */
    public function getPublishUrl()
    {
        return $this->getUrl('productquestion/answer/save', ['id' => $this->getAnswer()->getId()]);
    }
}
