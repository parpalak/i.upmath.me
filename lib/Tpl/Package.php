<?php
/**
 * @copyright 2015-2020 Roman Parpalak
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

namespace S2\Tex\Tpl;

class Package implements PackageInterface
{
	/**
	 * @var string[]
	 */
	protected $options = [];

	/**
	 * @var string
	 */
	private $package;

	/**
	 * @param string   $package
	 * @param string[] $options
	 */
	public function __construct(string $package, array $options = [])
	{
		$this->package = $package;
		$this->options = $options;
	}

	public function getCode(): string
	{
		return '\\usepackage' . $this->getOptions() . '{' . $this->package . '}';
	}

	private function getOptions(): string
	{
		return empty($this->options) ? '' : '[' . implode(',', $this->options) . ']';
	}
}
