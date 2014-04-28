<?php
/**
 * Bach SolrCoreAdmin unit tests
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
use Bach\AdministrationBundle\Entity\SolrCore\BachCoreAdminConfigReader
    as ConfigReader;
use Bach\AdministrationBundle\Entity\SolrCore\SolrCoreAdmin as CoreAdmin;

/**
 * Bach SolrCoreAdmin unit tests
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class SolrCoreAdmin extends Units\Test
{
    private $_sca;
    protected $conf;
    protected $params;

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

        $config_reader = new ConfigReader(
            false, 
            $this->params['solr_host'],
            $this->params['solr_port'],
            $this->params['solr_path'],
            'app/cache/test',
            'app'
        );

        $this->_sca = new CoreAdmin($config_reader);
    }

    /**
     * Test core creation
     *
     * @return void
     */
    /*public function testCreate()
    {
        $response = $this->_sca->create(
            'coreTestCreate', 
            'coreTestCreateAAA'
        );
        $this->assertTrue($response === false ? false : $response->isOk());
        $this->_sca->delete('coreTestCreate');
    }*/

    /**
     * Test all core status
     *
     * @return void
     */
    public function testGetStatusAllCore()
    {
        $response = $this->_sca->getStatus();
        $is_ok = $response->isOk();

        $this->boolean($is_ok)->isTrue();
    }

    /**
     * Test one core status
     *
     * @return void
     */
    public function testGetStatusOneCore()
    {
        $response = $this->_sca->getStatus($this->params['solr_search_core']);
        $is_ok = $response->isOk();

        $this->boolean($is_ok)->isTrue();
    }

    /*public function testReload() {
        $this->sca->create('coreTestReload', 'coreTestReload');
        $response = $this->sca->reload('coreTestReload');
        if ($response->isOk()) {
            $this->assertTrue(true);
        } else {
            echo '#####RELOAD#####';
            echo 'MSG :' . $response->getMessage() . '#####';
            echo 'CODE :' . $response->getCode() . '#####';
            echo 'TRACE :' . $response->getTrace() . '#####';
            $this->assertTrue(false);
        }
        $this->sca->delete('coreTestReload');
    }

    public function testRename() {
        $this->sca->create('coreTestRename', 'coreTestRename');
        $response = $this->sca->rename('coreTestRename', 'coreNewTestRename');
        $this->assertTrue($response === false ? false : $response->isOk());
        $this->sca->delete('coreNewTestRename');
    }

    public function testSwap() {
        $this->sca->create('coreTestSwap1', 'coreTestSwap1');
        $this->sca->create('coreTestSwap2', 'coreTestSwap2');
        $response = $this->sca->swap('core1', 'core0');
        $this->assertTrue($response->isOk());
        $this->sca->delete('coreTestSwap1');
        $this->sca->delete('coreTestSwap2');
    }

    public function testUnload() {
        $a = $this->sca->create('coreTestUnload', 'coreTestUnload3', true);
        $response = $this->sca->unload('coreTestUnload');
        $this->assertTrue($response->isOk());
        $this->sca->delete('coreTestUnload');
    }

    public function testDeleteIndex() {
        $this->sca->create('coreTestDeleteIndex', 'coreTestDeleteIndex');
        $response = $this->sca->delete(
            'coreTestDeleteIndex',
            SolrCoreAdmin::DELETE_INDEX
        );
        $this->assertTrue($response === false ? false : $response->isOk());
        $this->sca->delete('coreTestDeleteIndex');
    }

    public function testDeleteData() {
        $this->sca->create('coreTestDeleteData', 'coreTestDeleteData');
        $response = $this->sca->delete(
            'coreTestDeleteData',
            SolrCoreAdmin::DELETE_DATA
        );
        $this->assertTrue($response === false ? false : $response->isOk());
        $this->sca->delete('coreTestDeleteData');
    }

    public function testDeleteCore() {
        $this->sca->create('coreTestDeleteCore', 'coreTestDeleteCore');
        $response = $this->sca->delete('coreTestDeleteCore');
        $this->assertTrue($response);
    }*/
}
