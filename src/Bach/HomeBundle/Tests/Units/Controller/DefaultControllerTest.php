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
        $this->request()->GET('/search/')
            ->hasStatus(404);

        $this->request()->GET('/search')
            ->hasStatus(200)
            ->hasCharset('UTF-8')
            ->crawler
            ->hasElement('#tagCloud');

        //those ones will work with FRANCT_0001 EAD document indexed...
        //a successfull request
        $this->request->GET('/search/Cayenne')
            ->hasStatus(200)
            ->hasCharset('UTF-8')
            ->crawler
            ->hasElement('#search_results')
            ->hasChild('article');

        //a not successfull request, with one spelling suggestion
        $this->request->GET('/search/cyenne')
            ->hasStatus(200)
            ->hasCharset('UTF-8')
            ->crawler
            ->hasElement('#search_results')
            ->hasChild('p')
            ->withContent('Aucun résultat n\'a été trouvé.')
            ->end()
            ->hasChild('#suggestions')
            ->hasChild('a')
            ->withContent('cayenne');

    }
}
