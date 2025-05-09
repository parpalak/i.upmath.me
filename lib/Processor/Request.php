<?php
/**
 * @copyright 2020-2025 Roman Parpalak
 * @license   https://opensource.org/license/mit MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

namespace S2\Tex\Processor;

class Request
{
	public const SVG = 'svg';
	public const PNG = 'png';

	protected string $extension;
	protected string $formula;

	public function __construct(string $formula, string $extension)
	{
		if (!self::extensionIsValid($extension)) {
			throw new \InvalidArgumentException('Incorrect output format has been requested. Expected SVG or PNG.');
		}

		$this->formula   = $formula;
		$this->extension = $extension;
	}

	/**
	 * @throws \RuntimeException
	 */
	public static function createFromUri(string $uri): self
	{
		$parts = explode('/', $uri, 3);
		if (\count($parts) < 3) {
			throw new \RuntimeException('Incorrect input format.');
		}

		$extension = $parts[1];
		if ($extension === 'svgb' || $extension === 'pngb') {
			$extension = substr($extension, 0, -1);
			$formula   = self::decodeCompressedFormula($parts[2]);
		} else {
			$formula = rawurldecode($parts[2]);
		}
		$formula = trim($formula);

		return new static($formula, $extension);
	}

	public function getExtension(): string
	{
		return $this->extension;
	}

	public function getFormula(): string
	{
		return $this->formula;
	}

	public function isPng(): bool
	{
		return $this->extension === self::PNG;
	}

	public function isSvg(): bool
	{
		return $this->extension === self::SVG;
	}

	public function withExtension(string $extension): self
	{
		if (!self::extensionIsValid($extension)) {
			throw new \InvalidArgumentException(\sprintf('Unsupported extension "%s".', $extension));
		}
		$result            = clone $this;
		$result->extension = $extension;

		return $result;
	}

	private static function extensionIsValid(string $str): bool
	{
		return $str === self::SVG || $str === self::PNG;
	}

	/**
	 * @throws \RuntimeException
	 */
	public static function decodeCompressedFormula(string $compressed): string
	{
		$base64     = strtr($compressed, '-_', '+/'); // URL-safe base64 to standard
		$compressed = base64_decode($base64);

		$result = @gzinflate($compressed);
		if ($result === false) {
			throw new \RuntimeException('Failed to decompress formula.');
		}
		return $result;
	}
}
