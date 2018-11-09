if (version_compare($context->getVersion(), "1.0.0", "<")) {
		}
		if (version_compare($context->getVersion(), '1.0.2', '<')) {
			$installer->getConnection()->addColumn(
				$installer->getTable('review_detail'),
				'image_review',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,
					'comment' => 'Image'
				]
			);
		}