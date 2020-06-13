<?php
/**
 * @copyright 2020 Roman Parpalak
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

namespace S2\Tex\Cache;

class CacheState
{
	private $cacheName;
	private $cacheExists;

	public function __construct(string $cacheName, bool $cacheExists)
	{
		$this->cacheName   = $cacheName;
		$this->cacheExists = $cacheExists;
	}

	public function getCacheName(): string
	{
		return $this->cacheName;
	}

	public function cacheExists(): bool
	{
		return $this->cacheExists;
	}
}
