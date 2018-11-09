<?php
/*
 *
 *
 *
 */
namespace FishPig\WordPress_PluginShortcodeWidget\Model\Shortcode;

/* Constructor Args */
use FishPig\WordPress_PluginShortcodeWidget\Helper\Core as CoreHelper;
use FishPig\WordPress\Model\Plugin;
use Magento\Framework\Module\Manager;

abstract class AbstractShortcode
{
	/*
	 * @var CoreHelper
	 */
	protected $coreHelper;
	
	/*
	 * @var Plugin
	 */
	protected $plugin;
	
	/*
	 * @var Manager
	 */
	protected $moduleManager;

	/*
	 * @var array
	 */
	protected $inlineJs = [];
	
	/*
	 *
	 * @param CoreHelper $coreHelper
	 *
	 */
	public function __construct(CoreHelper $coreHelper, Plugin $plugin, Manager $moduleManager)
	{
		$this->coreHelper      = $coreHelper;	
		$this->plugin          = $plugin;
		$this->moduleManager   = $moduleManager;
	}
	
	/*
	 * Determine whether the plugin in WordPress is enabled and core is active
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return $this->isPluginEnabled() && $this->coreHelper->isActive();
	}

	/*
	 * Determine whether the plugin in WordPress is enabled
	 *
	 * @return bool
	 */
	protected function isPluginEnabled()
	{
		if (!isset($this->pluginName) || !$this->pluginName) {
			return true;
		}
		
		return $this->plugin->isEnabled($this->pluginName);
	}

	/*
	 *
	 *
	 * @return string
	 */
  protected function _getHtml()
  {
	  return $this->coreHelper->getHtml();
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
	 *
	 *
	 * @return array
	 */
	public function getInlineJs()
	{
		return $this->cleanAssetArray($this->inlineJs);
	}
	
	/*
	 * Extract any inline JS in $content
	 * and remove it from $content
	 *
	 * @param  string $content
	 * @return array
	 */
	public function extractInlineJs(&$content)
	{
		if (!preg_match_all('/(<script[^>]{0,}>)(.*)(<\/script>)/Us', $content, $matches)) {	
			return [];
		}
		
		$inline = [];
		
		foreach($matches[0] as $match) {
			$inline[] = $match;
			$content = str_replace($match, '', $content);
		}

		return $inline;
	}
	
	/*
	 * Clean the array of assets
	 *
	 * @param  array $assets
	 * @return array|false
	 */
	public function cleanAssetArray($assets)
	{
		if (!is_array($assets)) {
			return $assets;
		}

		$buffer = [];
		
		foreach($assets as $asset) {
			if (is_array($asset)) {
				foreach($this->cleanAssetArray($asset) as $line) {
					$buffer[] = $line;
				}
			}
			else if (trim($asset)) {
				$buffer[] = trim($asset);
			}
		}
		
		return $buffer;
	}

	/*
	 * A wrapper for preg_match
	 *
	 * @param string $pattern
	 * @param   string $haystack
	 * @param  null|int $return = null
	 * @return string|false
	 */
	protected function _match($pattern, $haystack, $return = null)
	{
		if (preg_match($pattern, $haystack, $matches)) {
			if (is_null($return)) {
				return $matches;
			}
			
			return isset($matches[$return]) ? $matches[$return] : false;
		}
		
		return false;
	}

	/*
	 * A wrapper for preg_match
	 *
	 * @param  string $pattern
	 * @param  string $haystack
	 * @param  null|int $return = null
	 * @return string|false
	 */
	protected function _matchAll($pattern, $haystack, $return = null)
	{
		if (preg_match_all($pattern, $haystack, $matches)) {
			if (is_null($return)) {
				return $matches;
			}
			
			return isset($matches[$return]) ? $matches[$return] : false;
		}
		
		return false;
	}
	
	/*
	 * Get all includes from the HTML that match the patterns
	 *
	 * @param  string $patterns
	 * @param  string $html
	 * @return false|array
	 */
	protected function _getIncludeHtmlFromString($patterns, $html = null)
	{
		if (is_null($html)) {
			$html = $this->_getHtml();
		}

		$includes = $this->_matchAll(
			'/<(script|link)[^>]+(href|src|id)=[\'"]{1}[^\'"]{0,}(' . str_replace('/', '\/', implode('|', $patterns)) . ')[^\'"]{1,}[\'"]{1}[^>]*>(<\/script>)*/i',
			$html
		);
		
		if ($includes) {
			foreach($includes[0] as $key => $include) {
				if ($includes[1][$key] === 'link') {
					if (!$this->_match('/rel=[\'"]{1}stylesheet[\'"]{1}/Ui', $include)) {
						unset($includes[0][$key]);
						continue;
					}
				}
				
				if (strpos($include, 'ie') !== false) {
					if ($match = $this->_match('/<!--\[if[ a-z]{0,}IE[ ]{0,}[0-9]+\]>' . preg_quote($include, '/') . '<!\[endif\]-->/sUi', $html, 0)) {
						$includes[0][$key] = $match;
					}
				}
			}
			
			return $includes[0];
		}
		
		return false;
	}

	/*
	 *
	 *
	 * @return bool
	 */
	protected function isPluginShortcodeWidgetEnabled()
	{
		return $this->moduleManager->isOutputEnabled('FishPig_WordPress_PluginShortcodeWidget');
	}
}
