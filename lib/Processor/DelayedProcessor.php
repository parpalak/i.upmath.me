<?php
/**
 * @copyright 2020-2023 Roman Parpalak
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

namespace S2\Tex\Processor;

use S2\Tex\Cache\CacheProvider;
use S2\Tex\Helper;

class DelayedProcessor
{
	private array $svgCommands = [];
	private array $pngCommands = [];
	private CacheProvider $cacheProvider;
	private string $httpSvgoUrl;

	public function __construct(CacheProvider $cacheProvider, string $httpSvgoUrl)
	{
		$this->cacheProvider = $cacheProvider;
		$this->httpSvgoUrl   = $httpSvgoUrl;
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

	public function process(Request $request): void
	{
		if ($request->isSvg()) {
			// Optimizing SVG
			$filePath = $this->cacheProvider->cachePathFromRequest($request, false);

			if ($this->optimizeSvgViaHttp($filePath)) {
				// We've optimized SVG via HTTP service, everything is fine.
				return;
			}

			if (file_exists($filePath)) {
				// Fallback way via executing shell commands in case if HTTP service is down.
				foreach ($this->svgCommands as $pattern) {
					$command = sprintf($pattern, $filePath);
					Helper::newRelicProfileDataStore(
						static fn() => shell_exec($command),
						'shell',
						Helper::getShortCommandName($command)
					);
				}
			}


			return;
		}

		if ($request->isPng()) {
			// Optimizing PNG
			foreach ($this->pngCommands as $pattern) {
				$command = sprintf($pattern, $this->cacheProvider->cachePathFromRequest($request, false));
				Helper::newRelicProfileDataStore(
					static fn() => shell_exec($command),
					'shell',
					Helper::getShortCommandName($command)
				);
			}

			return;
		}

		throw new \InvalidArgumentException(sprintf(
			'Unknown type "%s" for delayed processing. [%s]',
			$request->getExtension(),
			var_export($request, true)
		));
	}

	private function optimizeSvgViaHttp(string $filePath): bool
	{
		$unoptimizedSvg = file_get_contents($filePath);
		if ($unoptimizedSvg === false) {
			return false;
		}

		$context = stream_context_create(['http' => [
			'method'  => 'POST',
			'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
			'content' => $unoptimizedSvg,
		]]);

		$optimizedSvg = Helper::newRelicProfileDataStore(
			fn() => file_get_contents($this->httpSvgoUrl, false, $context),
			'runtime',
			'http-svgo'
		);
		if ($optimizedSvg === false) {
			return false;
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
}
