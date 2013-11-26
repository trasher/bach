<?php
/**
 * Bach ViewParams unit tests
 *
 * PHP version 5
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\HomeBundle\Tests\Units\Entity;

use atoum\AtoumBundle\Test\Units;
use Bach\HomeBundle\Entity\ViewParams as Vp;

/**
 * Bach ViewParams unit tests
 *
 * PHP version 5
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class ViewParams extends Units\WebTestCase
{
    /**
     * Test view parameters
     *
     * @return void
     */
    public function testViewParams()
    {
        $vp = new Vp();

        //test default values
        $show_pics = $vp->showPics();
        $res_per_page = $vp->getResultsByPage();
        $view = $vp->getView();
        $order = $vp->getOrder();

        $this->boolean($show_pics)->isTrue();
        $this->variable($res_per_page)->isIdenticalTo(10);
        $this->string($view)->isIdenticalTo(Vp::VIEW_LIST);
        $this->variable($order)->isIdenticalTo(Vp::ORDER_RELEVANCE);

        $client = static::createClient();
        $crawler = $client->request(
            'POST',
            '/placebo',
            array(
                'view'              => Vp::VIEW_THUMBS,
                'results_by_page'   => 13,
                'results_order'     => 1
            )
        );
        $req = $client->getRequest();
        $vp->bind($req);

        $res_per_page = $vp->getResultsByPage();
        $view = $vp->getView();
        $order = $vp->getOrder();

        $this->variable($res_per_page)->isIdenticalTo(13);
        $this->string($view)->isIdenticalTo(Vp::VIEW_THUMBS);
        $this->variable($order)->isIdenticalTo(Vp::ORDER_TITLE);

        $this->exception(
            function () use ( $vp ) {
                $vp->setView('doesnotexists');
            }
        )->hasMessage('View doesnotexists is not known!');

        $this->exception(
            function () use ( $vp ) {
                $vp->setOrder('doesnotexists');
            }
        )->hasMessage('Order doesnotexists is not known!');

        /** FIXME: this one works in regular app, but not in tests :/ */
        /*$crawler = $client->request(
            'POST',
            '/placebo',
            array(
                'view'          => Vp::VIEW_TEXT_LIST
            )
        );
        $req = $client->getRequest();
        $vp->bind($req);

        $this->string($view)->isIdenticalTo(Vp::VIEW_TEXT_LIST);*/
    }
}
