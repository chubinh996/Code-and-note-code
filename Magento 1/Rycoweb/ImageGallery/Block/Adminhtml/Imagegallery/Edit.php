<?php
/**
* @category Rycoweb
* @package Rycoweb_ImageGallery
*/ 

class Rycoweb_ImageGallery_Block_Adminhtml_Imagegallery_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'imagegallery';
        $this->_controller = 'adminhtml_imagegallery';

        $this->_updateButton('save', 'label', Mage::helper('imagegallery')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('imagegallery')->__('Delete Item'));

        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save'
                ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('imagegallery_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'imagegallery_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'imagegallery_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText() {
        if (Mage::registry('imagegallery_data') && Mage::registry('imagegallery_data')->getId()) {
            return Mage::helper('imagegallery')->__("Edit Image '%s'", $this->htmlEscape(Mage::registry('imagegallery_data')->getTitle()));
        } else {
            return Mage::helper('imagegallery')->__('Add Image');
        }
    }

}

