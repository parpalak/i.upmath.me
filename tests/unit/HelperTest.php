<?php

namespace S2\Tex\Test\unit;

use Codeception\Test\Unit;
use S2\Tex\Helper;

class HelperTest extends Unit
{
	public function testGetShortCommandName()
	{
		$this->assertEquals('gzip', Helper::getShortCommandName('gzip test test_dir/a/test_file.svg.gz'));
		$this->assertEquals('command', Helper::getShortCommandName('some/path/command -a'));
	}
}
