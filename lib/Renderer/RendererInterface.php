<?php
/**
 * Interface for latex renderer.
 *
 * @copyright 2015-2020 Roman Parpalak
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

namespace S2\Tex\Renderer;

interface RendererInterface
{
	/**
	 * Converts a latex formula into pictures.
	 *
	 * @param string $formula in latex
	 * @param string $type    of image
	 *
	 * @return string
	 */
	public function run(string $formula, string $type): string;
}
