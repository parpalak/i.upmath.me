<?php declare(strict_types=1);
/**
 * @copyright 2022 Roman Parpalak
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

namespace S2\Tex;

class Helper
{
	public static function newRelicProfileDataStore(callable $callback, string $product, string $operation, string $collection = 'other')
	{
		if (\extension_loaded('newrelic')) {
			return \newrelic_record_datastore_segment($callback, [
				'product'    => $product,
				'operation'  => $operation,
				'collection' => $collection,
			]);
		}

		return $callback();
	}

	public static function getShortCommandName(string $fullCommandName): string
	{
		return basename(explode(' ', $fullCommandName)[0]);
	}

	/**
	 * Wrapper for file_put_contents()
	 *
	 * 1. Creates parent directories if they do not exist.
	 * 2. Uses atomic rename operation to avoid using partial content and race conditions.
	 *
	 * @param string $filename
	 * @param string $content
	 * @param bool   $overwriteExisting Optimisation flag to unlink existing file first.
	 */
	public static function filePut(string $filename, string $content, bool $overwriteExisting = false): void
	{
		$dir = dirname($filename);
		if (!file_exists($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
			throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
		}

		$tmpFilename = $filename . '.temp';

		file_put_contents($tmpFilename, $content);

		if ($overwriteExisting || !@rename($tmpFilename, $filename)) {
			@unlink($filename);
			@rename($tmpFilename, $filename);
		}
	}
}
