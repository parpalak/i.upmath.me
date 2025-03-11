<?php
/**
 * Test runner and performance checker
 *
 * @copyright 2015-2020 Roman Parpalak
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

use Katzgrau\KLogger\Logger;
use S2\Tex\Renderer\PngConverter;
use S2\Tex\Renderer\Renderer;
use S2\Tex\Templater;
use S2\Tex\Tester;

require '../vendor/autoload.php';
require '../config.php';

$isDebug = defined('DEBUG') && DEBUG;
error_reporting(E_ALL);

$tmpDir = 'tmp/';

// Setting up external commands
define('LATEX_COMMAND', TEX_PATH . 'latex -output-directory=' . $tmpDir);
define('DVISVG_COMMAND', TEX_PATH . 'dvisvgm %1$s -o %1$s.svg -n --exact -v0 --zoom=' . OUTER_SCALE);
// define('DVIPNG_COMMAND', TEX_PATH.'dvipng -T tight %1$s -o %1$s.png -D '.(96 * OUTER_SCALE)); // outdated
define('SVG2PNG_COMMAND', 'rsvg-convert %1$s -d 96 -p 96 -b white'); // stdout

echo "\n", 'Using ', TEX_PATH, "\n\n";

$templater = new Templater(TPL_DIR);
$renderer  = new Renderer($templater, $tmpDir, TEX_PATH, LATEX_COMMAND, DVISVG_COMMAND);
$renderer
	->setPngConverter(new PngConverter(SVG2PNG_COMMAND))
	->setLogger(new Logger('log/'))
	->setIsDebug($isDebug)
;

$tester = new Tester($renderer, 'src/*.tex', '../www/test_out/', SVG2PNG_COMMAND);
$tester->run();

echo "\n";
