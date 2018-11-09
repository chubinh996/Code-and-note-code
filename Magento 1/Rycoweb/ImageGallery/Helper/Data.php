<?php
/**
* @category Rycoweb
* @package Rycoweb_ImageGallery
*/ 

class Rycoweb_ImageGallery_Helper_Data extends Mage_Core_Helper_Abstract
{

    const XML_PATH_IS_ENABLE           =   'imagegallery/settings/is_enable_gallery';
    const XML_PATH_IS_SHOW_GALLERY_TITLE           =   'imagegallery/settings/is_show_gallery_title';   
    const XML_PATH_GALLERY_EFFECT           =   'imagegallery/imagegallery_template/gallery_effect';
    const XML_PATH_GALLERY_INTERVAL           =   'imagegallery/imagegallery_template/interval';
	
 public function timeInterval($storeId = null)
    {
       if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }
       return Mage::getStoreConfig(self::XML_PATH_GALLERY_INTERVAL, $storeId);
	   
    } 
public function canShowGallery($storeId = null)
    {
       if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }
       return Mage::getStoreConfig(self::XML_PATH_IS_ENABLE, $storeId);
    }
    
  public function canShowTitle($storeId = null)
    {
       if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }
       return Mage::getStoreConfig(self::XML_PATH_IS_SHOW_GALLERY_TITLE, $storeId);
    }
    
      public function getGalleryEffect($storeId = null)
    {
       if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }
       return Mage::getStoreConfig(self::XML_PATH_GALLERY_EFFECT, $storeId);
    }
	
	public function getGalleryCollection($storeId = null){

		if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }
		
		if (!Mage::registry('gallImgCollection')) {
            $_gallImgCollection = Mage::getSingleton('imagegallery/imagegallery')->getCollection()
                    ->addFieldToFilter('status', 1);
			
			Mage::register('gallImgCollection', $_gallImgCollection);
			return $_gallImgCollection;
        }else{		
			return Mage::registry('gallImgCollection');
		}
	
	}
}