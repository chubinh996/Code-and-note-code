<?php
/*
 *
 */
namespace FishPig\WordPress_PluginShortcodeWidget\Model\Shortcode;

/* Constructor Args */
use FishPig\WordPress_PluginShortcodeWidget\Helper\Core as CoreHelper;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\View\Layout;

/* Misc */
use FishPig\WordPress_PluginShortcodeWidget\Block\ProductShortcode as ProductShortcodeBlock;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;

class ProductShortcode
{
	/*
	 * @var ProductCollectionFactory
	 */
	protected $productCollectionFactory;
	
	/*
	 *
	 *
	 */
	public function __construct(CoreHelper $coreHelper, ProductCollectionFactory $productCollectionFactory, Layout $layout)
	{
		$this->coreHelper               = $coreHelper;
		$this->productCollectionFactory = $productCollectionFactory;
		$this->layout                   = $layout;
	}

	/*
	 *
	 *
	 */
	public function renderShortcode($input, array $args = [])
	{
		if (strpos($input, '[product ') === false) {
			return $input;
		}
		
		if (!preg_match_all('/\[product([^\]]+)\]/', $input, $matches)) {
			return $input;
		}

		foreach($matches[0] as $key => $shortcode) {
			if (!preg_match_all('/([a-z_-]+)="([^"]+)"/', $matches[1][$key], $argMatches)) {
				continue;
			}

			$args = array_combine($argMatches[1], $argMatches[2]);
			
			if (!empty($args['ids'])) {
				$args['ids'] = explode(',', preg_replace('/[^0-9,]+/', '', $args['ids']));
				
				if (count($args['ids']) === 1 && !$args['ids'][0]) {
					$args['ids'] = [];
				}
			}
			
			$products = $this->productCollectionFactory->create()
				->addAttributeToSelect(['name', 'small_image'])
				->addAttributeToFilter('status', ProductStatus::STATUS_ENABLED)
				->setVisibility([2, 4]);
			
			if (!empty($args['ids'])) {
				$products->addAttributeToFilter('entity_id', ['in' => $args['ids']]);
			}

			$productsHtml =  $this->layout->createBlock(ProductShortcodeBlock::CLASS)
				->setProductCollection($products)
				->setTemplate('FishPig_WordPress_PluginShortcodeWidget::shortcode/product.phtml')
				->toHtml();
				
			$input = str_replace($shortcode, $productsHtml, $input);
		}

		return $input;
	}
}
