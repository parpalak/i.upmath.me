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
	protected $useBaseline;

	public function __construct(string $text, bool $useBaseline)
	{
		$this->text        = $text;
		$this->useBaseline = $useBaseline;
	}

	public function getText(): string
	{
		return $this->text;
	}

	public function useBaseline(): bool
	{
		return $this->useBaseline;
	}
}
