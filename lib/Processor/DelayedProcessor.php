<?php
/**
 * @copyright 2020-2025 Roman Parpalak
 * @license   https://opensource.org/license/mit MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

namespace S2\Tex\Processor;

use S2\Tex\Cache\CacheProvider;
use S2\Tex\Helper;

class DelayedProcessor
{
	private CacheProvider $cacheProvider;
	private string $httpSvgoUrl;

	public function __construct(CacheProvider $cacheProvider, string $httpSvgoUrl)
	{
		$this->cacheProvider = $cacheProvider;
		$this->httpSvgoUrl   = $httpSvgoUrl;
	}

	/**
	 * @throws \JsonException
	 * @throws \RuntimeException
	 */
	public function process(Request $request): void
	{
		if ($request->isSvg()) {
			$filePath = $this->cacheProvider->cachePathFromRequest($request, false);
			$this->optimizeSvgViaHttp($filePath);

			return;
		}

		if ($request->isPng()) {
			return;
		}

		throw new \InvalidArgumentException(\sprintf(
			'Unknown type "%s" for delayed processing. [%s]',
			$request->getExtension(),
			var_export($request, true)
		));
	}

	/**
	 * @throws \RuntimeException
	 */
	private function optimizeSvgViaHttp(string $filePath): bool
	{
		$unoptimizedSvg = file_get_contents($filePath);
		if ($unoptimizedSvg === false) {
			return false;
		}

		if ($this->shouldAvoidSVGO($unoptimizedSvg)) {
			$optimizedSvg = $unoptimizedSvg;
		} else {
			$context = stream_context_create(['http' => [
				'method'  => 'POST',
				'header'  => "Content-Type: image/svg+xml\r\n",
				'content' => $unoptimizedSvg,
				'timeout' => 5,
			]]);

			$optimizedSvg = Helper::newRelicProfileDataStore(
				fn() => file_get_contents($this->httpSvgoUrl, false, $context),
				'runtime',
				'http-svgo'
			);
			if ($optimizedSvg === false) {
				return false;
			}
		}

		Helper::filePut($filePath, $optimizedSvg, true);

		$gzEncodedSvg = Helper::newRelicProfileDataStore(
			static fn() => gzencode($optimizedSvg, 9),
			'runtime',
			'gzencode'
		);

		Helper::filePut($filePath . '.gz', $gzEncodedSvg);

		@unlink($filePath);

		return true;
	}

	/**
	 * Contains some logic to skip SVGO optimization if the file contains some known features
	 * that cause SVGO to produce incorrect results.
	 */
	public function shouldAvoidSVGO(string $svgContent): bool
	{
		return str_contains($svgContent, 'animateTransform');
	}
}
