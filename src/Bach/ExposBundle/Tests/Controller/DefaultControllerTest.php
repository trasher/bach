<?php
/**
 * Bach virtual expositions controller unit tests
 *
 * PHP version 5
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\ExposBundle\Tests\Controller;

use atoum\AtoumBundle\Test\Units\WebTestCase;
use atoum\AtoumBundle\Test\Controller\ControllerTest;

/**
 * Bach virtual expositions controller unit tests
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class DefaultController extends ControllerTest
{
    /**
     * Test index action
     *
     * @return void
     */
    public function testIndex()
    {
        $this->request()->GET('/search/')
            ->hasStatus(404);

        //a successfull request, with filtering only
        /*$this->request->GET('/search?filter_field=cSubject&filter_value=enjeux+internationaux')
            ->hasStatus(200)
            ->hasCharset('UTF-8')
            ->crawler
            ->hasElement('#active_filters')
            ->hasChild('ul')->exactly(1)
            ->end()->end()
            ->hasElement('#search_results')
            ->hasChild('article');*/

    }
}
