<?php
/**
 * NOTICE OF LICENSE
 * You may not sell, distribute, sub-license, rent, lease or lend complete or portion of software to anyone.
 *
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future.
 *
 * @package   ProductReview
 * @copyright Copyright (c) 2018
 * @contacts  support
 * @license  See the LICENSE.md file in module root directory
 */

namespace RDSC\CustomProductReview\Observer;

/**
 * Class AdminProductReviewSaveAfter
 *
 * @package RDSC\CustomProductReview\Observer
 * @author Umar Chaudhry
 */
class AdminProductReviewSaveAfter implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \RDSC\CustomProductReview\Model\ReviewMediaFactory
     */
    protected $_reviewMediaFactory;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
     */
    protected $_mediaDirectory;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $_fileHandler;

    /**
     * AdminProductReviewSaveAfter constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filesystem\Driver\File $fileHandler
     * @param \RDSC\CustomProductReview\Model\ReviewMediaFactory $reviewMediaFactory
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Driver\File $fileHandler,
        \RDSC\CustomProductReview\Model\ReviewMediaFactory $reviewMediaFactory
    )
    {
        $this->_request = $request;
        $this->_fileHandler = $fileHandler;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_reviewMediaFactory = $reviewMediaFactory;
    }


    /**
     * function
     * executes after review is saved
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $target = $this->_mediaDirectory->getAbsolutePath('review_images');
        $deletedMediaString = $this->_request->getParam('deleted_media');

        if ($deletedMediaString)
            try {
                $ids = explode(",", trim($deletedMediaString, ","));
                foreach ($ids as $id) {
                    $reviewMedia = $this->_reviewMediaFactory->create()->load($id);
                    $path = $target . $reviewMedia->getMediaUrl();
                    if ($this->_fileHandler->isExists($path)) {
                        $this->_fileHandler->deleteFile($path);
                    }
                    $reviewMedia->delete();
                }
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while updating review attachment(s).'));
            }
    }
}