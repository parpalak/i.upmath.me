<?php
/**
 * @copyright 2025 Roman Parpalak
 * @license   https://opensource.org/license/mit MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

declare(strict_types=1);

namespace S2\Tex\Tpl;

readonly class PreambleEntry implements PackageInterface
{
	public function __construct(private string $entry)
	{
	}

	public function getCode(): string
	{
		return $this->entry;
	}
}
