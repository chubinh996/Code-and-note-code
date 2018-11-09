<?php
/*
 *
 */
namespace FishPig\WordPress_PluginShortcodeWidget\Model\Shortcode;

/* Misc */
use FishPig\WordPress_PluginShortcodeWidget\Helper\Core as CoreHelper;

class oEmbedShortcode
{
	/*
	 *
	 *
	 */
	public function __construct(CoreHelper $coreHelper)
	{
		$this->coreHelper = $coreHelper;
	}

	/*
	 *
	 *
	 */
	public function renderShortcode($input, array $args = [])
	{
		return $this->coreHelper->simulatedCallback(function($input) {
			$wpEmbed = new \WP_Embed();
			
			return $wpEmbed->autoembed($input);
		}, array($input));
	}
}
