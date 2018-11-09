<?php

class Rycoweb_ImageGallery_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function galleryAction() {	

        $_gallImgColl = Mage::helper('imagegallery')->getGalleryCollection();

        $_mediaBaseUrl = rtrim(Mage::getBaseUrl('media'), '/') . '/';
        $_gallimgArr = array();
        $_loadimgcount = 0;        
        $_gallImgSrc = '';
        $_gallImgTitle = '';
        $_gallImgLink = '';
        foreach ($_gallImgColl as $_gallImage) {
            $_loadimgcount++;
            $_gallImgSrc = $_mediaBaseUrl . $_gallImage->getLandscapeimage();
            $_gallPortraitImgSrc = $_mediaBaseUrl . $_gallImage->getPortraitimage();
            $_gallImgTitle = $_gallImage->getTitle();
            $_gallImgLink = $_gallImage->getWeblink();          

            $_gallimgArr[] = array('img' => $_gallImgSrc,'imgPortrait'=>$_gallPortraitImgSrc, 'title' => $_gallImgTitle, 'link' => $_gallImgLink);
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($_gallimgArr));
    }

}

