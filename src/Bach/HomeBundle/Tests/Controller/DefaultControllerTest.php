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
            ->withContent('Aucun résultat trouvé.')
            ->end()
            ->hasChild('#suggestions')
            ->hasChild('a')
            ->withContent('cayenne');

        //a successfull request, with filtering only
        $this->request->GET('/search?filter_field=cSubject&filter_value=enjeux+internationaux')
            ->hasStatus(200)
            ->hasCharset('UTF-8')
            ->crawler
            ->hasElement('#active_filters')
            ->hasChild('ul')->exactly(1)
            ->end()->end()
            ->hasElement('#search_results')
            ->hasChild('article');

        $this->request->GET('/search/Cayenne?view=thumbs')
            ->hasStatus(200)
            ->hasCharset('UTF-8')
            ->crawler
            ->hasElement('#search_results')
            ->hasChild('article.thumbs');

        $this->request->GET('/search/Cayenne?result_order=1')
            ->hasStatus(200)
            ->hasCharset('UTF-8')
            ->crawler
            ->hasElement('#search_results')
            ->hasChild('article');
    }

    /**
     * test facet listing
     *
     * @return void
     */
    public function testFacetList()
    {
        $this->request->GET('/fullfacet/*:*/cSubject/ajax')
            ->hasStatus(200)
            ->hasCharset('UTF-8')
            ->crawler
            ->hasElement('#facets_list');
    }
}
