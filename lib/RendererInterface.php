<?php
/**
 * Interface for latex renderer.
 *
 * @copyright 2015 Roman Parpalak
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

namespace S2\Tex;

interface RendererInterface
{
	/**
	 * Converts a latex formula into pictures.
	 *
	 * @param string $formula in latex
	 * @return null
	 */
	public function run ($formula);

	/**
	 * @return string
	 */
	public function getSVG ();

	/**
	 * @return string
	 */
	public function getPNG ();
}
