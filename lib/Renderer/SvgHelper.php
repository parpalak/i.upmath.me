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
	public static function processSvgContent(string $svg, bool $useBaseline): string
	{
		// $svg = '...<!--start 19.8752 31.3399 -->...';

		//                               x          y
		$startPattern = '#<!--start (-?[\d.]+) (-?[\d.]+) -->#';
		if (!preg_match($startPattern, $svg, $matchBaseline)) {
			// SVG has no info about baseline position.
			return $svg;
		}

		//                                    x            y            w            h
		$viewBoxPattern = '#viewBox=["\'](-?[\d.]+)\s+(-?[\d.]+)\s+(-?[\d.]+)\s+(-?[\d.]+)["\']#';
		if (!preg_match($viewBoxPattern, $svg, $matchViewBox)) {
			// SVG has no info about image size.
			return $svg;
		}

		/**
		 * See details for viewport and user coordinates:
		 * https://www.sarasoueidan.com/blog/svg-coordinate-systems/
		 *
		 * Values of the viewBox argument (user* variables) are in user coordinates.
		 * The unit in user space is TeX point (1/72.27 inch).
		 */
		[, $userStartX, $userStartY, $userWidth, $userHeight] = $matchViewBox;

		if ($userWidth < 0.000001 || $userHeight < 0.000001) {
			// Almost empty image
			return $svg;
		}

		$userBaselineY = $matchBaseline[2];

		// Typically $userBaselineY > $userStartY
		$userFromTopToBaseline    = max(0, $userBaselineY - $userStartY);
		$userFromBottomToBaseline = $useBaseline
			? max($userHeight - $userFromTopToBaseline, 0)
			: $userHeight * 0.5;

		/**
		 * We need to convert user sizes to the viewport coordinates (svg.width and svg.height).
		 * 1. Convert from TeX point to usual point (1/72 inch) by multiplier
		 * 72.27/72 = 1.00375.
		 * 2. The project is set up to scale everything by 1.25. That's why
		 * OUTER_SCALE = 1.25 * 1.00375.
		 * 3. Convert from points (pt) to pixels (px) by multiplier 4/3.
		 */
		$multiplier = 4.0 / 3.0 * OUTER_SCALE;

		$viewportFromBottomToBaseline = $multiplier * $userFromBottomToBaseline;
		$viewportHeight               = $multiplier * $userHeight;
		$viewportWidth                = $multiplier * $userWidth;

		/**
		 * 4. Expand the viewport to the int pixel grid to avoid fractions.
		 * Otherwise there are some bugs in browsers leading wrong image scale or even cut-off.
		 */
		$extendedViewportHeight               = ceil($viewportHeight);
		$extendedViewportWidth                = ceil($viewportWidth);
		$extendedViewportFromBottomToBaseline = $viewportFromBottomToBaseline + $extendedViewportHeight - $viewportHeight;

		/**
		 * 5. Extend the viewBox in the same proportion as the viewport to avoid the image deformation.
		 */
		$extendedUserHeight = $userHeight * $extendedViewportHeight / $viewportHeight;
		$extendedUserWidth  = $userWidth * $extendedViewportWidth / $viewportWidth;

		$svg = preg_replace('#<svg.*?>#', sprintf(
			'<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="%s" height="%s" viewBox="%s %s %s %s">',
			round($extendedViewportWidth, 6),
			round($extendedViewportHeight, 6),
			$userStartX,
			$userStartY,
			round($extendedUserWidth, 6),
			round($extendedUserHeight, 6)
		), $svg);

		// Embed script providing size info to the parent.
		$script = sprintf(
			'<script type="text/ecmascript">if(window.parent.postMessage)window.parent.postMessage("%s|%s|%s|"+window.location,"*");</script>',
			round($extendedViewportFromBottomToBaseline * 0.75, 5),
			round($extendedViewportWidth * 0.75, 5), // back to pt due to backward compatibility reasons for old version of latex.js
			round($extendedViewportHeight * 0.75, 5)
		);

		$svg = str_replace('</svg>', $script . "\n" . '</svg>', $svg);

		return $svg;
	}
}
