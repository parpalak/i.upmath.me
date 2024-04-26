<?php
/**
 * Autotest runner.
 *
 * @copyright 2024 Roman Parpalak
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

declare(strict_types=1);

use Katzgrau\KLogger\Logger;
use S2\Tex\Renderer\Renderer;
use S2\Tex\Templater;
use S2\Tex\Tester;

require '../vendor/autoload.php';
require '../config.php';

$isDebug = false;
error_reporting(E_ALL);

// Setting up external commands
define('LATEX_COMMAND', TEX_PATH . 'latex -output-directory=' . TMP_DIR);
define('DVISVG_COMMAND', TEX_PATH . 'dvisvgm %1$s -o %1$s.svg -n --exact -v0 --zoom=' . OUTER_SCALE);

echo "\n", 'Using ', TEX_PATH, "\n\n";

$templater = new Templater(TPL_DIR);
$renderer  = new Renderer($templater, 'tmp/', TEX_PATH, LATEX_COMMAND, DVISVG_COMMAND);
$renderer
	->setLogger(new Logger('log/'))
	->setIsDebug($isDebug)
;

$tester = new Tester($renderer, 'src/*.tex', 'result/');
$tester->run(['svg']);

echo "\n";

exit($tester->compareResults('expected/') ? 0 : 1);
