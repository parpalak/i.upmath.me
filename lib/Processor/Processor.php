<?php
/**
 * @copyright 2014-2022 Roman Parpalak
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

namespace S2\Tex\Processor;

use S2\Tex\Cache\CacheProvider;
use S2\Tex\Renderer\PngConverter;
use S2\Tex\Renderer\RendererInterface;

/**
 * Processes requested URL and caches the result.
 * Uses cache if possible.
 */
class Processor
{
	private RendererInterface $renderer;
	private CacheProvider $cacheProvider;
	private PngConverter $pngConverter;

	public function __construct(
		RendererInterface $renderer,
		CacheProvider $cacheProvider,
		PngConverter $pngConverter
	) {
		$this->renderer      = $renderer;
		$this->cacheProvider = $cacheProvider;
		$this->pngConverter  = $pngConverter;
	}

	public function process(Request $request): Response
	{
		$cacheState = $this->cacheProvider->getCacheState($request);

		if ($cacheState->cacheExists()) {
			// Cached SVG or PNG.
			$modifiedAt = filemtime($cacheState->getCacheName());
			$content    = file_get_contents($cacheState->getCacheName());

			return new CachedResponse($request, $content, $modifiedAt);
		}

		if ($request->isPng()) {
			// Not cached PNG. Maybe there is an SVG cache.
			$svgRequest    = $request->withExtension(Request::SVG);
			$svgCacheState = $this->cacheProvider->getCacheState($svgRequest);
			if ($svgCacheState->cacheExists()) {
				$modifiedAt = time();
				$content    = $this->pngConverter->convert($svgCacheState->getCacheName());

				return new Response($request, $content, $modifiedAt);
			}
		}

		try {
			$modifiedAt = time();
			$content    = $this->renderer->run($request->getFormula(), $request->getExtension());

			return new Response($request, $content, $modifiedAt);
		} catch (\Exception $e) {
			return new Response($request, '', null, $e->getMessage());
		}
	}
}
