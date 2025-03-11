<?php
/**
 * Makes latex documents containing a formula.
 *
 * @copyright 2015-2024 Roman Parpalak
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

namespace S2\Tex;

use S2\Tex\Tpl\Formula;
use S2\Tex\Tpl\Package;
use S2\Tex\Tpl\PackageCollection;
use S2\Tex\Tpl\PreambleEntry;

class Templater implements TemplaterInterface
{
	private string $dir;

	public function __construct(string $dir)
	{
		$this->dir = $dir;
	}

	/**
	 * {@inheritdoc}
	 */
	public function run(string $formula): Formula
	{
		$isMathMode    = true;
		$extraPackages = new PackageCollection();

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
			if (str_contains($formula, '\\begin{' . $command . '}') || str_contains($formula, '\\begin{' . $command . '*}')) {
				$isMathMode = false;
				if ($env) {
					$extraPackages->add($env, new Package($env));
				}
			}
		}

		// Check if there are used certain commands and include corresponding packages
		$test_command = [
			'\\addplot'             => 'pgfplots',
			'\\smartdiagram'        => 'smartdiagram',
			'\\DisplayProof'        => 'bussproofs',
			'\\tdplotsetmaincoords' => 'tikz-3dplot',
			'\\tikz'                => 'tikz',
		];

		foreach ($test_command as $command => $env) {
			if (str_contains($formula, $command . '{') || str_contains($formula, $command . '[') || str_contains($formula, $command . ' ')) {
				$isMathMode = false; // TODO make an option
				if ($env) {
					$extraPackages->add($env, new Package($env));
				}
			}
		}

		// Same as above but for inline commands inside math mode
		$test_command = [
			'\\color'     => 'xcolor',
			'\\textcolor' => 'xcolor',
			'\\colorbox'  => 'xcolor',
			'\\pagecolor' => 'xcolor',
			'\\ce'        => 'mhchem',
			'\\vv'        => 'esvect',
			'\\mathscr'   => 'mathrsfs',
		];

		foreach ($test_command as $command => $env) {
			if (str_contains($formula, $command . '{') || str_contains($formula, $command . ' ')) {
				$extraPackages->add($env, new Package($env));
			}
		}

		$testEntries = [
			'\usetikzlibrary{hobby}', // the code of hobby must be included in preamble only
		];

		foreach ($testEntries as $entry) {
			if (str_contains($formula, $entry)) {
				$extraPackages->add($entry, new PreambleEntry($entry));
			}
		}

		// Custom rules
		if (str_contains($formula, '\\xymatrix') || str_contains($formula, '\\begin{xy}')) {
			$extraPackages->add('xy', new Package('xy', ['all']));
		}

		if (preg_match('#[А-Яа-яЁё]#u', $formula)) {
			$extraPackages->add('babel', new Package('babel', ['russian']));
		}

		if (preg_match('#[\x{1100}-\x{11FF}\x{3130}-\x{318F}\x{A960}-\x{A97C}\x{AC00}-\x{D7AF}\x{D7B0}-\x{D7FF}\x{3200}-\x{321E}\x{3260}-\x{327F}]#u', $formula)) {
			$extraPackages->add('kotex', new Package('kotex'));
		}

		// Parse custom Upmath setup prefixes
		$isInline = $hasDvisvgmOption = false;
		while (true) {
			if (str_starts_with($formula, '\\inline')) {
				$formula  = substr($formula, 7);
				$isInline = true;
				continue;
			}

			if (str_starts_with($formula, '\\dvisvgm')) {
				$formula          = substr($formula, 8);
				$hasDvisvgmOption = true;
				continue;
			}

			break;
		}

		if ($isInline) {
			$formula = '\\textstyle ' . $formula;
		}

		$tpl = $isMathMode ? 'displayformula' : 'common';

		ob_start();
		include $this->dir . $tpl . '.php';
		$documentContent = ob_get_clean();

		ob_start();
		include $this->dir . 'document.php';
		$text = ob_get_clean();

		return new Formula($text, $isMathMode);
	}
}
