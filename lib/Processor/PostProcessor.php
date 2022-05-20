<?php
/**
 * @copyright 2020-2022 Roman Parpalak
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

namespace S2\Tex\Processor;


use S2\Tex\Cache\CacheProvider;

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

		self::filePut($cacheName, $content);

		if (!$response->hasError()) {
			if ($response->isSvg()) {
				// Optimize SVG in background, skip PNG optimization
				return $response->getRequest();
			}
		}

		return null;
	}

	/**
	 * Wrapper for file_put_contents()
	 *
	 * 1. Creates parent directories if they do not exist.
	 * 2. Uses atomic rename operation to avoid using partial content and race conditions.
	 *
	 * @param string $filename
	 * @param string $content
	 */
	private static function filePut(string $filename, string $content): void
	{
		$dir = dirname($filename);
		if (!file_exists($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
			throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
		}

		$tmpFilename = $filename . '.temp';

		file_put_contents($tmpFilename, $content);

		if (!@rename($tmpFilename, $filename)) {
			@unlink($filename);
			@rename($tmpFilename, $filename);
		}
	}
}
