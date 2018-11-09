<?php
/**
* @category Rycoweb
* @package Rycoweb_ImageGallery
*/ 

class Rycoweb_Imagegallery_Block_Adminhtml_Imagegallery extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'adminhtml_imagegallery';
        $this->_blockGroup = 'imagegallery';
        $this->_headerText = Mage::helper('imagegallery')->__('Gallery Manager');
        $this->_addButtonLabel = Mage::helper('imagegallery')->__('Add Gallery');
        parent::__construct();
    }

}

