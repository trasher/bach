<?php
namespace Bach\IndexationBundle\Tests\Utility;

use Bach\IndexationBundle\Entity\ObjectTree;
use Bach\IndexationBundle\Entity\ObjectSheet;

class ObjectTreeTest extends \PHPUnit_Framework_TestCase
{
	public function testTree()
	{
		$tree = new ObjectTree("root");
		
			
		$tree->append(new ObjectSheet("foo",new Foo()));
		$tree->append(new ObjectTree("child"));
		
		$tree->get("child")->append(new ObjectSheet("foo2", new Foo2()));
		
		$this->assertTrue($tree->get("foo")->getContent() instanceof Foo);
		$this->assertTrue($tree->get("child") instanceof ObjectTree);
		$this->assertTrue($tree->get("child")->get("foo2")->getContent() instanceof Foo2);
		$this->assertEquals("root",$tree->getName());
		
	}
}

class Foo{}
class Foo2{}