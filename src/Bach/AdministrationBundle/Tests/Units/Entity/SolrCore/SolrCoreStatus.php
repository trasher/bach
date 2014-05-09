<?php
/**
 * Bach SolrCoreStatus unit tests
 *
 * PHP version 5
 *
 * Copyright (c) 2014, Anaphore
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are
 * met:
 *
 *     (1) Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *     (2) Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *     (3)The name of the author may not be used to
 *    endorse or promote products derived from this software without
 *    specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT,
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
 * STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING
 * IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */

namespace Bach\AdministrationBundle\Tests\Units\Entity\SolrCore;

use atoum\AtoumBundle\Test\Units;
use Symfony\Component\Yaml\Parser;
use Bach\AdministrationBundle\Entity\SolrCore\SolrCoreStatus as CoreStatus;

/**
 * Bach SolrCoreStatus unit tests
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class SolrCoreStatus extends Units\Test
{
    private $_sca;
    private $_scs;

    /**
     * Set up tests
     *
     * @param stgring $testMethod Method tested
     *
     * @return void
     */
    public function beforeTestMethod($testMethod)
    {
        $yaml = new Parser();

        //load main configuration
        $config_file = 'app/config/defaults_parameters.yml';
        $this->conf = $yaml->parse(
            file_get_contents($config_file)
        );

        //load local configuration
        $config_file = 'app/config/parameters.yml';
        $this->conf = array_replace_recursive(
            $this->conf,
            $yaml->parse(
                file_get_contents($config_file)
            )
        );


        $this->params = $this->conf['parameters'];
        if ( $this->params['solr_search_core'] === '%ead_corename%' ) {
            $this->params['solr_search_core'] = $this->params['ead_corename'];
        }

        $config_reader = new BachCoreAdminConfigReader(
            false, 
            $this->params['solr_host'],
            $this->params['solr_port'],
            $this->params['solr_path'],
            'app/cache/test',
            'app'
        );
        $this->_sca = new SolrCoreAdmin($config_reader);
        $this->_scs = $this->_sca
            ->getStatus($this->params['solr_search_core'])
            ->getCoreStatus($this->params['solr_search_core']);
    }

    /**
     * Test if core is default
     *
     * @return void
     */
    /*public function testIsDefaultCore()
    {
        $is_default = $this->_scs->isDefaultCore();
        $this->boolean($is_default)->isFalse();
    }*/

    /*public function testGetInstanceDir()
    {
        $object = $this->scs->getInstanceDir();
        if ($object === null) {
            $this->assertTrue(false);
        } else {
            $this->assertEquals(
                $object,
                '/var/solr/TestCoreStatus/',
                'GetInstanceDir error!'
            );
        }
    }

    public function testGetDataDir()
    {
        $object = $this->scs->getdataDir();
        if ($object === null) {
            $this->assertTrue(false);
        } else {
            $this->assertEquals(
                $object,
                '/var/solr/TestCoreStatus/data/',
                'GetDataDir error!'
            );
        }
    }

    public function testGetConfig()
    {
        $object = $this->scs->getConfig();
        if ($object === null) {
            $this->assertTrue(false);
        } else {
            $this->assertEquals($object, 'solrconfig.xml', 'GetConfig error!');
        }
    }

    public function testGetSchema()
    {
        $object = $this->scs->getSchema();
        if ($object === null) {
            $this->assertTrue(false);
        } else {
            $this->assertEquals($object, 'schema.xml', 'GetSchema error!');
        }
    }

    public function testGetStartTime()
    {
        $this->assertInstanceOf(
            'DateTime',
            $this->scs->getStartTime(),
            'GetStartTime error!'
        );
    }

    public function testGetUptime()
    {
        $this->assertTrue(true);
    }

    public function testGetNumDocs()
    {
        $this->assertEquals($this->scs->getNumDocs(), '0', 'GetNumDocs error!');
    }

    public function testGetMaxDocs()
    {
        $this->assertEquals($this->scs->getMaxDoc(), '0', 'GetMaxDocs error!');
    }

    public function testGetVersion()
    {
        $this->assertEquals($this->scs->getVersion(), '1', 'GetVersion error!');
    }

    public function testGetSegmentCount()
    {
        $this->assertEquals(
            $this->scs->getSegmentCount(),
            '0',
            'GetSegmentCount error!'
        );
    }

    public function testGetCurrent()
    {
        $this->assertTrue($this->scs->getCurrent(), 'GetCurrent error!');
    }

    public function testHasDeletions()
    {
        $this->assertFalse($this->scs->hasDeletions(), 'HasDeletions error!');
    }

    public function testGetDirectory()
    {
        $this->assertNotEquals(
            $this->scs->getDirectory(),
            null,
            'GetDirectory error!'
        );
    }

    public function testGetSizeInBytes()
    {
        $this->assertEquals(
            $this->scs->getSizeInBytes(),
            '65',
            'GetSizeInBytes error!'
        );
    }

    public function testGetSize()
    {
        $this->assertEquals($this->scs->getSize(), '65 bytes', 'GetSize error!');
    }*/
}
