<?php
/**
* @category Rycoweb
* @package Rycoweb_ImageGallery
*/ 

class Rycoweb_ImageGallery_Model_Uploader {

    protected $fileUploaded = false;

    /*
     * checking directory if available
     */

    protected function _initFilesystem() {
        $this->_createWriteableDir($this->getTargetDir());
        $this->_createWriteableDir($this->getQuoteTargetDir());

        // Directory listing and hotlink secure
        $io = new Varien_Io_File();
        $io->cd($this->getTargetDir());
        if (!$io->fileExists($this->getTargetDir() . DS . '.htaccess')) {
            $io->streamOpen($this->getTargetDir() . DS . '.htaccess');
            $io->streamLock(true);
            $io->streamWrite("Order deny,allow\nAllow from all");
            $io->streamUnlock();
            $io->streamClose();
        }
    }

    /**
     * Create Writeable directory if it doesn't exist
     *
     * @param string Absolute directory path
     * @return void
     */
    protected function _createWriteableDir($path) {
        $io = new Varien_Io_File();
        if (!$io->isWriteable($path) && !$io->mkdir($path, 0777, true)) {
            Mage::throwException(Mage::helper('catalog')->__("Cannot create writeable directory '%s'.", $path));
        }
    }

    public function getTargetDir($relative = false) {
        $fullPath = Mage::getBaseDir('media') . DS . 'imagegallery';
        return $relative ? str_replace(Mage::getBaseDir(), '', $fullPath) : $fullPath;
    }

    protected function _getUploadMaxFilesize() {
        return min($this->_getBytesIniValue('upload_max_filesize'), $this->_getBytesIniValue('post_max_size'));
    }

    /**
     * Return php.ini setting value in bytes
     *
     * @param string $ini_key php.ini Var name
     * @return int Setting value
     */
    protected function _getBytesIniValue($ini_key) {
        $_bytes = @ini_get($ini_key);

        // kilobytes
        if (stristr($_bytes, 'k')) {
            $_bytes = intval($_bytes) * 1024;
            // megabytes
        } elseif (stristr($_bytes, 'm')) {
            $_bytes = intval($_bytes) * 1024 * 1024;
            // gigabytes
        } elseif (stristr($_bytes, 'g')) {
            $_bytes = intval($_bytes) * 1024 * 1024 * 1024;
        }
        return (int) $_bytes;
    }

    /**
     * Simple converrt bytes to Megabytes
     *
     * @param int $bytes
     * @return int
     */
    protected function _bytesToMbytes($bytes) {
        return round($bytes / (1024 * 1024));
    }

    public function getQuoteTargetDir($relative = false) {
        return $this->getTargetDir($relative) . DS . 'quote';
    }

    public function saveImages($file) {
        $upload = new Zend_File_Transfer_Adapter_Http();

        try {
            $this->_initFilesystem();

            $fileInfo = $upload->getFileInfo($file);
            $fileInfo = $fileInfo[$file];


            if ($fileInfo['error'] == 0) {


                $extension = pathinfo(strtolower($fileInfo['name']), PATHINFO_EXTENSION);
                $fileName = Mage_Core_Model_File_Uploader::getCorrectFileName($fileInfo['name']);

                $dispersion = Mage_Core_Model_File_Uploader::getDispretionPath($fileName);
                $filePath = $dispersion;
                $fileHash = md5(file_get_contents($fileInfo['tmp_name']));
                $targetFile = $fileHash . '.' . $extension;
                $fileFullPath = $this->getQuoteTargetDir() . $filePath;


                /* Starting upload */
                $uploader = new Varien_File_Uploader($file);

                // Any extention would work
                $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                $uploader->setAllowRenameFiles(false);

                $uploader->setFilesDispersion(false);
                $uploader->save($fileFullPath, $targetFile);

                $fileFullPath_a = explode('media'.DS, $fileFullPath . DS . $targetFile);
				if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') { //assuming mostly \ on windows. 
					$fileFullPath_a[1] = str_replace(DS,"/",$fileFullPath_a[1]);
				}
                $this->fileUploaded = $fileFullPath_a[1];
            }
        } catch (Exception $e) {

            Mage::throwException(Mage::helper('catalog')->__('Please specify the product option(s) value image'));
        }
        return $this;
    }

    public function getImageName() {
        return $this->fileUploaded;
    }

}
