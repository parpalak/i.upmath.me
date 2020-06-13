<?php

namespace S2\Tex\Test\unit;

use S2\Tex\Renderer\SvgHelper;

class SvgHelperTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function test1(): void
	{
    	$emptySvg = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="3.69562pt" version="1.1" viewBox="1872.02 1486.63 7.57091 2.94545" width="9.49912pt">
<defs>
<path d="m7.18909 -2.50909c0.185454 0 0.381818 0 0.381818 -0.218182s-0.196364 -0.218182 -0.381818 -0.218182h-5.90182c-0.185454 0 -0.381818 0 -0.381818 0.218182s0.196364 0.218182 0.381818 0.218182h5.90182z" id="g0-0"/>
</defs>
<g id="page1"><!--start 1872.02 1489.58 --><use x="1872.02" xlink:href="#g0-0" y="1489.58"/>
<!--bbox 1872.02 1486.63 7.57091 2.94545 --></g>
</svg>';

		$this->assertEquals('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="3.69562pt" version="1.1" viewBox="1872.02 1486.63 7.57091 2.94545" width="9.49912pt">
<defs>
<path d="m7.18909 -2.50909c0.185454 0 0.381818 0 0.381818 -0.218182s-0.196364 -0.218182 -0.381818 -0.218182h-5.90182c-0.185454 0 -0.381818 0 -0.381818 0.218182s0.196364 0.218182 0.381818 0.218182h5.90182z" id="g0-0"/>
</defs>
<g id="page1"><!--start 1872.02 1489.58 --><use x="1872.02" xlink:href="#g0-0" y="1489.58"/>
<!--bbox 1872.02 1486.63 7.57091 2.94545 --></g>
<script type="text/ecmascript">if(window.parent.postMessage)window.parent.postMessage("0|9.49913|3.69562|"+window.location,"*");</script>
</svg>', SvgHelper::processSvgContent($emptySvg, true));
    }

    public function test2(): void
	{
    	$emptySvg = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="9.11587pt" version="1.1" viewBox="1872.02 1483.22 7.86545 7.26545" width="9.86868pt">
<defs>
<path d="m4.46182 -2.50909h3.04364c0.152727 0 0.36 0 0.36 -0.218182s-0.207273 -0.218182 -0.36 -0.218182h-3.04364v-3.05454c0 -0.152727 0 -0.36 -0.218182 -0.36s-0.218182 0.207273 -0.218182 0.36v3.05454h-3.05454c-0.152727 0 -0.36 0 -0.36 0.218182s0.207273 0.218182 0.36 0.218182h3.05454v3.05454c0 0.152727 0 0.36 0.218182 0.36s0.218182 -0.207273 0.218182 -0.36v-3.05454z" id="g0-43"/>
</defs>
<g id="page1"><!--start 1872.02 1489.58 --><use x="1872.02" xlink:href="#g0-43" y="1489.58"/>
<!--bbox 1872.02 1483.22 7.86545 7.26545 --></g>
</svg>';

		$this->assertEquals('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="9.11587pt" version="1.1" viewBox="1872.02 1483.22 7.86545 7.26545" width="9.86868pt">
<defs>
<path d="m4.46182 -2.50909h3.04364c0.152727 0 0.36 0 0.36 -0.218182s-0.207273 -0.218182 -0.36 -0.218182h-3.04364v-3.05454c0 -0.152727 0 -0.36 -0.218182 -0.36s-0.218182 0.207273 -0.218182 0.36v3.05454h-3.05454c-0.152727 0 -0.36 0 -0.36 0.218182s0.207273 0.218182 0.36 0.218182h3.05454v3.05454c0 0.152727 0 0.36 0.218182 0.36s0.218182 -0.207273 0.218182 -0.36v-3.05454z" id="g0-43"/>
</defs>
<g id="page1"><!--start 1872.02 1489.58 --><use x="1872.02" xlink:href="#g0-43" y="1489.58"/>
<!--bbox 1872.02 1483.22 7.86545 7.26545 --></g>
<script type="text/ecmascript">if(window.parent.postMessage)window.parent.postMessage("1.13606|9.86868|9.11587|"+window.location,"*");</script>
</svg>', SvgHelper::processSvgContent($emptySvg, true));
    }

    public function testEmpty()
    {
    	$emptySvg = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="0pt" version="1.1" viewBox="0 0 0 0" width="0pt">
<g id="page1"><!--start 1872.02 1483.22 --><!--bbox 0 0 0 0 --></g>
</svg>';
		$this->tester->assertEquals('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="0pt" version="1.1" viewBox="0 0 0 0" width="0pt">
<g id="page1"><!--start 1872.02 1483.22 --><!--bbox 0 0 0 0 --></g>
<script type="text/ecmascript">if(window.parent.postMessage)window.parent.postMessage("0|0|0|"+window.location,"*");</script>
</svg>', SvgHelper::processSvgContent($emptySvg, true));
    }
}
