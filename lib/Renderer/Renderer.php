<?php
/**
 * @copyright 2014-2026 Roman Parpalak
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

namespace S2\Tex\Renderer;

use Psr\Log\LoggerInterface;
use S2\Tex\Helper;
use S2\Tex\TemplaterInterface;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

/**
 * Runs Latex CLI.
 */
class Renderer implements RendererInterface
{
	private TemplaterInterface $templater;
	private ?PngConverter $pngConverter;
	private ?LoggerInterface $logger;
	private string $tmpDir;
	private bool $isDebug = false;
	private string $texPath;
	private string $latexCommand;
	private string $svgCommand;
	private ?string $pngCommand;

	public function __construct(
		TemplaterInterface $templater,
		string             $tmpDir,
		string             $texPath,
		string             $latexCommand,
		string             $svgCommand,
		?string            $pngCommand = null
	) {
		$this->templater    = $templater;
		$this->tmpDir       = $tmpDir;
		$this->latexCommand = $latexCommand;
		$this->svgCommand   = $svgCommand;
		$this->pngCommand   = $pngCommand;
		$this->texPath      = $texPath;
	}

	public function setIsDebug(bool $isDebug): self
	{
		$this->isDebug = $isDebug;

		return $this;
	}

	public function setLogger(LoggerInterface $logger): self
	{
		$this->logger = $logger;

		return $this;
	}

