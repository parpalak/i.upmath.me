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

readonly class Tester
{
	public function __construct(
		private RendererInterface $renderer,
		private string            $srcTemplate,
		private string            $outDir,
		private string            $svg2pngCommand,
	) {
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
			$fileName        = basename($testFilename, '.tex');
			$resultContent   = $this->convertSvgToPng($this->outDir . $fileName . '.svg');
			$expectedContent = $this->convertSvgToPng($expectedDir . $fileName . '.svg');
			if ($resultContent !== $expectedContent && ($diff = $this->diffPngImages($expectedContent, $resultContent)) > 0) {
				file_put_contents($this->outDir . $fileName . '.expected.png',  $expectedContent);
				file_put_contents($this->outDir . $fileName . '.result.png',  $resultContent);
				file_put_contents($this->outDir . $fileName . '.diff.png',  $this->createDiffImage($expectedContent, $resultContent));
				echo 'Failed: ', $fileName, "\n";
				echo "\tDiff: ", $diff, "\n";
//				echo "\tResult: ", file_get_contents($this->outDir . $fileName . '.svg'), "\n";
//				echo "\tExpected: ", file_get_contents($expectedDir . $fileName . '.svg'), "\n";
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

	private function convertSvgToPng(string $svgFileName): string
	{
		$command = sprintf($this->svg2pngCommand, $svgFileName);
		ob_start();
		passthru($command);

		return ob_get_clean();
	}

	private function diffPngImages(string $content1, string $content2): float
	{
		$image1 = imagecreatefromstring($content1);
		$image2 = imagecreatefromstring($content2);

		$width1  = imagesx($image1);
		$height1 = imagesy($image1);
		$width2  = imagesx($image2);
		$height2 = imagesy($image2);

		if ($width1 !== $width2 || $height1 !== $height2) {
			return 999;
		}

		// Сравниваем пиксели
		$differentPixels = 0;
		for ($x = 0; $x < $width1; $x++) {
			for ($y = 0; $y < $height1; $y++) {
				if (imagecolorat($image1, $x, $y) !== imagecolorat($image2, $x, $y)) {
					$differentPixels++;
				}
			}
		}

		return $differentPixels / ($width1 * $height1);
	}

	private function createDiffImage(string $content1, string $content2): string
	{
		$image1 = imagecreatefromstring($content1);
		$image2 = imagecreatefromstring($content2);

		$width1  = imagesx($image1);
		$height1 = imagesy($image1);
		$width2  = imagesx($image2);
		$height2 = imagesy($image2);

		if ($width1 !== $width2 || $height1 !== $height2) {
			return '';
		}

		$diffImage = imagecreatetruecolor($width1, $height1);

		$black = imagecolorallocate($diffImage, 0, 0, 0);
		$white = imagecolorallocate($diffImage, 255, 255, 255);

		for ($x = 0; $x < $width1; $x++) {
			for ($y = 0; $y < $height1; $y++) {
				if (imagecolorat($image1, $x, $y) !== imagecolorat($image2, $x, $y)) {
					imagesetpixel($diffImage, $x, $y, $white);
				} else {
					imagesetpixel($diffImage, $x, $y, $black);
				}
			}
		}

		ob_start();
		imagepng($diffImage);
		$diffContent = ob_get_contents();
		ob_end_clean();

		imagedestroy($image1);
		imagedestroy($image2);
		imagedestroy($diffImage);

		return $diffContent;
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
