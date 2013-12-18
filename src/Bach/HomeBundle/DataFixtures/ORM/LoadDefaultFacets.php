<?php
/**
 * Bach defaults facets fixture
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\HomeBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Bach\HomeBundle\Entity\Facets;

/**
 * Bach defaults facets fixture
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class MainDecorator implements FixtureInterface
{
    /**
     * Loads fixture
     *
     * @param ObjectManager $manager Object manager
     *
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        $defaults = array(
            array(
                'field'     => 'archDescUnitTitle',
                'fr_label'  => 'Document',
                'en_label'  => 'Document'
            ),
            array(
                'field'     => 'cSubject',
                'fr_label'  => 'Sujet',
                'en_label'  => 'Subject'
            ),
            array(
                'field'     => 'cPersname',
                'fr_label'  => 'Personne',
                'en_label'  => 'People'
            ),
            array(
                'field'     => 'cGeogname',
                'fr_label'  => 'Lieux',
                'en_label'  => 'Places'
            )
        );

        for ( $i = 0; $i < count($defaults); $i++ ) {
            $facet = new Facets();
            $data = $defaults[$i];
            $facet->setSolrFieldName($data['field']);
            $facet->setFrLabel($data['fr_label']);
            $facet->setEnLabel($data['en_label']);
            $facet->setActive(true);
            $facet->setPosition($i);
            $manager->persist($facet);
        }

        $manager->flush();
    }
}
