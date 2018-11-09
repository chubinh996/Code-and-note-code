<?php

namespace RDSC\Custom\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Helper\DefaultCategory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Catalog\Setup\CategorySetupFactory;

class InstallData implements InstallDataInterface {

    private $categorySetupFactory;

    public function __construct(CategorySetupFactory $categorySetupFactory) {
        $this->categorySetupFactory = $categorySetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {

        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);

        $categorySetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY, 'thumbnail', [
            'type' => 'varchar',
            'label' => 'Thumbnail',
            'input' => 'image',
            'backend' => 'Magento\Catalog\Model\Category\Attribute\Backend\Image',
            'required' => false,
            'sort_order' => 5,
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'group' => 'Content',
            'is_used_in_grid' => false,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => false
                ]
        );
    }

}
