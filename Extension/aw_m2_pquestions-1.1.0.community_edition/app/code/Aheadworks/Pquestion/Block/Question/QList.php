<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Pquestion\Block\Question;

use \Aheadworks\Pquestion\Model\Source\ProductPageCustomerAllowOptions as ProductPageCustomerAllowOptions;

/**
 * Class QList
 * @package Aheadworks\Pquestion\Block\Question
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class QList extends \Magento\Framework\View\Element\Template implements \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Aheadworks\Pquestion\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Aheadworks\Pquestion\Helper\Config
     */
    protected $_configHelper;

    /**
     * @var \Aheadworks\Pquestion\Helper\Helpfulness
     */
    protected $_helpfulnessHelper;

    /**
     * @var \Aheadworks\Pquestion\Helper\Request
     */
    protected $_requestHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var mixed
     */
    protected $_customerModel;

    /**
     * @var \Aheadworks\Pquestion\Model\ResourceModel\Question\Collection
     */
    protected $_questionCollection;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * @var \Aheadworks\Pquestion\Model\Source\Question\Sorting\Dir
     */
    protected $_sourceQuestionSortingDir;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $_formKey;

    /**
     * @param \Magento\Framework\Registry $registry
     * @param \Aheadworks\Pquestion\Helper\Data $helper
     * @param \Aheadworks\Pquestion\Helper\Config $configHelper
     * @param \Aheadworks\Pquestion\Helper\Helpfulness $helpfulnessHelper
     * @param \Aheadworks\Pquestion\Helper\Request $requestHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Aheadworks\Pquestion\Model\ResourceModel\Question\Collection $questionCollection
     * @param \Magento\Catalog\Model\Product $product
     * @param \Aheadworks\Pquestion\Model\Source\Question\Sorting\Dir $sourceQuestionSortingDir
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Aheadworks\Pquestion\Helper\Data $helper,
        \Aheadworks\Pquestion\Helper\Config $configHelper,
        \Aheadworks\Pquestion\Helper\Helpfulness $helpfulnessHelper,
        \Aheadworks\Pquestion\Helper\Request $requestHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Aheadworks\Pquestion\Model\ResourceModel\Question\Collection $questionCollection,
        \Magento\Catalog\Model\Product $product,
        \Aheadworks\Pquestion\Model\Source\Question\Sorting\Dir $sourceQuestionSortingDir,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_registry = $registry;
        $this->_helper = $helper;
        $this->_configHelper = $configHelper;
        $this->_helpfulnessHelper = $helpfulnessHelper;
        $this->_requestHelper = $requestHelper;
        $this->_customerSession = $customerSession;
        $this->_questionCollection = $questionCollection;
        $this->_product = $product;
        $this->_sourceQuestionSortingDir = $sourceQuestionSortingDir;
        $this->_formKey = $formKey;

        $this->setTitle(__('Product Questions'));
    }

    /**
     * @return bool
     */
    public function canAskQuestion()
    {
        if ($this->_customerSession->isLoggedIn()) {
            return true;
        }
        return $this->_configHelper->isAllowGuestToAskQuestion();
    }

    /**
     * @return bool
     */
    public function canAnswerQuestion()
    {
        switch ($this->_configHelper->getAllowCustomerToAddAnswer()) {
            case ProductPageCustomerAllowOptions::DENIED_VALUE:
                return false;
                break;
            case ProductPageCustomerAllowOptions::REGISTERED_CUSTOMERS_BOUGHT_PRODUCT_VALUE:
                if (!$this->_customerSession->isLoggedIn()) {
                    return false;
                }
                $customer = $this->_customerSession->getCustomerDataObject();
                if (!$this->_helper->isCustomerBoughtProduct($customer, $this->getProduct())) {
                    return false;
                }
                return true;
            case ProductPageCustomerAllowOptions::REGISTERED_CUSTOMERS_VALUE:
                if ($this->_customerSession->isLoggedIn()) {
                    return true;
                }
                return false;
            case ProductPageCustomerAllowOptions::ALL_CUSTOMERS_VALUE:
                return true;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getVoteMap()
    {
        if (!$this->hasData('vote_map')) {
            $questionIdList = [];
            foreach ($this->getQuestionCollection() as $question) {
                /** @var \Aheadworks\Pquestion\Model\Question $question */
                $questionIdList[] = $question->getId();
            }
            $voteMap = $this->_helpfulnessHelper->getVoteMap($questionIdList);
            $this->setData('vote_map', $voteMap);
        }
        return $this->getData('vote_map');
    }

    /**
     * @return bool
     */
    public function isCustomerCanVoteQuestion()
    {
        return $this->_customerSession->isLoggedIn()
            || $this->_configHelper->isAllowGuestRateHelpfulness()
        ;
    }

    /**
     * @return bool
     */
    public function isCustomerCanVoteAnswer()
    {
        return $this->_customerSession->isLoggedIn()
            || $this->_configHelper->isAllowGuestRateHelpfulness()
        ;
    }

    /**
     * @return string
     */
    public function getTitleForQuestionVote()
    {
        if (!$this->isCustomerCanVoteQuestion()) {
            return __("Only registered customers can rate.");
        }
        return "";
    }

    /**
     * @return string
     */
    public function getTitleForAnswerVote()
    {
        if (!$this->isCustomerCanVoteAnswer()) {
            return __("Only registered customers can rate.");
        }
        return "";
    }

    /**
     * @param int $questionId
     *
     * @return bool
     */
    public function isCustomerLikeQuestion($questionId)
    {
        $voteMap = $this->getVoteMap();
        $voteMap = $voteMap['question_vote_map'];
        if (array_key_exists($questionId, $voteMap) && $voteMap[$questionId] == 1) {
            return true;
        }
        return false;
    }

    /**
     * @param int $questionId
     *
     * @return bool
     */
    public function isCustomerDislikeQuestion($questionId)
    {
        $voteMap = $this->getVoteMap();
        $voteMap = $voteMap['question_vote_map'];
        if (array_key_exists($questionId, $voteMap) && $voteMap[$questionId] == -1) {
            return true;
        }
        return false;
    }

    /**
     * @param int $answerId
     *
     * @return bool
     */
    public function isCustomerLikeAnswer($answerId)
    {
        $voteMap = $this->getVoteMap();
        $voteMap = $voteMap['answer_vote_map'];
        if (array_key_exists($answerId, $voteMap) && $voteMap[$answerId] == 1) {
            return true;
        }
        return false;
    }

    /**
     * @param int $answerId
     *
     * @return bool
     */
    public function isCustomerDislikeAnswer($answerId)
    {
        $voteMap = $this->getVoteMap();
        $voteMap = $voteMap['answer_vote_map'];
        if (array_key_exists($answerId, $voteMap) && $voteMap[$answerId] == -1) {
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getAnswerMessage()
    {
        switch ($this->_configHelper->getAllowCustomerToAddAnswer()) {
            case ProductPageCustomerAllowOptions::REGISTERED_CUSTOMERS_BOUGHT_PRODUCT_VALUE:
                if (!$this->_customerSession->isLoggedIn()) {
                    return __(
                        "You must be <a href='%1'>logged in</a> to answer questions.",
                        $this->getLoginUrl()
                    );
                }
                $customer = $this->_customerSession->getCustomerDataObject();
                if (!$this->_helper->isCustomerBoughtProduct($customer, $this->getProduct())) {
                    return __('Only customers who bought the product can answer questions.');
                }
                break;
            case ProductPageCustomerAllowOptions::REGISTERED_CUSTOMERS_VALUE:
                return __("You must be <a href='%1'>logged in</a> to answer questions.", $this->getLoginUrl());
        }
        return '';
    }

    /**
     * @return \Aheadworks\Pquestion\Model\ResourceModel\Question\Collection
     */
    public function getQuestionCollection()
    {
        $collection = $this->_questionCollection;
        $collection
            ->addFilterByProduct($this->getProduct())
            ->addShowInStoresFilter($this->_storeManager->getStore()->getId())
            ->addPublicFilter()
            ->addApprovedStatusFilter()
            ->addCreatedAtLessThanNowFilter()
        ;
        if ($this->_getCurrentSortBy() == \Aheadworks\Pquestion\Model\Source\Question\Sorting::DATE_VALUE) {
            $collection->sortByDate($this->_getCurrentSortDir());
        } else {
            $collection->sortByHelpfull($this->_getCurrentSortDir());
        }
        $collection = $this->_helper->filterQuestionCollectionByConditions($collection, $this->getProduct());
        return $collection;
    }

    /**
     * @param \Aheadworks\Pquestion\Model\Question $question
     *
     * @return \Aheadworks\Pquestion\Model\ResourceModel\Answer\Collection
     */
    public function getAnswerCollectionForQuestion(\Aheadworks\Pquestion\Model\Question $question)
    {
        return $question->getApprovedAnswerCollection()
            ->addCreatedAtLessThanNowFilter()
        ;
    }

    /**
     * @return string
     */
    public function getQuestionPageSize()
    {
        return \Zend_Json::encode($this->_configHelper->getNumberQuestionsToDisplay());
    }

    /**
     * @return string
     */
    public function getAnswerPageSize()
    {
        return \Zend_Json::encode($this->_configHelper->getNumberAnswersToDisplay());
    }

    /**
     * @return string
     */
    public function getLoginUrl()
    {
        return $this->getUrl(
            'customer/account/index',
            ['_secure' => $this->_request->isSecure()]
        );
    }

    /**
     * @return string
     */
    public function getLikeQuestionUrl()
    {
        return $this->getUrl(
            'productquestion/question/like',
            [
                '_secure'     => $this->_request->isSecure(),
                'question_id' => 'placeholder',
            ]
        );
    }

    /**
     * @return string
     */
    public function getDislikeQuestionUrl()
    {
        return $this->getUrl(
            'productquestion/question/dislike',
            [
                '_secure'     => $this->_request->isSecure(),
                'question_id' => 'placeholder',
            ]
        );
    }

    /**
     * @return string
     */
    public function getLikeAnswerUrl()
    {
        return $this->getUrl(
            'productquestion/answer/like',
            [
                '_secure'   => $this->_request->isSecure(),
                'answer_id' => 'placeholder',
            ]
        );
    }

    /**
     * @return string
     */
    public function getDislikeAnswerUrl()
    {
        return $this->_urlBuilder->getUrl(
            'productquestion/answer/dislike',
            [
                '_secure'   => $this->_request->isSecure(),
                'answer_id' => 'placeholder',
            ]
        );
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if ($this->_registry->registry('product')) {
            return $this->_registry->registry('product');
        }
        return $this->_product->load(
            $this->_requestHelper->getRewriteProductId()
        );
    }

    /**
     * @param \Aheadworks\Pquestion\Model\Answer $answer
     *
     * @return string
     */
    public function getAnswerContent(\Aheadworks\Pquestion\Model\Answer $answer)
    {
        $content = $this->escapeHtml($answer->getContent());
        if ($this->_configHelper->isAllowDisplayUrlAsLink()) {
            $content = $this->_helper->parseContentUrls($content);
        }
        return nl2br($content);
    }

    /**
     * @param \Aheadworks\Pquestion\Model\Question $question
     *
     * @return string
     */
    public function getQuestionContent(\Aheadworks\Pquestion\Model\Question $question)
    {
        $content = $this->escapeHtml($question->getContent());
        if ($this->_configHelper->isAllowDisplayUrlAsLink()) {
            $content = $this->_helper->parseContentUrls($content);
        }
        return nl2br($content);
    }

    /**
     * @return string
     */
    public function getSessionFormKey()
    {
        return $this->_formKey->getFormKey();
    }

    /**
     * @return mixed
     */
    protected function _getCurrentSortBy()
    {
        return $this->_request->getParam(
            'orderby',
            $this->_configHelper->getDefaultQuestionsSortBy()
        );
    }

    /**
     * @return null|string
     */
    protected function _getCurrentSortDir()
    {
        return $this->_sourceQuestionSortingDir->getStorageValue(
            $this->_request->getParam(
                'dir',
                $this->_configHelper->getDefaultSortOrder()
            )
        );
    }

    /**
     * @param null $date
     * @param int $format
     * @param bool $showTime
     * @param null $timezone
     * @return string
     */
    public function formatDate(
        $date = null,
        $format = IntlDateFormatter::SHORT,
        $showTime = false,
        $timezone = null
    ) {
        $date = $date instanceof \DateTimeInterface ? $date : new \DateTime($date);
        $date = $this->_localeDate->date($date);
        return parent::formatDate($date, $format, $showTime, $timezone);
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [
            \Aheadworks\Pquestion\Model\Question::CACHE_TAG,
            \Aheadworks\Pquestion\Model\Answer::CACHE_TAG,
        ];
    }
}
