<?php
/**
 * Bach SolrCoreResponse unit tests
 *
 * PHP version 5
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\AdministrationBundle\Tests\Units\Entity\SolrCore;

use atoum\AtoumBundle\Test\Units;
use Bach\AdministrationBundle\Entity\SolrCore\SolrCoreResponse as CoreResponse;

/**
 * Bach SolrCoreResponse unit tests
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class SolrCoreResponse extends Units\Test
{

    /**
     * Test basic response
     *
     * @return void
     */
    public function testNewSolrCoreResponse()
    {
        $xml = '<response>
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
        $res = new CoreResponse($xml);

        $status = $res->getStatus();
        $this->string($status)->isIdenticalTo('0');

        $message = $res->getMessage();
        $this->string($message)->isIdenticalTo('Error handling "reload" action');

        $trace = $res->getTrace();
        $this->string($trace)->isIdenticalTo('org.apache.solr.common.SolrException');

        $code = $res->getCode();
        $this->string($code)->isIdenticalTo('500');

        $ok = $res->IsOk();
        $this->boolean($ok)->isTrue();
    }
}
