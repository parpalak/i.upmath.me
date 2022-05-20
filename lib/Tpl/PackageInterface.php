<?php
/**
 * @copyright 2015-2022 Roman Parpalak
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

namespace S2\Tex\Tpl;

interface PackageInterface
{
	/**
	 * @return string LaTeX code for the package
	 */
	public function getCode(): string;
}
