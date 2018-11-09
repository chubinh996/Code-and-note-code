<?php
/**
* @category Rycoweb
* @package Rycoweb_ImageGallery
*/ 

class Rycoweb_ImageGallery_Model_Mysql4_Imagegallery_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('imagegallery/imagegallery');
    }
 
}