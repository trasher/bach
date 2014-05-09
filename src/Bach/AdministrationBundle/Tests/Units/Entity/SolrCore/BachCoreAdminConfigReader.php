<?php
/**
 * Bach BachCoreAdminConfigReader unit tests
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

/**
 * Bach BachCoreAdminConfigReader unit tests
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class BachCoreAdminConfigReader extends Units\Test
//class BachCoreAdminConfigReaderTest extends \PHPUnit_Framework_TestCase
{
    private $_bcacr;
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

        $this->_bcacr = new ConfigReader(
            false, 
            $this->params['solr_host'],
            $this->params['solr_port'],
            $this->params['solr_path'],
            'app/cache/test',
            'app'
        );
    }

    /**
     * Test constructor and stream loading
     *
     * @return void
     */
    public function testConstruct()
    {
        $bcacr = new ConfigReader(
            false, 
            $this->params['solr_host'],
            $this->params['solr_port'],
            $this->params['solr_path'],
            'app/cache/test',
            'app'
        );

        $this->object($bcacr)->isInstanceof(
            'Bach\AdministrationBundle\Entity\SolrCore\BachCoreAdminConfigReader'
        );
    }

    /**
     * Test serialization
     *
     * @return void
     */
    public function testSerialization()
    {
        $serial = serialize($this->_bcacr);
        $bool = is_string($serial);

        $this->boolean($bool)->isTrue();

        $unserial = unserialize($serial);
        $this->object($unserial)->isInstanceof(
            'Bach\AdministrationBundle\Entity\SolrCore\BachCoreAdminConfigReader'
        );
    }

    /**
     * Test cores path
     *
     * @return void
     */
    public function testGetCoresPath()
    {
        $actual = $this->_bcacr->getCoresPath();
        $this->string($actual)->isNotEmpty();
    }

    /**
     * Test cores URL
     *
     * @return void
     */
    public function testGetCoresURL()
    {
        $actual = $this->_bcacr->getCoresURL();

        $url_pattern = 'http%ssl://%host:%port%path';
        $url = str_replace(
            array(
                '%ssl',
                '%host',
                '%port',
                '%path'
            ),
            array(
                ($this->params['solr_ssl'] == true) ? 's' : '',
                $this->params['solr_host'],
                $this->params['solr_port'],
                $this->params['solr_path']
            ),
            $url_pattern
        );

        $this->string($actual)->isIdenticalTo($url);
    }

    /**
     * Test core templates path
     *
     * @return void
     */
    public function testGetCoreTemplatePath()
    {
        $actual = $this->_bcacr->getCoreTemplatePath();
        $expected = 'app/config/templates/cores/archives';

        $this->string($actual)->isIdenticalTo($expected);
    }

    /**
     * Test core data dir
     *
     * @return void
     */
    public function testGetCoreDataDir()
    {
        $actual = $this->_bcacr->getDataDir($this->params['solr_search_core']);

        $this->string($actual)->match(
            '#.*/'  . $this->params['solr_search_core'] . '/data/$#'
        );
    }

    /**
     * Test core config dir
     *
     * @return void
     */
    public function testGetDefaultConfigDir()
    {
        $actual = $this->_bcacr->getDefaultConfigDir();
        $expected = 'conf/';

        $this->string($actual)->isIdenticalTo($expected);
    }

    /**
     * Test default config file name
     *
     * @return void
     */
    public function testGetDefaultConfigFileName()
    {
        $actual = $this->_bcacr->getDefaultConfigFileName();
        $expected = 'solrconfig.xml';

        $this->string($actual)->isIdenticalTo($expected);
    }

    /**
     * Test schema default file name
     *
     * @return void
     */
    public function testGetDefaultSchemaFileName()
    {
        $actual = $this->_bcacr->getDefaultSchemaFileName();
        $expected = 'schema.xml';

        $this->string($actual)->isIdenticalTo($expected);
    }

    /**
     * Test temporary cores path
     *
     * @return void
     */
    public function testTempCorepath()
    {
        $actual = $this->_bcacr->getTempCorePath();
        $expected = 'app/cache/test/tmpCores/';

        $this->string($actual)->isIdenticalTo($expected);
    }

    /**
     * Test instance dir
     *
     * @return void
     */
    public function testGetInstanceDir()
    {
        $actual = $this->_bcacr->getInstanceDir($this->params['solr_search_core']);

        $this->string($actual)->match(
            '#.*/'  . $this->params['solr_search_core'] . '/#'
        );
    }

    /**
     * Test conf dir
     *
     * @return void
     */
    public function testConfDir()
    {
        $actual = $this->_bcacr->getConfDir($this->params['solr_search_core']);

        $this->string($actual)->match(
            '#.*/'  . $this->params['solr_search_core'] . '/conf/#'
        );
    }

    /**
     * Test schema path
     *
     * @return void
     */
    public function testSchemaPath()
    {
        $actual = $this->_bcacr->getSchemaPath($this->params['solr_search_core']);

        $this->string($actual)->match(
            '#.*/'  . $this->params['solr_search_core'] . '/conf/schema.xml#'
        );
    }

    /**
     * Test default config filename
     *
     * @return void
     */
    public function testDefaultDataConfigFileName()
    {
        $actual = $this->_bcacr->getDefaultDataConfigFileName();
        $expected = 'data-config.xml';

        $this->string($actual)->isIdenticalTo($expected);
    }
}
