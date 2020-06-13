<?php
/**
 * @copyright 2020 Roman Parpalak
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

namespace S2\Tex\Renderer;

class SvgHelper
{
	private const SVG_PRECISION = 5;

	public static function processSvgContent(string $svg, bool $hasBaseline): string
	{
		// $svg = '...<!--start 19.8752 31.3399 -->...';

		//                                    x        y
		$hasStart = preg_match('#<!--start (-?[\d.]+) (-?[\d.]+) -->#', $svg, $matchStart);
		//                                  x        y        w        h
		$hasBbox = preg_match('#<!--bbox (-?[\d.]+) (-?[\d.]+) (-?[\d.]+) (-?[\d.]+) -->#', $svg, $matchBbox);

		if ($hasStart && $hasBbox) {
			// SVG contains info about image size and baseline position.
			[, , $rawY, $rawWidth, $rawHeight] = $matchBbox;

			$rawStartY = $matchStart[2];

			// Typically $rawY < $rawStartY
			$rawDepth = $hasBaseline ? max(min(0, $rawY - $rawStartY) + $rawHeight, 0) : $rawHeight * 0.5;

			// Taking into account OUTER_SCALE since coordinates are in the internal scale.
			$depth  = round(OUTER_SCALE * $rawDepth, self::SVG_PRECISION);
			$height = round(OUTER_SCALE * $rawHeight, self::SVG_PRECISION);
			$width  = round(OUTER_SCALE * $rawWidth, self::SVG_PRECISION);

			// Embedding script providing that info to the parent.
			$script = '<script type="text/ecmascript">if(window.parent.postMessage)window.parent.postMessage("' . $depth . '|' . $width . '|' . $height . '|"+window.location,"*");</script>' . "\n";
			$svg    = str_replace('</svg>', $script . '</svg>', $svg);
		}

		return $svg;
	}
}
