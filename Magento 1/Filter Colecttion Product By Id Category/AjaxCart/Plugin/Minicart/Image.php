<?php

namespace MGS\AjaxCart\Plugin\Minicart;

class Image
{
	public function aroundGetItemData($subject, $proceed, $item)
	{
		$result = $proceed($item);
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$product = $objectManager->get('\Magento\Catalog\Model\ProductRepository')->get($item->getSku());
		$_imagehelper = $objectManager->create('Magento\Catalog\Helper\Image');
		$image = 'product_thumbnail_image';
		$urlThumbnailImage = $_imagehelper->init($product, $image)->getUrl();
		$result['product_image']['src'] =  $urlThumbnailImage;		
		return $result;
	}
}