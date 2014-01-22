<?php
/**
 * Bach geolocalization fields management for main
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\HomeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Bach geolocalization fields management for main
 *
 * @ORM\Entity
 * @ORM\Table(name="geoloc_fields")
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class GeolocMainFields extends GeolocFields
{

    /**
     * Get query builder
     *
     * @param EntityManager $em Entity manager
     *
     * @return QueryBuilder
     */
    protected function getQueryBuilder($em)
    {
        $qb = $em->createQueryBuilder()
            ->add('select', 'gf')
            ->add('from', 'Bach\HomeBundle\Entity\GeolocMainFields gf');
        return $qb;
    }

    /**
     * Get default fields
     *
     * @return array
     */
    protected function getDefaultFields()
    {
        return array('cGeogname');
    }

    /**
     * String representation
     *
     * @return string
     */
    public function __toString()
    {
        return _('Geolocalization fields');
    }
}
