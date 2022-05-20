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
				'product'      => $product,
				'operation'    => $operation,
				'collection'   => $collection,
			]);
		}

		return $callback();
	}

	public static function getShortCommandName(string $fullCommandName): string
	{
		return basename(explode(' ', $fullCommandName)[0]);
	}
}