	public function setPngConverter(PngConverter $pngConverter): self
	{
		$this->pngConverter = $pngConverter;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function run(string $formula, string $type): string
	{
		$this->validateFormula($formula);

		$formulaObj = $this->templater->run($formula);
		$texSource  = $formulaObj->getText();
		$this->echoDebug(htmlspecialchars($texSource));

		// Each render gets its own private directory.
		$workDir = $this->createWorkDir();
		$tmpName = $workDir . '/formula';

		// Latex
		if (file_put_contents($tmpName, $texSource) === false) {
			$this->cleanupWorkDir($workDir);
			throw new \RuntimeException('Cannot write the temporary Latex source.');
		}

		/**
		 * Adding the TeX bin directory (e.g. `/home/tex/tl-2020/bin/x86_64-linux`) to the PATH environment variable.
		 * Otherwise, there can be floating bugs when generating font files.
		 *
		 * See https://github.com/symfony/symfony/issues/5030 for 'exec' hack
		 */
		$process = Process::fromShellCommandline(
			'PATH="$PATH:' . $this->texPath . '" exec ' . $this->latexCommand
			. ' -no-shell-escape -output-directory=' . escapeshellarg($workDir)
			. ' formula 2>&1',
			$workDir,
			self::texSecurityEnvironment($workDir)
		);
		$process->setTimeout(8);

		try {
			$exitCode = Helper::newRelicProfileDataStore(
				static fn() => $process->run(),
				'shell',
				Helper::getShortCommandName($this->latexCommand)
			);
		} catch (\Exception $e) {
			if ($this->logger !== null) {
				$message = $e instanceof ProcessTimedOutException ? 'Latex has been interrupted by a timeout' : 'Cannot run Latex';
				$this->logger->error($message, [
					'message' => $e->getMessage(),
					'command' => $process->getCommandLine(),
					'formula' => self::shorten($formula),
				]);
			}
			$this->dumpDebug($texSource);
			$this->cleanupWorkDir($workDir);

			throw $e;
		}

		if ($this->isDebug) {
			echo '<pre>';
			readfile($tmpName . '.log');
			var_dump('exitcode', $exitCode);
			echo '</pre>';
		}

		if (!file_exists($tmpName . '.dvi')) {
			$texError = self::extractTexError((string)@file_get_contents($tmpName . '.log'));
			if ($texError !== null) {
				// An error in the formula itself, nothing to investigate.
				if ($this->logger !== null) {
					$this->logger->notice(self::oneLine('Invalid formula: ' . $texError . ' | ' . self::shorten($formula)));
				}

				$this->cleanupWorkDir($workDir);
				throw new \RuntimeException('Invalid formula');
			}

			// Ohe has to figure out why the process was killed and why no dvi-file has been created.
			if ($this->logger !== null) {
				$this->logger->error('Latex finished incorrectly', [
					'command'                   => $process->getCommandLine(),
					'exit_code'                 => $process->getExitCode(),
					'exit_code_text'            => $process->getExitCodeText(),
					"file_exists($tmpName.dvi)" => file_exists($tmpName . '.dvi'),
				]);
				$this->logger->error('source', [$texSource]);
				$this->logger->error('trace (' . $tmpName . '.log)', [@file_get_contents($tmpName . '.log')]);
			}

			$this->cleanupWorkDir($workDir);
			throw new \RuntimeException('Invalid formula');
		}

		// DVI -> SVG
		$cmd       = sprintf($this->svgCommand, $tmpName);
		$svgOutput = Helper::newRelicProfileDataStore(
			static fn() => shell_exec($cmd),
			'shell',
			Helper::getShortCommandName($cmd)
		);

		$this->dumpDebug($cmd);
		$this->dumpDebug($svgOutput);
		if (!file_exists($tmpName . '.svg')) {
			// Ohe has to figure out why the dvisvgm command has failed.
			if ($this->logger !== null) {
				$this->logger->error('Dvisvgm finished incorrectly', [
					'command'                   => $cmd,
					'dvisvgm output'            => $svgOutput,
					"file_exists($tmpName.svg)" => file_exists($tmpName . '.svg'),
				]);
				$this->logger->error('source', [$texSource]);
				$this->logger->error('trace (' . $tmpName . '.log)', [@file_get_contents($tmpName . '.log')]);
			}

			$this->cleanupWorkDir($workDir);
			throw new \RuntimeException('Invalid formula');
		}

		$svgContent = SvgHelper::processSvgContent(file_get_contents($tmpName . '.svg'), $formulaObj->useBaseline());

		if ($type === 'png') {
			if ($this->pngConverter) {
				// SVG -> PNG
				$pngContent = $this->pngConverter->convert($tmpName . '.svg');
			}
			if ($this->pngCommand) {
				// DVI -> PNG
				Helper::newRelicProfileDataStore(
					static fn() => exec(sprintf($this->pngCommand, $tmpName)),
					'shell',
					Helper::getShortCommandName($this->pngCommand)
				);
				$pngContent = file_get_contents($tmpName . '.png');
			}
		}

		// Cleaning up
		$this->cleanupWorkDir($workDir);

		return $type === 'png' ? $pngContent : $svgContent;
	}

	/**
	 * Returns the first error message from a Latex log up to the "l.<number>" line with the error location, if any.
	 */
	public static function extractTexError(string $log): ?string
	{
		$lines = explode("\n", $log);
		foreach ($lines as $i => $line) {
			if (!str_starts_with($line, '!')) {
				continue;
			}

			$errorLines = \array_slice($lines, $i, 6);
			foreach ($errorLines as $j => $errorLine) {
				if (preg_match('#^l\.\d+ #', $errorLine) === 1) {
					// The location line is wrapped, the next line contains the rest of the input.
					$errorLines = \array_slice($errorLines, 0, $j + 2);
					break;
				}
			}

			return self::shorten(implode(' ', $errorLines), 500);
		}

		return null;
	}

	private static function texSecurityEnvironment(string $workDir): array
	{
		return [
			'openin_any'   => 'p',
			'openout_any'  => 'p',
			'shell_escape' => 'f',
			'TEXMFOUTPUT'  => $workDir,
		];
	}

	private function createWorkDir(): string
	{
		$baseDir = realpath($this->tmpDir);
		if ($baseDir === false) {
			throw new \RuntimeException('Temporary directory does not exist.');
		}
		for ($attempt = 0; $attempt < 5; $attempt++) {
			$workDir = $baseDir . '/render-' . bin2hex(random_bytes(12));
			if (@mkdir($workDir, 0700)) {
				return $workDir;
			}
		}

		throw new \RuntimeException('Cannot create a temporary directory.');
	}

	/**
	 * Unexpected files are kept for manual inspection.
	 */
	private function cleanupWorkDir(string $workDir): void
	{
		$tmpName = $workDir . '/formula';
		foreach (['', '.log', '.aux', '.dvi', '.svg', '.png'] as $ext) {
			@unlink($tmpName . $ext);
		}

		if (@rmdir($workDir)) {
			return;
		}

		if ($this->logger !== null) {
			$this->logger->warning('Unexpected files left in the temporary directory', [
				'dir'   => $workDir,
				'files' => array_values(array_diff(scandir($workDir) ?: [], ['.', '..'])),
			]);
		}
	}

	private static function shorten(string $text, int $maxLength = 2000): string
	{
		return \strlen($text) > $maxLength ? substr($text, 0, $maxLength) . '...' : $text;
	}

	private static function oneLine(string $text): string
	{
		return preg_replace('#\s+#', ' ', $text) ?? $text;
	}

	/**
	 * @param mixed $output
	 */
	private function dumpDebug($output): void
	{
		if ($this->isDebug) {
			echo '<pre>';
			var_dump($output);
			echo '</pre>';
		}
	}

	private function echoDebug(string $output): void
	{
		if ($this->isDebug) {
			echo '<pre>';
			echo $output;
			echo '</pre>';
		}
	}

	private function validateFormula(string $formula): void
	{
		foreach (['\\write', '\\input', '\\usepackage', '\\special', '\\include'] as $disabledCommand) {
			if (strpos($formula, $disabledCommand) !== false) {
				if ($this->logger !== null) {
					$this->logger->error(self::oneLine(sprintf('Forbidden command "%s": ', $disabledCommand) . self::shorten($formula)));
					$this->logger->error('Server vars: ', $_SERVER);
				}
				throw new \RuntimeException('Forbidden commands.');
			}
		}

		if (preg_match('#{\\s*filecontents\\s*\\*?\\s*}#', $formula) === 1) {
			if ($this->logger !== null) {
				$this->logger->error(self::oneLine('Forbidden command "filecontents": ' . self::shorten($formula)));
				$this->logger->error('Server vars: ', $_SERVER);
			}
			throw new \RuntimeException('Forbidden commands.');
		}
	}
}
