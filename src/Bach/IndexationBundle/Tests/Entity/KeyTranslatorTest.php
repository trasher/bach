<?php
namespace Bach\IndexationBundle\Tests\Utility;

use Bach\IndexationBundle\Entity\KeyTranslator;

class KeyTranslatorTest extends \PHPUnit_Framework_TestCase
{
	public function testTranslate()
	{
		$temp = array(	"cle1"	=>	"test",
						"cle2"	=>	array(	"cle1"	=>	"test2")
					);
		
		$translator = new KeyTranslator($temp);
		$translator->addTranslation("cle1","tl1");
		$result = $translator->translate();
		$this->assertTrue(array_key_exists("tl1",$result) && !array_key_exists("cle1",$result) && $result['tl1'] == "test");
		
		$translator->reset();
		$translator->addTranslation("cle1","tl1",1);
		$result = $translator->translate();

		$this->assertTrue(array_key_exists("cle1",$result) 
						&& !array_key_exists("cle1",$result['cle2']) 
						&& array_key_exists("tl1",$result['cle2'])
						&& $result['cle2']['tl1'] == "test2");
		
	}
}