<?php
/**
 * @copyright 2020-2022 Roman Parpalak
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

namespace S2\Tex\Renderer;

use S2\Tex\Helper;

class PngConverter
{
	private string $svg2pngCommand;

	public function __construct(string $svg2pngCommand)
	{
		$this->svg2pngCommand = $svg2pngCommand;
	}

	public function convert(string $svgFileName): string
	{
		$command = sprintf($this->svg2pngCommand, $svgFileName);

		ob_start();
		Helper::newRelicProfileDataStore(
			static fn () => passthru($command),
			'shell',
			Helper::getShortCommandName($command)
		);

		return ob_get_clean();
	}
}
