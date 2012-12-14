<?php
namespace Anph\AdministrationBundle\Tests\Entity\SolrCore;
use Anph\AdministrationBundle\Entity\SolrCore\SolrCoreResponse;

class SolrCoreResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testNewSolrCoreResponse() {
        $XMLResponse = '<response>
	                        <lst name="responseHeader">
		                        <int name="status">0</int>
		                        <int name="QTime">281</int>
	                        </lst>
	                        <str name="core">core2</str>
                            <lst name="error">
		                        <str name="msg">Error handling "reload" action</str>
		                        <str name="trace">org.apache.solr.common.SolrException</str>
		                        <int name="code">500</int>
	                        </lst>
                        </response>';
        $res = new SolrCoreResponse($XMLResponse);
        $this->assertTrue($res->getStatus() == '0');
        $this->assertTrue($res->getMessage() == 'Error handling "reload" action');
        $this->assertTrue($res->getTrace() == 'org.apache.solr.common.SolrException');
        $this->assertTrue($res->getCode() == '500');
        $this->assertTrue($res->isOk());
    }
}
