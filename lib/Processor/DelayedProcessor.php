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

class DelayedProcessor
{
	private array $svgCommands = [];
	private array $pngCommands = [];
	private CacheProvider $cacheProvider;

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

	public function process(Request $request): void
	{
		if ($request->isSvg()) {
			// Optimizing SVG
			foreach ($this->svgCommands as $pattern) {
				$command = sprintf($pattern, $this->cacheProvider->cachePathFromRequest($request, false));
				Helper::newRelicProfileDataStore(
					static fn () => shell_exec($command),
					'shell',
					Helper::getShortCommandName($command)
				);
			}

			return;
		}

		if ($request->isPng()) {
			// Optimizing PNG
			foreach ($this->pngCommands as $pattern) {
				$command = sprintf($pattern, $this->cacheProvider->cachePathFromRequest($request, false));
				Helper::newRelicProfileDataStore(
					static fn () => shell_exec($command),
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
}
