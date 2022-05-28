<?php
/**
 * Entry point for async cache optimizer.
 *
 * @copyright 2020-2022 Roman Parpalak
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

require 'vendor/autoload.php';
require 'config.php';

define('SVGO', realpath(SVGO_PATH) . '/svgo -i %1$s -o %1$s.new; rm %1$s; mv %1$s.new %1$s');
define('GZIP', 'gzip -cn6 %1$s > %1$s.gz.new; rm %1$s.gz; mv %1$s.gz.new %1$s.gz');
define('OPTIPNG', 'optipng %1$s');
define('PNGOUT', 'pngout %1$s');

use S2\Tex\Cache\CacheProvider;
use S2\Tex\Processor\DelayedProcessor;
use S2\Tex\Processor\Request;

$delayedProcessor = new DelayedProcessor(
	new CacheProvider(CACHE_SUCCESS_DIR, CACHE_FAIL_DIR),
	'http://localhost:' . (defined('HTTP_SVGO_PORT') ? HTTP_SVGO_PORT : '8800') . '/'
);

$delayedProcessor
	->addSVGCommand(SVGO)
	->addSVGCommand(GZIP)
//	->addPNGCommand(OPTIPNG)
//	->addPNGCommand(PNGOUT)
;

$request = new Request($_POST['formula'], $_POST['extension']);
$delayedProcessor->process($request);
