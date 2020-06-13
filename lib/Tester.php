<?php
/**
 * Test infrastructure.
 *
 * @copyright 2015-2020 Roman Parpalak
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

namespace S2\Tex;

use S2\Tex\Renderer\RendererInterface;

class Tester
{
	private $srcTemplate = 'src/*.tex';
	private $outDir      = '../www/test_out/';

	/**
	 * @var RendererInterface
	 */
	private $renderer;

	public function __construct(RendererInterface $renderer, string $srcTpl, string $outDir)
	{
		$this->renderer    = $renderer;
		$this->srcTemplate = $srcTpl;
		$this->outDir      = $outDir;
	}

	public function run(): void
	{
		$this->clearOutDir();

		foreach (glob($this->srcTemplate) as $testFilename) {
			$source = file_get_contents($testFilename);
			$start  = microtime(1);

			$svg = $this->renderer->run($source, 'svg');
			$this->saveResultFile($testFilename, 'svg', $svg);

			$png = $this->renderer->run($source, 'png');
			$this->saveResultFile($testFilename, 'png', $png);

			printf("| %-30s| %-8s|\n", $testFilename, round(microtime(1) - $start, 4));
		}
	}

	private function saveResultFile(string $testFilename, string $extension, string $content): void
	{
		file_put_contents($this->outDir . basename($testFilename, '.tex') . '.' . $extension, $content);
	}

	private function clearOutDir(): void
	{
		foreach (glob($this->outDir . '*.png') as $outFile) {
			unlink($outFile);
		}

		foreach (glob($this->outDir . '*.svg') as $outFile) {
			unlink($outFile);
		}
	}
}
