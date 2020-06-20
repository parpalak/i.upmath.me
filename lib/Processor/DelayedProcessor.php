<?php
/**
 * @copyright 2020 Roman Parpalak
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

namespace S2\Tex\Processor;

use S2\Tex\Cache\CacheProvider;

class DelayedProcessor
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

	public function process(Request $request): void
	{
		if ($request->getExtension() === Request::SVG) {
			// Optimizing SVG
			foreach ($this->svgCommands as $command) {
				shell_exec(sprintf($command, $this->cacheProvider->cachePathFromRequest($request, false)));
			}
		}

		if ($request->getExtension() === Request::PNG) {
			// Optimizing PNG
			foreach ($this->pngCommands as $command) {
				shell_exec(sprintf($command, $this->cacheProvider->cachePathFromRequest($request, false)));
			}
		}

		throw new \InvalidArgumentException('Unknown type "%s" for delayed processing.');
	}
}
