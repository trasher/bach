<?php
/**
 * Bach DataBagFactory unit tests
 *
 * PHP version 5
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\IndexationBundle\Tests\Units\Service;

use Bach\IndexationBundle\Service\DataBagFactory as Factory;
use Bach\IndexationBundle\Entity\Bag\XMLDataBag;
use atoum\AtoumBundle\Test\Units;

/**
 * Bach DataBagFactory unit tests
 *
 * PHP version 5
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class DataBagFactory extends Units\Test
{
    /**
     * Tests service instanciation
     *
     * @return void
     */
    public function testService()
    {
        $xml_str = '<?xml version="1.0"?><data>' .
            '<address loc="Paris"></address></data>';

        $xml = simplexml_load_string($xml_str);
        $tmpName = uniqid();

        $tmp_dir =  sys_get_temp_dir() . '/bach_tests_tmp/';
        if ( !file_exists($tmp_dir) ) {
            mkdir($tmp_dir);
        }

        $xml->asXML($tmp_dir . $tmpName . '.xml');

        $fileInfo = new \SplFileInfo(
            $tmp_dir . $tmpName . '.xml'
        );

        $factory = new Factory();
        $result = $factory->encapsulate($fileInfo);

        $is_instance = $result instanceof XMLDataBag;
        $this->boolean($is_instance)->isTrue();

        unlink($fileInfo->getRealPath());
    }
}
