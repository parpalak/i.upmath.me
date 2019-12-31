<?php
/**
 * @copyright 2016 Roman Parpalak
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

namespace S2\Tex\Tpl;

/**
 * Class Formula
 */
class Formula
{
	/**
	 * @var string
	 */
	protected $text = '';

	/**
	 * @var bool
	 */
	protected $hasBaseline = true;

	/**
	 * Formula constructor.
	 *
	 * @param string $text
	 * @param bool   $hasBaseline
	 */
	public function __construct($text, $hasBaseline)
	{
		$this->text        = $text;
		$this->hasBaseline = $hasBaseline;
	}

	/**
	 * @return string
	 */
	public function getText()
	{
		return $this->text;
	}

	/**
	 * @return boolean
	 */
	public function hasBaseline()
	{
		return $this->hasBaseline;
	}
}
