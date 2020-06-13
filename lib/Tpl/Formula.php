<?php
/**
 * @copyright 2020 Roman Parpalak
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

namespace S2\Tex\Tpl;

class Formula
{
	protected $text;
	protected $hasBaseline;

	public function __construct(string $text, bool $hasBaseline)
	{
		$this->text        = $text;
		$this->hasBaseline = $hasBaseline;
	}

	public function getText(): string
	{
		return $this->text;
	}

	public function hasBaseline(): bool
	{
		return $this->hasBaseline;
	}
}
