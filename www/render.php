<?php
/**
 * Entry point for rendering.
 *
 * @copyright 2014-2020 Roman Parpalak
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

use Katzgrau\KLogger\Logger;
use S2\Tex\Cache\CacheProvider;
use S2\Tex\Processor\CachedResponse;
use S2\Tex\Processor\PostProcessor;
use S2\Tex\Processor\Processor;
use S2\Tex\Processor\Request;
use S2\Tex\Renderer\PngConverter;
use S2\Tex\Renderer\Renderer;
use S2\Tex\Templater;

require '../vendor/autoload.php';
require '../config.php';

$isDebug = defined('DEBUG') && DEBUG;
error_reporting($isDebug ? E_ALL : -1);

// Setting up external commands
define('LATEX_COMMAND', TEX_PATH . 'latex -output-directory=' . TMP_DIR);
define('DVISVG_COMMAND', TEX_PATH . 'dvisvgm %1$s -o %1$s.svg -n --exact -v0 --relative --zoom=' . OUTER_SCALE);
// define('DVIPNG_COMMAND', TEX_PATH . 'dvipng -T tight %1$s -o %1$s.png -D ' . (96 * OUTER_SCALE)); // outdated
define('SVG2PNG_COMMAND', 'rsvg-convert %1$s -d 96 -p 96 -b white'); // stdout

define('SVGO', realpath(SVGO_PATH) . '/svgo -i %1$s -o %1$s.new; rm %1$s; mv %1$s.new %1$s');
define('GZIP', 'gzip -cn6 %1$s > %1$s.gz.new; rm %1$s.gz; mv %1$s.gz.new %1$s.gz');
define('OPTIPNG', 'optipng %1$s');
define('PNGOUT', 'pngout %1$s');

function error400($error = 'Invalid formula')
{
	header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
	include '400.php';
}


//ignore_user_abort();
ini_set('max_execution_time', 10);
header('X-Powered-By: Upmath Latex Renderer');

$templater = new Templater(TPL_DIR);

$pngConverter = new PngConverter(SVG2PNG_COMMAND);
$renderer     = new Renderer($templater, TMP_DIR, LATEX_COMMAND, DVISVG_COMMAND);
$renderer
	->setPngConverter($pngConverter)
	->setIsDebug($isDebug)
;
if (defined('LOG_DIR')) {
	$renderer->setLogger(new Logger(LOG_DIR));
}

$cacheProvider = new CacheProvider(CACHE_SUCCESS_DIR, CACHE_FAIL_DIR);
$processor     = new Processor($renderer, $cacheProvider, $pngConverter);

try {
	$request = Request::createFromUri(
		parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
	);
} catch (Exception $e) {
	error400($isDebug ? $e->getMessage() : 'Invalid formula');
	die;
}

$response = $processor->process($request);

if (!$response->hasError()) {
	$response->echoContent();
} else {
	error400($isDebug ? $response->getError() : 'Invalid formula');
}

if (!$isDebug && !($response instanceof CachedResponse)) {
	// Disconnecting from web-server
	flush();
	fastcgi_finish_request();

	$postProc = new PostProcessor($cacheProvider);
	$postProc
		->addSVGCommand(SVGO)
		->addSVGCommand(GZIP)
//		->addPNGCommand(OPTIPNG)
//		->addPNGCommand(PNGOUT)
	;
	$postProc->cacheResponse($response, $_SERVER['HTTP_REFERER']);
}
