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

use S2\Tex\Cache\CacheProvider;
use S2\Tex\Processor\DelayedProcessor;
use S2\Tex\Processor\Request;

$delayedProcessor = new DelayedProcessor(
	new CacheProvider(CACHE_SUCCESS_DIR, CACHE_FAIL_DIR),
	'http://127.0.0.1:' . (defined('HTTP_SVGO_PORT') ? HTTP_SVGO_PORT : '8800') . '/'
);

$request = new Request($_POST['formula'] ?? '', $_POST['extension'] ?? '');
$delayedProcessor->process($request);
