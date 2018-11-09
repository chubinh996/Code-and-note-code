<?php

namespace AHT\Custom\Block;

class BestSelling extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface {

    protected $_template = 'widget/bestselling_product.phtml';

    const DEFAULT_PRODUCTS_COUNT = 12;
    const DEFAULT_IMAGE_WIDTH = 300;
    const DEFAULT_IMAGE_HEIGHT = 150;

    protected $_productsCount;
    protected $httpContext;
    protected $_resourceFactory;
    protected $_catalogProductVisibility;
    protected $_productCollectionFactory;
    protected $_imageHelper;
    protected $_cartHelper;

    public function __construct(
    \Magento\Catalog\Block\Product\Context $context, \Magento\Reports\Model\ResourceModel\Report\Collection\Factory $resourceFactory, \Magento\Reports\Model\Grouped\CollectionFactory $collectionFactory, \Magento\Reports\Helper\Data $reportsData, array $data = []
    ) {
        $this->_resourceFactory = $resourceFactory;
        $this->_collectionFactory = $collectionFactory;
        $this->_reportsData = $reportsData;
        $this->_imageHelper = $context->getImageHelper();
        $this->_cartHelper = $context->getCartHelper();
        parent::__construct($context, $data);
    }

    public function imageHelperObj() {
        return $this->_imageHelper;
    }

    public function getBestsellerProduct() {
        $limit = $this->getProductLimit();
        $resourceCollection = $this->_resourceFactory->create('Magento\Sales\Model\ResourceModel\Report\Bestsellers\Collection');
        $resourceCollection->setPageSize($limit);
        return $resourceCollection;
    }

    public function getProductLimit() {
        if ($this->getData('product_count') == '') {
            return self::DEFAULT_PRODUCTS_COUNT;
        }
        return $this->getData('product_count');
    }

    public function getProductImageWidth() {
        if ($this->getData('image_width') == '') {
            return self::DEFAULT_IMAGE_WIDTH;
        }
        return $this->getData('image_width');
    }

    public function getProductImageHeight() {
        if ($this->getData('image_height') == '') {
            return self::DEFAULT_IMAGE_HEIGHT;
        }
        return $this->getData('image_height');
    }

    public function getAddToCartUrl($product, $additional = []) {
        return $this->_cartHelper->getAddUrl($product, $additional);
    }

    public function getProductPriceHtml(
    \Magento\Catalog\Model\Product $product, $priceType = null, $renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST, array $arguments = []
    ) {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }
        $arguments['zone'] = isset($arguments['zone']) ? $arguments['zone'] : $renderZone;
        $arguments['price_id'] = isset($arguments['price_id']) ? $arguments['price_id'] : 'old-price-' . $product->getId() . '-' . $priceType;
        $arguments['include_container'] = isset($arguments['include_container']) ? $arguments['include_container'] : true;
        $arguments['display_minimal_price'] = isset($arguments['display_minimal_price']) ? $arguments['display_minimal_price'] : true;
        /** @var \Magento\Framework\Pricing\Render $priceRender */
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');
        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                    \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE, $product, $arguments
            );
        }
        return $price;
    }

}
