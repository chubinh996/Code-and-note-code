<?php
/**
* @category Rycoweb
* @package Rycoweb_ImageGallery
*/ 

class Rycoweb_Imagegallery_Model_System_Config_Source_Galleryeffectoptions
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'slide', 'label'=>Mage::helper('adminhtml')->__('Auto Slide')),
            array('value' => 'fade', 'label'=>Mage::helper('adminhtml')->__('Fadding/Faddout')),
        );
    }


}