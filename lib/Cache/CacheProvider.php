<?php
/**
 * @copyright 2020 Roman Parpalak
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

namespace S2\Tex\Cache;

use S2\Tex\Processor\Request;

class CacheProvider
{
	protected $cacheFailDir;
	protected $cacheSuccessDir;

	public function __construct(string $cacheSuccessDir, string $cacheFailDir)
	{
		$this->cacheFailDir    = $cacheFailDir;
		$this->cacheSuccessDir = $cacheSuccessDir;
	}

	public function getCacheState(Request $request): CacheState
	{
		$cacheName = $this->cachePathFromRequest($request, false);

		return new CacheState($cacheName, file_exists($cacheName));
	}

	/**
	 * Returns the cached path.
	 * This algorithm should be used by a web-server to process the cache files as a static content.
	 *
	 * @param Request $request
	 * @param bool    $hasError
	 *
	 * @return string
	 */
	public function cachePathFromRequest(Request $request, bool $hasError): string
	{
		$hash      = md5($request->getFormula());
		$prefixDir = $hasError ? $this->cacheFailDir : $this->cacheSuccessDir;

		return $prefixDir . substr($hash, 0, 2) .
			'/' . substr($hash, 2, 2) .
			'/' . substr($hash, 4) .
			'.' . $request->getExtension();
	}
}
