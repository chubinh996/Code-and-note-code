<?php
namespace MGS\AjaxCart\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;

class Sliderproduct extends Action
{
	public function execute()
    {
    	$pgFatory = $this->_objectManager->create('Magento\Framework\View\Result\PageFactory');
    	$resultPage = $pgFatory->create();
    	$block = $resultPage->getLayout()
                ->createBlock('Magento\Framework\View\Element\Template')
                ->setTemplate('MGS_AjaxCart::cart/product_in_cart.phtml')
                ->toHtml();
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($block); 
    	return $resultJson; 
    }

}