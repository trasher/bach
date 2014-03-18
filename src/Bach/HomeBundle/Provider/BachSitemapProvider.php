<?php
/**
 * Bach sitemap
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
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
 * @license  Unknown http://unknown.com
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
        $root = $this->container->getParameter('bach_url');
        //add home URL
        $url = new Url();
        $url->setLoc(
            $this->router->generate('bach_homepage')
        );
        $url->setLastmod(new \DateTime());
        $sitemap->add($url);

        //if enabled, add classification scheme URL
        if ( $this->container->getParameter('has_cdc') === true ) {
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
        if ( $this->container->getParameter('has_matricules') === true ) {
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
                /** FIXME: not the good URL (it does not exists yet) */
                $url->setLoc(
                    $this->router->generate(
                        'bach_display_document',
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
        /*if ( $this->container->getParameter('show_expos') === true ) {
            $url = new Url();
            $url->setLoc(
                $this->router->generate('expos_homepage')
            );
        }*/

        //add browse URLs, if any
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

        //What else?
    }

}
