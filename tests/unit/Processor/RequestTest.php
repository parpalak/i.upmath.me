<?php
/**
 * @copyright 2025 Roman Parpalak
 * @license   https://opensource.org/license/mit MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

declare(strict_types=1);

namespace S2\Tex\Test\unit\Processor;

use Codeception\Test\Unit;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use S2\Tex\Processor\Request;

class RequestTest extends Unit
{
	/**
	 * @throws ExpectationFailedException
	 * @throws Exception
	 */
	public function testConstructorWithValidExtensions(): void
	{
		$requestSvg = new Request('2+2', Request::SVG);
		$this->assertEquals(Request::SVG, $requestSvg->getExtension());
		$this->assertEquals('2+2', $requestSvg->getFormula());

		$requestPng = new Request('3*3', Request::PNG);
		$this->assertEquals(Request::PNG, $requestPng->getExtension());
		$this->assertEquals('3*3', $requestPng->getFormula());
	}

	public function testConstructorWithInvalidExtension(): void
	{
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Incorrect output format has been requested. Expected SVG or PNG.');

		new Request('1+1', 'jpg');
	}

	/**
	 * @dataProvider validUriDataProvider
	 * @throws ExpectationFailedException
	 */
	public function testCreateFromUriWithValidData(string $uri, string $expectedExtension, ?string $expectedFormula): void
	{
		if ($expectedFormula === null) {
			$this->expectException(\RuntimeException::class);
		}
		$request = Request::createFromUri($uri);

		$this->assertEquals($expectedExtension, $request->getExtension());
		$this->assertEquals($expectedFormula, $request->getFormula());
	}

	public function validUriDataProvider(): array
	{
		return [
			'SVG with encoded plus' => [
				'/svg/2%2B2',
				Request::SVG,
				'2+2'
			],
			'PNG with multiplication' => [
				'/png/3*3',
				Request::PNG,
				'3*3'
			],
			'SVG with spaces' => [
				'/svg/hello%20world',
				Request::SVG,
				'hello world'
			],
			'PNG with special chars' => [
				'/png/%24%25%5E%26',
				Request::PNG,
				'$%^&'
			],
			'SVG with empty formula' => [
				'/svg/',
				Request::SVG,
				''
			],

			// Base64+Deflate форматы
			'SVGb with simple formula' => [
				'/svgb/M9I2AgA',
				'svg',
				'2+2'
			],
			'PNGb with complex formula' => [
				'/pngb/M9I2AgA',
				'png',
				'2+2'
			],
			'SVGb with spaces' => [
				'/svgb/y0jNyclXKM8vykkBAA',
				'svg',
				'hello world'
			],
			'PNGb with special chars' => [
				'/pngb/U1GNUwMA',
				'png',
				'$%^&'
			],
			'SVGb with empty formula' => [
				'/svgb/', // empty
				'svg',
				null
			],
			'PNGb with complex math' => [
				'/pngb/i8nMK4lPjEtSSNOo0FRIqVCwVXDTSNJU0FVw00jUBAA',
				'png',
				'\int_a^b f(x) dx = F(b) - F(a)'
			],
			'PNGb with long formula' => [
				'/pngb/S4wzUtBWSIozUrBVSI4zUnD0c1GoAItVgsWq4owA',
				'png',
				'a^2 + b^2 = c^2 AND x^2 + y^2 = z^2'
			]
		];
	}
	public function testCreateFromUriWithInvalidFormat(): void
	{
		$this->expectException(\RuntimeException::class);
		$this->expectExceptionMessage('Incorrect input format.');

		Request::createFromUri('/svg');
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testIsSvgAndIsPng(): void
	{
		$svgRequest = new Request('test', Request::SVG);
		$this->assertTrue($svgRequest->isSvg());
		$this->assertFalse($svgRequest->isPng());

		$pngRequest = new Request('test', Request::PNG);
		$this->assertTrue($pngRequest->isPng());
		$this->assertFalse($pngRequest->isSvg());
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testWithExtension(): void
	{
		$original = new Request('original', Request::SVG);
		$modified = $original->withExtension(Request::PNG);

		$this->assertEquals(Request::SVG, $original->getExtension());
		$this->assertEquals(Request::PNG, $modified->getExtension());
		$this->assertEquals('original', $modified->getFormula());
	}

	public function testWithInvalidExtension(): void
	{
		$original = new Request('test', Request::SVG);

		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Unsupported extension "pdf".');

		$original->withExtension('pdf');
	}
}
