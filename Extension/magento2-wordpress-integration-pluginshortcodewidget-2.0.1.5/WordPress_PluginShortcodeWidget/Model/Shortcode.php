<?php
/*
 *
 *
 */
namespace FishPig\WordPress_PluginShortcodeWidget\Model;

/* Parent Class */
use FishPig\WordPress_PluginShortcodeWidget\Model\Shortcode\AbstractShortcode;

/* Constructor Args */
use FishPig\WordPress_PluginShortcodeWidget\Helper\Core as CoreHelper;
use FishPig\WordPress\Model\Plugin;
use Magento\Framework\Module\Manager;
use FishPig\WordPress_PluginShortcodeWidget\Helper\Data as DataHelper;

class Shortcode extends AbstractShortcode
{
	/*
	 *
	 *
	 */
	public function __construct(CoreHelper $coreHelper, Plugin $plugin, Manager $moduleManager, DataHelper $dataHelper)
	{
		$this->dataHelper = $dataHelper;
		
		parent::__construct($coreHelper, $plugin, $moduleManager);
	}
	
	/*
	 *
	 *
	 */
	public function renderShortcode($input, array $args = [])
	{
		$input = $this->coreHelper->doShortcode($this->coreHelper->doShortcode($input));

		$this->inlineJs[] = $this->extractInlineJs($input);

		/*
		 * Gravity Forms
		 */		 
		/*
		if (strpos($input, 'gform_wrapper') !== false) {
			if (strpos($input, 'gform_ajax_frame_') !== false) {
				if (preg_match('/<form[^>]{1,}gform[^>]{1,}(action=(["\']{1})[^\#\'"]{1,}(#.*)\\2)/U', $input, $formId)) {
					$input = str_replace($formId[1], 'action="' . Mage::helper('wordpress')->getBaseUrl() . $formId[3] . '"', $input);
				}
			}
		}
		*/

		return $input;
	}
	
