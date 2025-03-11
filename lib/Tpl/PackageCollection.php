<?php
/**
 * @copyright 2025 Roman Parpalak
 * @license   https://opensource.org/license/mit MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

declare(strict_types=1);

namespace S2\Tex\Tpl;

class PackageCollection
{
	private array $packages = [];

	public function add(string $env, PackageInterface $package): void
	{
		$this->packages[$env] = $package;
	}

	public function getCode(): string
	{
		$result  = '';
		$result2 = '';
		foreach ($this->packages as $package) {
			if ($package instanceof PreambleEntry) {
				$result2 .= $package->getCode() . "\n";
			} else {
				$result .= $package->getCode() . "\n";
			}
		}

		return $result . $result2;
	}
}
