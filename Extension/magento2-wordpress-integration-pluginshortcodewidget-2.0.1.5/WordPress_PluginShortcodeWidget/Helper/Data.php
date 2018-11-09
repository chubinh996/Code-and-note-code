<?php
/*
 *
 *
 *
 */
namespace FishPig\WordPress_PluginShortcodeWidget\Helper;

/* Parent Class */
use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
	/*
	 *
	 *
	 * @param  Post $post
	 * @return bool
	 */
	public function isVisualEditorMode()
	{
		$keys = array(
			'vc_editable',       // WPBakery Frontend Editor
			'elementor-preview', // Elementor
			'fl_builder',        // BeaverBuilder
			'et_fb',             // Divi
		);
		
		foreach($keys as $key) {
			if (isset($_GET[$key])) {
				return true;
			}
		}
		
		return false;
	}
}