	/*
	 * @return array
	 */
	public function getRequiredAssets($responseHtml)
	{
		global $wp_styles, $wp_scripts;

		$assets = array(
			'head' => array(),
			'inline' => $this->inlineJs,
			'footer' => array(),
			'queued' => array(),
		);

		/*
		 * wp_head()
		 */
		if ($wpHead = $this->_getWpHeadOutput()) {
			if (preg_match_all('/<script[^>]{0,}>.*<\/script>/Us', $wpHead, $matches)) {
				foreach($matches[0] as $key => $match) {
					$assets['head']['script_wp_head_' . $key] = $match;
				}
			}
			
			if (preg_match_all('/<style[^>]{0,}>.*<\/style>/Us', $wpHead, $matches)) {
				foreach($matches[0] as $key => $match) {
					$assets['head']['style_wp_head_' . $key] = $match;
				}
			}
		}

		/*
		 * wp_footer()
		 */
		if ($wpFooter = $this->_getWpFooterOutput()) {
			if (preg_match_all('/<script[^>]{0,}>.*<\/script>/Us', $wpFooter, $matches)) {
				foreach($matches[0] as $key => $match) {
					$assets['footer']['script_wp_footer_' . $key] = $match;
				}
			}
			
			if (preg_match_all('/<style[^>]{0,}>.*<\/style>/Us', $wpFooter, $matches)) {
				foreach($matches[0] as $key => $match) {
					$assets['footer']['style_wp_footer_' . $key] = $match;
				}
			}
		}

		/*
		 * Queued Scripts
		 */
		if (isset($wp_scripts) && $wp_scripts->queue) {
			$wp_scripts->do_concat = false;

			foreach($wp_scripts->queue as $item) {
				if (in_array($item, $wp_scripts->done)) {
					continue;
				}

				// Sometimes do_item echo's so this catches that too
				ob_start();
				
				$wp_scripts->do_item($item);
				
				$extra = ob_get_clean();
	
				// We have buffered content
				if ($extra) {
					$assets['queued']['script_' . $item . '_ob'] = $extra;
				}
				
				// We have none buffered (ie. returned) content
				if ($wp_scripts->print_html) {
					$assets['queued']['script_' . $item] = $wp_scripts->print_html;
				}
			}
		}

		/*
		 * Queued Styles
		 */
		if (isset($wp_styles) && $wp_styles->queue) {
			$wp_styles->do_concat = false;
			
			foreach($wp_styles->queue as $item) {
				// Catch any echo'd content
				ob_start();
				
				$wp_styles->do_item($item);
				
				$extra = ob_get_clean();
	
				// We have buffered content
				if ($extra) {
					$assets['queued']['style_' . $item . '_ob'] = $extra;
				}
				
				// We have non-buffered content
				if ($wp_styles->print_html) {
					$assets['queued']['style_' . $item] = $wp_styles->print_html;
				}
			}
		}

		// Body Classes
		$bodyClasses = [];
		
		if (preg_match('/<body[^>]class="([^"]+)"/', $this->coreHelper->getHtml(), $matches)) {
			$bodyClasses = explode(' ', trim(preg_replace('/[ ]{2,}/', ' ', $matches[1])));
		}
		
		/* WPBakery Page Builder */
		$vcActive = $this->coreHelper->simulatedCallback(function(){
			return function_exists('is_plugin_active') ? is_plugin_active('js_composer/js_composer.php') : false;
		});
			
		if ($vcActive) {
			$this->bodyClasses[] = 'wpb-js-composer';
		}

		if ($bodyClasses) {			
			array_unshift(
				$assets['head'], 
				'<script type="text/javascript">document.body.className+=\' ' . implode(' ', array_unique($bodyClasses)) . '\';</script>'
			);
		}

		$assets = $this->cleanAssetArray($assets);

		// VC Frontend Editor
		if ($this->dataHelper->isVisualEditorMode()) {
			return $assets;
		}
		
		/* Strip out jQuery & Underscore if already available */
		foreach($assets as $key => $value) {
			if (strpos($responseHtml, 'jquery') !== false) {
				if (strpos($value, 'jquery/jquery.js') !== false) {
					unset($assets[$key]);
				}
			}

			if (strpos($responseHtml, 'underscore') !== false) {			
				if (strpos($value, 'js/underscore.min.js') !== false) {
					unset($assets[$key]);
				}
			}
		}

		// Elementor Fix
		foreach($assets as $k => $v) {
			if (strpos($v, 'var elementorFrontendConfig') !== false) {
				$assets[$k] = str_replace('var elementorFrontendConfig', 'elementorFrontendConfig', $v);
			}		
		}

		// Move config JSON JS blocks to the start of the assets array
		$prepend = [];
		
		foreach($assets as $key => $value) {
			$assets[$key] = $value = trim(str_replace(['/* <![CDATA[ */', '/* ]]> */'], '', $value));

			if (preg_match('/<script[^>]*>(.*)<\/script>/s', $value, $matches)) {
				if (preg_match('/^var [a-zA-Z0-9_]+[\s]*=[\s]*(\{.*\})[;]*$/', trim($matches[1]), $jsonMatch)) {
					if (@json_decode($jsonMatch[1], true)) {
						$value = '<script type="text/javascript">' . preg_replace('/^var /', '', trim($matches[1])) . '</script>';

						unset($assets[$key]);
						$prepend[] = $value;
					}
				}
			}
		}

		$assets = $prepend + $assets;

		// Fix var scoped variables
		foreach($assets as $key => $value) {
			if (strpos($value, 'var ') !== false) {
				$assets[$key] = trim(preg_replace("/\n[\s]*var /Usi", "\n", $value));
			}
		}

		$assets = $this->cleanAssetArray($assets);

		/* Divi */
		$diviActive = $this->coreHelper->simulatedCallback(function(){
			return function_exists('is_plugin_active') ? is_plugin_active('divi-builder/divi-builder.php') : false;
		});
		
		if ($diviActive) {
			$hasAnimationData = false;
			
			foreach($assets as $key => $value) {
				if (strpos($value, 'et_') !== false) {
					$assets[$key] = str_replace('var et_', 'et_', $value);
				}
				
				if (strpos($value, 'et_animation_data') !== false) {
					if (!$hasAnimationData) {
						$hasAnimationData = true;
					}
					else {
						unset($assets[$key]);
					}
				}
			}
		}

		// Clean duplicates
		$cleaned = [];
		
		foreach($assets as $key => $value) {
			$value = trim($value);
			
			if (!in_array($value, $cleaned)) {
				$cleaned[] = $value;
			}
		}

		return $cleaned;
	}
	
	/*
	 *
	 *
	 * @return string
	 */
	protected function _getWpHeadOutput()
	{
		$wpHead = $this->coreHelper->simulatedCallback(function() {
			ob_start();
			wp_head();
		
			return ob_get_clean();
		});
		
		if (preg_match('/(<head>.*<\/head>)/Us', $this->coreHelper->getHtml(), $match)) {
			$wpHead .= $match[1];
		}

		return $wpHead;
	}	

	/*
	 *
	 *
	 * @return string
	 */
	protected function _getWpFooterOutput()
	{
		$wpFooter = $this->coreHelper->simulatedCallback(function() {
			ob_start();
			wp_footer();
		
			return ob_get_clean();
		});

		if (preg_match('/<!--WP-FOOTER-->(.*)<!--\/WP-FOOTER-->/Us', $this->coreHelper->getHtml(), $match)) {
			$wpFooter .= $match[1];
		}
	
		return $wpFooter;
	}

	/*
	 * Determine whether the current request is a 404 request
	 *
	 * @return bool
	 */
	protected function _is404()
	{
		$html = $this->coreHelper->getHtml();
		
		return strpos($html, '404') !== false && strpos($html, 'not found') !== false;
	}
	
	/*
	 *
	 *
	 * @return bool
	 */
	public function requiresAssetInjection()
	{
		return true;
	}
	
	/*
	 * Is Visual Editor Mode
	 *
	 * @return bool
	 */
	public function isVisualEditorMode()
	{
		return $this->dataHelper->isVisualEditorMode();
	}
}
