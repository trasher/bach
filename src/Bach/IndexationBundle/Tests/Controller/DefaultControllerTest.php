<?php

namespace Bach\IndexationBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Finder\SplFileInfo;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $fileInfo = new SplFileInfo("FRAD027_404142R.xml",__DIR__.'/../../Controller/FRAD027_404142R.xml',__DIR__);
    	
        $this->assertTrue(file_exists($fileInfo->getRelativePath()));
    }
}
