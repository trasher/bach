<?php
/**
 * Bach default controller unit tests
 *
 * PHP version 5
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */


namespace Bach\HomeBundle\Tests\Units\Controller;

use atoum\AtoumBundle\Test\Units\WebTestCase;
use atoum\AtoumBundle\Tests\Controller\ControllerTest;

/**
 * Bach default controller unit tests
 *
 * PHP version 5
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class DefaultController extends WebTestCase
{
    /**
     * Test index action
     *
     * @return void
     */
    public function testIndex()
    {
        $this->request(
            array('debug' => true)
        )
            ->GET('/search/')
            ->hasStatus(404)
            ->GET('/search')
            ->hasStatus(200);

    }
}
