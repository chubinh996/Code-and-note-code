<?php
/**
* @category Rycoweb
* @package Rycoweb_ImageGallery
*/ 

class Rycoweb_ImageGallery_Block_Adminhtml_Imagegallery_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('imagegallery_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('imagegallery')->__('Gallery Information'));
    }

    protected function _beforeToHtml() {
        $this->addTab('form_section', array(
            'label' => Mage::helper('imagegallery')->__('Gallery Information'),
            'title' => Mage::helper('imagegallery')->__('Gallery Information'),
            'content' => $this->getLayout()->createBlock('imagegallery/adminhtml_imagegallery_edit_tab_form')->toHtml()
        ));

        return parent::_beforeToHtml();
    }

}

