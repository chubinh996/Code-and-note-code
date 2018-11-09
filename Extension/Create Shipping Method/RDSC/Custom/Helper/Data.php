<?php

namespace RDSC\Custom\Helper;

use Magento\Framework\Registry;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    protected $_objectManager;
    private $_registry;
    protected $_filterProvider;
    private $_messageManager;
    protected $_configFactory;

    public function __construct(
    \Magento\Framework\App\Helper\Context $context, \Magento\Framework\ObjectManagerInterface $objectManager, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Cms\Model\Template\FilterProvider $filterProvider, \Magento\Framework\Message\ManagerInterface $messageManager, \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configFactory, Registry $registry
    ) {
        $this->_storeManager = $storeManager;
        $this->_objectManager = $objectManager;
        $this->_filterProvider = $filterProvider;
        $this->_registry = $registry;
        $this->_messageManager = $messageManager;
        $this->_configFactory = $configFactory;

        parent::__construct($context);
    }

    public function getCategoryThumbnailUrl($category) {
        $url = false;
        $image = $category->getThumbnail();
        if ($image) {
            if (is_string($image)) {
                $url = $this->_storeManager->getStore()->getBaseUrl(
                                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                        ) . 'catalog/category/' . $image;
            } else {
                return $url;
            }
        }

        return $url;
    }

}
