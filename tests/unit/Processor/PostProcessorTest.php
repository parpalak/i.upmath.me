<?php

declare(strict_types=1);

namespace S2\Tex\Test\unit\Processor;

use Codeception\Test\Unit;
use S2\Tex\Cache\CacheProvider;
use S2\Tex\Processor\PostProcessor;
use S2\Tex\Processor\Request;
use S2\Tex\Processor\Response;

class PostProcessorTest extends Unit
{
	public function testErrorCacheDoesNotStoreRequestMetadata(): void
	{
		$root = sys_get_temp_dir() . '/upmath-test-' . bin2hex(random_bytes(8));
		$cacheProvider = new CacheProvider($root . '/success/', $root . '/error/');
		$request = new Request('invalid formula', Request::SVG);
		$response = new Response($request, '', null, 'Renderer details');
		$cacheFile = $cacheProvider->cachePathFromRequest($request, true);

		try {
			$postProcessor = new PostProcessor($cacheProvider);
			$this->assertNull($postProcessor->cacheResponseAndGetAsyncRequest($response));
			$this->assertSame('Invalid formula', file_get_contents($cacheFile));
		} finally {
			@unlink($cacheFile);
			@rmdir(dirname($cacheFile));
			@rmdir(dirname(dirname($cacheFile)));
			@rmdir($root . '/error');
			@rmdir($root);
		}
	}
}
