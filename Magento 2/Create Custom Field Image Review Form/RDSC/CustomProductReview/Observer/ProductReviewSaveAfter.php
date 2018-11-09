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
 * Class ProductReviewSaveAfter
 *
 * @package RDSC\CustomProductReview\Observer
 * @author Umar Chaudhry
 */
class ProductReviewSaveAfter implements \Magento\Framework\Event\ObserverInterface
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
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_fileUploaderFactory;

    protected $messageManager;

    /**
     * ProductReviewSaveAfter constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param \RDSC\CustomProductReview\Model\ReviewMediaFactory $reviewMediaFactory
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \RDSC\CustomProductReview\Model\ReviewMediaFactory $reviewMediaFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager
    )
    {
        $this->_request = $request;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_reviewMediaFactory = $reviewMediaFactory;
        $this->messageManager = $messageManager;
    }

    /**
     * function
     * executed after a product review is saved
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $reviewId = $observer->getEvent()->getObject()->getReviewId();
        $media = $this->_request->getFiles('review_media');
        $target = $this->_mediaDirectory->getAbsolutePath('review_images');

        if ($media) {
            try {
                for ($i = 0; $i < count($media); $i++) {
                    $uploader = $this->_fileUploaderFactory->create(['fileId' => 'review_media[' . $i . ']']);
                    $uploader->setAllowedExtensions(['jpg', 'gif', 'png']);
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);
                    $uploader->setAllowCreateFolders(true);

                    $result = $uploader->save($target);

                    $reviewMedia = $this->_reviewMediaFactory->create();
                    $reviewMedia->setMediaUrl($result['file']);
                    $reviewMedia->setReviewId($reviewId);
                    $reviewMedia->save();
                    $this->messageManager->addSuccess(__("Coupon code : Photo-Review"));
                }
            } catch (\Exception $e) {
                if ($e->getCode() == 0) {
                    $this->messageManager->addError("Something went wrong while saving review attachment(s).");
                }
            }
        }
    }
}