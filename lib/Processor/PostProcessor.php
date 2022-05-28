<?php
/**
 * @copyright 2020-2022 Roman Parpalak
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

namespace S2\Tex\Processor;

use S2\Tex\Cache\CacheProvider;
use S2\Tex\Helper;

class PostProcessor
{
	private CacheProvider $cacheProvider;

	public function __construct(CacheProvider $cacheProvider)
	{
		$this->cacheProvider = $cacheProvider;
	}

	public function cacheResponseAndGetAsyncRequest(Response $response, string $errorPayload): ?Request
	{
		$cacheName = $this->cacheProvider->cachePathFromRequest(
			$response->getRequest(),
			$response->hasError()
		);

		$content = $response->getContent();
		if ($response->hasError()) {
			$content = $errorPayload . ' ' . $response->getRequest()->getExtension() . ': ' . $content;
		}

		Helper::filePut($cacheName, $content);

		if (!$response->hasError()) {
			if ($response->isSvg()) {
				// Optimize SVG in background, skip PNG optimization
				return $response->getRequest();
			}
		}

		return null;
	}
}
