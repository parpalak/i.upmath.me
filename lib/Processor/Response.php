<?php
/**
 * @copyright 2020 Roman Parpalak
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

namespace S2\Tex\Processor;

class Response
{
	private $request;
	private $content;
	private $errorMessage;
	private $modifiedAt;

	public function __construct(Request $request, string $content, ?int $modifiedAt = null, ?string $errorMessage = null)
	{
		$this->request      = $request;
		$this->content      = $content;
		$this->modifiedAt   = $modifiedAt;
		$this->errorMessage = $errorMessage;
	}

	public function hasError(): bool
	{
		return $this->errorMessage !== null;
	}

	public function getContent(): string
	{
		return $this->content;
	}

	public function isSvg(): bool
	{
		return $this->request->isSvg();
	}

	public function isPng(): bool
	{
		return $this->request->isPng();
	}

	public function getError(): string
	{
		return $this->errorMessage;
	}

	public function getRequest(): Request
	{
		return $this->request;
	}

	public function echoContent(): void
	{
		if ($this->errorMessage !== null) {
			return;
		}

		if ($this->isSvg()) {
			header('Content-Type: image/svg+xml');
		} elseif ($this->isPng()) {
			header('Content-Type: image/png');
		}

		header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $this->modifiedAt) . ' GMT');
		header('Content-Length: ' . strlen($this->content));

		echo $this->content;
	}
}
