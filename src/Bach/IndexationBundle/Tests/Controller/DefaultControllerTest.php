<?php
/**
 * Bach indexation default controller unit tests
 *
 * PHP version 5
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\IndexationBundle\Tests\Units\Controller;

use atoum\AtoumBundle\Test\Units\WebTestCase;
use atoum\AtoumBundle\Tests\Controller\ControllerTest;

/**
 * Bach indexation default controller unit tests
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
     * Test indexAction
     *
     * @return void
     */
    public function testIndex()
    {
        $this->request()->GET('/notexists/')
            ->hasStatus(404);

        /*$this->request()->GET('/indexation')
            ->hasStatus(200)
            ->hasCharset('UTF-8')
            ->crawler
            ->hasElement('form#documents');*/
    }

    /**
     * Test add action
     *
     * @return void
     */
    /*public function testAdd()
    {
        $this->request()->GET('/indexation/add')
            ->hasStatus(200)
            ->hasCharset('UTF-8')
            ->crawler
            ->hasElement('body')
            ->hasChild('fieldset')->exactly(2)
            ->end()->end()
            ->hasElement('#form_file')
            ->end()
            ->hasElement('#form_extension');
    }*/

    /**
     * Test queue action
     *
     * @return void
     */
    /*public function testQueue()
    {
        $this->request()->GET('/indexation/queue')
            ->hasStatus(200)
            ->hasCharset('UTF-8')
            ->crawler
            ->hasElement('body')
            ->hasChild('table')->exactly(1);
    }*/
}

