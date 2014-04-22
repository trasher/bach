<?php
/**
 * Bach defaults browse fields fixture
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
use Bach\HomeBundle\Entity\BrowseFields;

/**
 * Bach defaults browse fields fixture
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class LoadDefaultBrowseFields implements FixtureInterface
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
                'field'     => 'cSubject',
                'fr_label'  => 'Sujets',
                'en_label'  => 'Subject'
            ),
            array(
                'field'     => 'cPersname',
                'fr_label'  => 'Personnes',
                'en_label'  => 'People'
            ),
            array(
                'field'     => 'cGeogname',
                'fr_label'  => 'Lieux',
                'en_label'  => 'Places'
            )
        );

        for ( $i = 0; $i < count($defaults); $i++ ) {
            $fields = new BrowseFields();
            $data = $defaults[$i];
            $fields->setSolrFieldName($data['field']);
            $fields->setFrLabel($data['fr_label']);
            $fields->setEnLabel($data['en_label']);
            $fields->setActive(true);
            $fields->setPosition($i);
            $manager->persist($fields);
        }

        $manager->flush();
    }
}
