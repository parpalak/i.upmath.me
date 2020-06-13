<?php
/**
 * @copyright 2020 Roman Parpalak
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

namespace S2\Tex\Processor;


use S2\Tex\Cache\CacheProvider;

class PostProcessor
{
	private $svgCommands = [];
	private $pngCommands = [];
	private $cacheProvider;

	public function __construct(CacheProvider $cacheProvider)
	{
		$this->cacheProvider = $cacheProvider;
	}

	public function addSVGCommand(string $command): self
	{
		$this->svgCommands[] = $command;

		return $this;
	}

	public function addPNGCommand(string $command): self
	{
		$this->pngCommands[] = $command;

		return $this;
	}

	public function cacheResponse(Response $response): void
	{
		$cacheName = $this->cacheProvider->cachePathFromRequest(
			$response->getRequest(),
			$response->hasError()
		);

		$content = $response->getContent();
		if ($response->hasError()) {
			// TODO
			$content = $_SERVER['HTTP_REFERER'] . ' ' . $response->getRequest()->getExtension() . ': ' . $content;
		}

		self::filePut($cacheName, $content);

		if (!$response->hasError()) {
			if ($response->isSvg()) {
				// Optimizing SVG
				foreach ($this->svgCommands as $command) {
					shell_exec(sprintf($command, $cacheName));
				}
			}

			if ($response->isPng()) {
				// Optimizing PNG
				foreach ($this->pngCommands as $command) {
					shell_exec(sprintf($command, $cacheName));
				}
			}
		}
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
