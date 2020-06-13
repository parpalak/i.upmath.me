<?php
/**
 * Interface for latex doc templates processing.
 *
 * @copyright 2015 Roman Parpalak
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

namespace S2\Tex;

use S2\Tex\Tpl\Formula;

interface TemplaterInterface
{
	/**
	 * Inserts a latex formula into appropriate templates.
	 *
	 * @param string $formula in latex
	 *
	 * @return Formula
	 */
	public function run(string $formula): Formula;
}
