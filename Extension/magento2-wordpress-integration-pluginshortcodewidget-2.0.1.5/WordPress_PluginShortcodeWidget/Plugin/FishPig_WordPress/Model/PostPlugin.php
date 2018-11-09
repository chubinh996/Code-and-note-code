<?php
/*
 *
 *
 */
namespace FishPig\WordPress_PluginShortcodeWidget\Plugin\FishPig_WordPress\Model;

/* Constructor Args */
use FishPig\WordPress_PluginShortcodeWidget\Helper\Core as CoreHelper;
use FishPig\WordPress\Model\ShortcodeManager;
use FishPig\WordPress_PluginShortcodeWidget\Helper\Data as DataHelper;

/* Subject */
use FishPig\WordPress\Model\Post;

/* Misc */
use Closure;

class PostPlugin
{
	/*
	 *
	 *
	 */
	protected $coreHelper;
	
	/*
	 *
	 *
	 */
	protected $dataHelper;
	
	/*
	 *
	 *
	 */
	protected $shortcodeManager;

	/*
	 *
	 *
	 */
	protected $postCache = [];

	/*
	 *
	 *
	 *
	 */
	public function __construct(CoreHelper $coreHelper, ShortcodeManager $shortcodeManager, DataHelper $dataHelper)
	{
		$this->coreHelper       = $coreHelper;
		$this->shortcodeManager = $shortcodeManager;
		$this->dataHelper       = $dataHelper;
	}
	
	/*
	 *
	 *
	 *
	 */
	public function aroundGetContent(Post $post, Closure $callback)
	{
#		if (!$this->canReplacePostContent($post)) {
#			return $callback();
#		}

		$post->setAsGlobal();
		
		$content = $this->coreHelper->simulatedCallback(
			function($post) {
				ob_start();

				the_content();
				
				// WP Bakery Custom CSS for post
				if (is_plugin_active('js_composer/js_composer.php')) {
					if ($customCss = get_post_meta($post->getId(), '_wpb_shortcodes_custom_css', true)) {
						echo '<style type="text/css" data-type="vc_shortcodes-custom-css">' . strip_tags($customCss) . '</style>';
					}
				}

				return ob_get_clean();
			}, [$post]
		);
		
		if (!$content) {

			return $callback();
		}

		$content = str_replace(['″', '”'], '"', html_entity_decode($content));
			
		return $this->shortcodeManager->renderShortcode($content, $post);
	}

	/*
	 *
	 *
	 *
	 */
	public function aroundSetAsGlobal(Post $post, Closure $callback)
	{
		return $this->getWordPressPostObject($post);
	}
	
	/*
	 *
	 *
	 *
	 */
	protected function getWordPressPostObject(Post $post)
	{
		if (!isset($this->postCache[$post->getId()])) {
			$this->postCache[$post->getId()] = $this->coreHelper->simulatedCallback(
				function($post) {
					if ($post = get_post((int)$post->getId())) {
						setup_postdata($post);
						return $post;
					}
					
					return false;
				}, 
				[$post]
			);
		}

		return $this->postCache[$post->getId()];
	}
	
	/*
	 *
	 * @param  Post $post
	 * @return bool
	 */
	protected function canReplacePostContent(Post $post)
	{
		if ($this->dataHelper->isVisualEditorMode()) {
			return true;
		}
		
		if ($post->getMetaValue('_elementor_edit_mode') === 'builder') {
			return true;
		}

		if ($post->getMetaValue('_et_pb_use_builder') === 'on') {
			return true;
		}
		
		return false;
	}
}
