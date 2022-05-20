<?php
/**
 * Makes latex documents containing a formula.
 *
 * @copyright 2015-2022 Roman Parpalak
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

namespace S2\Tex;

use S2\Tex\Tpl\Formula;

class Templater implements TemplaterInterface
{
	private string $dir;

	public function __construct(string $dir)
	{
		$this->dir = $dir;
	}

	/**
	 * {@inheritdoc}
	 * @noinspection OnlyWritesOnParameterInspection
	 * @noinspection PhpArrayWriteIsNotUsedInspection
	 */
	public function run(string $formula): Formula
	{
		$isMathMode    = true;
		$extraPackages = [];

		// Check if there are used certain environments and include corresponding packages
		$test_env = [
			'eqnarray'        => 'eqnarray',
			'tikzcd'          => 'tikz-cd',
			'tikzpicture'     => 'tikz',
			'circuitikz'      => 'circuitikz',
			'sequencediagram' => 'pgf-umlsd',
			'prooftree'       => 'bussproofs',
			'align'           => '', // just turns math mode off
		];

		foreach ($test_env as $command => $env) {
			if (strpos($formula, '\\begin{' . $command . '}') !== false || strpos($formula, '\\begin{' . $command . '*}') !== false) {
				$isMathMode = false;
				if ($env) {
					$extraPackages[$env] = new Tpl\Package($env);
				}
			}
		}

		// Check if there are used certain commands and include corresponding packages
		$test_command = [
			'\\addplot'             => 'pgfplots',
			'\\smartdiagram'        => 'smartdiagram',
			'\\DisplayProof'        => 'bussproofs',
			'\\tdplotsetmaincoords' => 'tikz-3dplot',
		];

		foreach ($test_command as $command => $env) {
			if (strpos($formula, $command) !== false) {
				$isMathMode = false; // TODO make an option
				if ($env) {
					$extraPackages[$env] = new Tpl\Package($env);
				}
			}
		}

		// Same as above but for inline commands inside math mode
		$test_command = [
			'\\color'               => 'xcolor',
			'\\textcolor'           => 'xcolor',
			'\\colorbox'            => 'xcolor',
			'\\pagecolor'           => 'xcolor',
		];

		foreach ($test_command as $command => $env) {
			if (strpos($formula, $command) !== false) {
				$extraPackages[$env] = new Tpl\Package($env);
			}
		}

		// Custom rules
		if (strpos($formula, '\\xymatrix') !== false || strpos($formula, '\\begin{xy}') !== false) {
			$extraPackages['xy'] = new Tpl\Package('xy', ['all']);
		}

		if (preg_match('#[А-Яа-яЁё]#u', $formula)) {
			$extraPackages['babel'] = new Tpl\Package('babel', ['russian']);
		}

		// Other setup
		if (0 === strpos($formula, '\\inline')) {
			$formula = '\\textstyle ' . substr($formula, 7);
		}

		$tpl = $isMathMode ? 'displayformula' : 'common';

		ob_start();
		/** @noinspection PhpIncludeInspection */
		include $this->dir . $tpl . '.php';
		$documentContent = ob_get_clean();

		ob_start();
		/** @noinspection PhpIncludeInspection */
		include $this->dir . 'document.php';
		$text = ob_get_clean();

		return new Formula($text, $isMathMode);
	}
}
