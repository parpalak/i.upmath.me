<?php
/**
 * @copyright 2026 Roman Parpalak
 * @license   https://opensource.org/license/mit MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

namespace S2\Tex\Test\unit\Renderer;

use Codeception\Test\Unit;
use S2\Tex\Renderer\Renderer;

class RendererTest extends Unit
{
	public function testExtractTexErrorWithContext(): void
	{
		$log = <<<'LOG'
File: umsa.fd 2013/01/14 v3.01 AMS symbols A
)
LaTeX Font Info:    Trying to load font information for U+msb on input line 76.

(/opt/tl-2025/texmf-dist/tex/latex/amsfonts/umsb.fd
File: umsb.fd 2013/01/14 v3.01 AMS symbols B
)
LaTeX Font Info:    Trying to load font information for U+stmry on input line 7
6.
(/opt/tl-2025/texmf-dist/tex/latex/stmaryrd/Ustmry.fd)
! Missing $ inserted.
<inserted text>
                                $
l.79 \begin{cases} \u_
                                            {\max} & \psi_L > 0 \
I've inserted a begin-math/end-math symbol since I think
you left one out. Proceed, with fingers crossed.

! Missing { inserted.
<to be read again>
                                     \global
l.79 \begin{cases} \u_
                                            {\max} & \psi_L > 0 \
A left brace was mandatory here, so I've put one in.
You might want to delete and/or insert some corrections
so that I will find a matching right brace soon.
(If you're confused by all this, try typing `I}' now.)

! Improper \spacefactor.
<recently read> \spacefactor
LOG;

		$this->assertSame(
			"! Missing \$ inserted. <inserted text>                                 \$ l.79 \\begin{cases} \\u_                                             {\\max} & \\psi_L > 0 \\",
			Renderer::extractTexError($log)
		);
	}

	public function testExtractTexErrorTakesFirstError(): void
	{
		$log = "line\n! First error.\nl.5 x\n\n! Second error.\nl.6 y\n";

		$this->assertSame("! First error. l.5 x ", Renderer::extractTexError($log));
	}

	public function testExtractTexErrorWithoutError(): void
	{
		$this->assertNull(Renderer::extractTexError("This is pdfTeX\nOutput written on formula.dvi (1 page, 228 bytes).\n"));
		$this->assertNull(Renderer::extractTexError(''));
	}

	public function testExtractTexErrorIsLimited(): void
	{
		$error = Renderer::extractTexError('! ' . str_repeat('x', 1000));

		$this->assertSame(503, \strlen($error));
		$this->assertStringEndsWith('...', $error);
	}
}
