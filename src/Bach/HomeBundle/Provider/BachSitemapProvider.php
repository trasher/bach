<?php
/**
 * Bach sitemap
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
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
namespace Bach\HomeBundle\Provider;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use SitemapGenerator\Provider\ProviderInterface;
use SitemapGenerator\Sitemap\Sitemap;
use SitemapGenerator\Entity\Url;

/**
 * Builds the whole sitemap.
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class BachSitemapProvider implements ProviderInterface
{
    protected $router;
    protected $em;
    protected $container;

    /**
     * Constructor
     *
     * @param Entitymanager      $em        Doctrine entity manager.
     * @param RouterInterface    $router    The application router.
     * @param ContainerInterface $container Application container
     */
    public function __construct(EntityManager $em, RouterInterface $router,
        ContainerInterface $container
    ) {
        $this->router = $router;
        $this->em = $em;
        $this->container = $container;
    }

    /**
     * Populate a sitemap using a Doctrine entity.
     *
     * @param Sitemap $sitemap The current sitemap.
     *
     * @return void
     */
    public function populate(Sitemap $sitemap)
    {
        //add home URL
        $url = new Url();
        $url->setLoc(
            $this->router->generate('bach_homepage')
        );
        $url->setLastmod(new \DateTime());
        $sitemap->add($url);

        //if enabled, add classification scheme URL
        if ( $this->container->getParameter('feature.cdc') === true ) {
            $url = new Url();
            //$href = $router->generate(
            $url->setLoc(
                $this->router->generate('bach_classification')
            );
            $sitemap->add($url);
        }

        //ead HTML documents
        $query = $this->em->createQuery(
            'SELECT h.headerId, h.updated '.
            'FROM BachIndexationBundle:EADHeader h'
        );
        $elements = $query->getResult();

        foreach ( $elements as $elt ) {
            $url = new Url();
            $url->setLoc(
                $this->router->generate(
                    'bach_ead_html',
                    array(
                        'docid' => $elt['headerId']
                    )
                )
            );
            $url->setLastmod($elt['updated']);
            $sitemap->add($url);
        }

        //add archives URLs
        $query = $this->em->createQuery(
            'SELECT f.fragmentid, f.updated FROM ' .
            'BachIndexationBundle:EADFileFormat f'
        );
        $elements = $query->getResult();

        foreach ( $elements as $elt ) {
            $url = new Url();
            $url->setLoc(
                $this->router->generate(
                    'bach_display_document',
                    array(
                        'docid' => $elt['fragmentid']
                    )
                )
            );
            $url->setLastmod($elt['updated']);
            $sitemap->add($url);
        }

        //if enabled, add matricules URLs
        if ( $this->container->getParameter('feature.matricules') === true ) {
            $url = new Url();
            $url->setLoc(
                $this->router->generate('bach_matricules')
            );
            $sitemap->add($url);

            //add matricules URLs
            $query = $this->em->createQuery(
                'SELECT m.id, m.updated FROM ' .
                ' BachIndexationBundle:MatriculesFileFormat m'
            );
            $elements = $query->getResult();
            foreach ( $elements as $elt ) {
                $url = new Url();
                $url->setLoc(
                    $this->router->generate(
                        'bach_display_matricules',
                        array(
                            'docid' => $elt['id']
                        )
                    )
                );
                $url->setLastmod($elt['updated']);
                $sitemap->add($url);
            }
        }

        //if enabled, add expos URLs
        /** TODO */
        /*if ( $this->container->getParameter('feature.expos') === true ) {
            $url = new Url();
            $url->setLoc(
                $this->router->generate('expos_homepage')
            );
        }*/

        //add browse URLs, if any
        if ( $this->container->getParameter('feature.browse') === true ) {
            $query = $this->em->createQuery(
                'SELECT b.solr_field_name FROM BachHomeBundle:BrowseFields b ' .
                'WHERE b.active=true ORDER BY b.position'
            );
            $elements = $query->getResult();
            if ( count($elements) > 0) {
                $url = new Url();
                $url->setLoc(
                    $this->router->generate('bach_browse')
                );

                foreach ( $elements as $elt ) {
                    $url = new Url();
                    $url->setLoc(
                        $this->router->generate(
                            'bach_browse',
                            array(
                                'part' => $elt['solr_field_name']
                            )
                        )
                    );
                    $sitemap->add($url);
                }
            }
        }

        //What else?
    }

}
