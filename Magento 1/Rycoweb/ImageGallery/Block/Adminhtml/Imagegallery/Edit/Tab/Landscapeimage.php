<?php
/**
* @category Rycoweb
* @package Rycoweb_ImageGallery
*/ 

class Rycoweb_ImageGallery_Block_Adminhtml_Imagegallery_Edit_Tab_Landscapeimage extends Mage_Adminhtml_Block_Widget implements Varien_Data_Form_Element_Renderer_Interface {

    protected $_values;
    protected $_element = null;

    public function __construct() {
        $this->setTemplate('imagegallery/landscapeimage.phtml');
    }

    public function setElement(Varien_Data_Form_Element_Abstract $element) {
        $this->_element = $element;
        return $this;
    }

    public function getElement() {
        return $this->_element;
    }

    public function getValues() {
        $values = array();
        $data = $this->getElement()->getValue();
        return $values;
    }

    public function render(Varien_Data_Form_Element_Abstract $element) {
        $this->setElement($element);
        return $this->toHtml();
    }

}

