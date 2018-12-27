<?php

namespace Webbureau\Custom\Controller\Index;

use Zend\Log\Filter\Timestamp;
use Magento\Framework\App\Filesystem\DirectoryList;

class Post extends \Magento\Framework\App\Action\Action {

    const XML_PATH_EMAIL_RECIPIENT_NAME = 'trans_email/ident_support/name';
    const XML_PATH_EMAIL_RECIPIENT_EMAIL = 'trans_email/ident_support/email';

    protected $_inlineTranslation;
    protected $_transportBuilder;
    protected $_scopeConfig;
    protected $_logLoggerInterface;
    protected $_storeManager;
    protected $uploaderFactory;
    protected $adapterFactory;
    protected $filesystem;

    public function __construct(
    \Magento\Framework\App\Action\Context $context, \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation, \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Psr\Log\LoggerInterface $loggerInterface, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory, \Magento\Framework\Image\AdapterFactory $adapterFactory, \Magento\Framework\Filesystem $filesystem, array $data = []
    ) {
        $this->_inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->_scopeConfig = $scopeConfig;
        $this->_logLoggerInterface = $loggerInterface;
        $this->messageManager = $context->getMessageManager();
        $this->_storeManager = $storeManager;
        $this->uploaderFactory = $uploaderFactory;
        $this->adapterFactory = $adapterFactory;
        $this->filesystem = $filesystem;
        parent::__construct($context);
    }

    public function execute() {
        $post = $this->getRequest()->getPost();
        try {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $customerSession = $objectManager->create('Magento\Customer\Model\Session');
            $customer = $objectManager->create('Magento\Customer\Model\Customer')->load($customerSession->getCustomer()->getId());
            $address = '';
            $shippingAddressId = $customer->getDefaultShipping();
            if ($shippingAddressId) {
                $shippingAddress = $objectManager->create('Magento\Customer\Model\Address')->load($shippingAddressId);
                $address = implode(' ', $shippingAddress->getStreet()) . ', ' . $shippingAddress->getCity() . ', ' . $shippingAddress->getRegion() . ' ' . $shippingAddress->getPostcode() . ', ' . $shippingAddress->getCountry();
            }
            $storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
            $currentStore = $storeManager->getStore();
            $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            $filePath = '';
            try {
                $uploaderFactory = $this->uploaderFactory->create(['fileId' => 'file_attachment']);
                $uploaderFactory->setAllowedExtensions(['doc', 'docx', 'pdf', 'jpg', 'jpeg', 'png', 'gif']);
                $imageAdapter = $this->adapterFactory->create();
                $uploaderFactory->setAllowRenameFiles(true);
                $uploaderFactory->setFilesDispersion(true);
                $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
                $destinationPath = $mediaDirectory->getAbsolutePath('op');
                $result = $uploaderFactory->save($destinationPath);
                $filePath = $mediaUrl . 'op' . $result['file'];
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;
            }
            $this->_inlineTranslation->suspend();
            $sender = [
                'name' => $this->_scopeConfig->getValue('trans_email/ident_general/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
                'email' => $this->_scopeConfig->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
            ];
            $transport = $this->_transportBuilder
                    ->setTemplateIdentifier('order_prescription_general_email_template')
                    ->setTemplateOptions(
                            [
                                'area' => 'frontend',
                                'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                            ]
                    )
                    ->setTemplateVars([
                        'name' => $post['name'],
                        'email' => $post['email'],
                        'company_address' => $address,
                        'telephone' => $post['telephone'],
                        'comment' => $post['comment'],
                        'file_attachment' => $filePath
                    ])
                    ->setFrom($sender)
                    ->addTo($this->_scopeConfig->getValue('order_prescription/general/recipient_email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE), $this->_scopeConfig->getValue('order_prescription/general/recipient_name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE))
                    ->addBcc([$post['email']])
                    ->setReplyTo($post['email'], $post['name'])
                    ->getTransport();
            $transport->sendMessage();
            $this->_inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
        $urlThankyoupage = $baseUrl . 'thank-you-for-your-order';
        $this->_redirect($urlThankyoupage);
    }

}
