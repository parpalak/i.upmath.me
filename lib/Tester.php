<?php
/**
 * Test infrastructure.
 *
 * @copyright 2015-2024 Roman Parpalak
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

declare(strict_types=1);

namespace S2\Tex;

use S2\Tex\Renderer\RendererInterface;

class Tester
{
	private string $srcTemplate;
	private string $outDir;
	private RendererInterface $renderer;

	public function __construct(RendererInterface $renderer, string $srcTpl, string $outDir)
	{
		$this->renderer    = $renderer;
		$this->srcTemplate = $srcTpl;
		$this->outDir      = $outDir;
	}

	public function run(array $extensions = ['svg', 'png']): void
	{
		$this->clearOutDir();

		foreach (glob($this->srcTemplate) as $testFilename) {
			$source = file_get_contents($testFilename);
			$start  = microtime(true);

			foreach ($extensions as $ext) {
				$this->saveResultFile($testFilename, $ext, $this->renderer->run($source, $ext));
			}

			printf("| %-30s| %-8s|\n", $testFilename, round(microtime(true) - $start, 4));
		}
	}

	public function compareResults(string $expectedDir): bool
	{
		$ok = true;
		foreach (glob($this->srcTemplate) as $testFilename) {
			$fileName = basename($testFilename, '.tex');
			$result   = file_get_contents($this->outDir . $fileName . '.svg');
			$expected = file_get_contents($expectedDir . $fileName . '.svg');
			if ($result !== $expected) {
				echo 'Failed: ', $fileName, "\n";
				echo "\tResult: ", $result, "\n";
				echo "\tExpected: ", $expected, "\n";
				$ok = false;
			} else {
				echo 'Passed: ', $fileName, "\n";
			}
		}

		return $ok;
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
