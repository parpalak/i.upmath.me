<?php
/**
 * @copyright 2020 Roman Parpalak
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

namespace S2\Tex\Renderer;

class PngConverter
{
	private $svg2pngCommand;

	public function __construct(string $svg2pngCommand)
	{
		$this->svg2pngCommand = $svg2pngCommand;
	}

	public function convert(string $svgFileName): string
	{
		ob_start();
		passthru(sprintf($this->svg2pngCommand, $svgFileName));

		return ob_get_clean();
	}
}
