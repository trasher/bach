<?php
namespace Anph\AdministrationBundle\Tests\Entity\SolrCore;
use Anph\AdministrationBundle\Entity\SolrCore\SolrCoreAdmin;

class SolrCoreAdminTest extends \PHPUnit_Framework_TestCase
{
	private $sca;
	
	public function setUp() {
		$this->sca = new SolrCoreAdmin();
	}
	
	public function tearDown() {
		unset($this->sca);
	}
	
	public function testCreate() {
		$response = $this->sca->create('bobo');
		echo '##########' . $response . '#########';
		$this->assertTrue($response !== false);
	}
}