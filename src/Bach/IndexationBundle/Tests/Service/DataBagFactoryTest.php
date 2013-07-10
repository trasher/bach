<?php

namespace Bach\IndexationBundle\Tests\Service;

use Symfony\Component\Finder\SplFileInfo;
use Bach\IndexationBundle\Service\DataBagFactory;
use Bach\IndexationBundle\Entity\Bag\XMLDataBag;

class DataBagFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testIndex()
    {
    	$dom = new \DomDocument();
    	$element = $dom->createElement('data');
    	$element2 = $dom->createElement('address');
    	$element2->setAttribute('loc','Paris');
    	$element->appendChild($element2);
    	$dom->appendChild($element);
    	
    	$tmpName = uniqid();
    	
    	if (!file_exists(__DIR__.'/../../Resources/tmp')) {
    		mkdir(__DIR__.'/../../Resources/tmp', 0777);
    	}
    	
    	$dom->save(__DIR__.'/../../Resources/tmp/'.$tmpName.'.xml');
    	
        $fileInfo = new SplFileInfo($tmpName.'.xml',__DIR__.'/../../Resources/tmp/'.$tmpName.'.xml',__DIR__.'/../../Resources/tmp');
    	
      	$factory = new DataBagFactory();
      	$result = $factory->encapsulate($fileInfo);
        
        $this->assertTrue($result instanceof XMLDataBag);
    
    	unlink($fileInfo->getRelativePath());
    }
}
