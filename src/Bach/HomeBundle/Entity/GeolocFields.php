<?php
/**
 * Bach geolocalization fields management
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
 * Bach geolocalization fields management
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
class GeolocFields
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var text
     *
     * @ORM\Column(name="solr_fields_names", type="text")
     */
    protected $solr_fields_names;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set solr_fields_names
     *
     * @param array $fields Fields names
     *
     * @return TagCloud
     */
    public function setSolrFieldsNames($fields)
    {
        $this->solr_fields_names = serialize($fields);
        return $this;
    }

    /**
     * Get solr_fields_names
     *
     * @return string
     */
    public function getSolrFieldsNames()
    {
        return unserialize($this->solr_fields_names);
    }

    /**
     * Load geoloc fields configuration
     *
     * @param EntityManager $em Entity manager
     *
     * @return GeolocFields
     */
    public function loadCloud($em)
    {
        $qb = $em->createQueryBuilder()
            ->add('select', 'gf')
            ->add('from', 'Bach\HomeBundle\Entity\GeolocFields gf');

        $query = $qb->getQuery();
        $results = $query->getResult();

        $tagcloud = null;
        if ( count($results) > 0 ) {
            return $results[0];
        } else {
            $this->setSolrFieldsNames(array('cGeogname'));
            $em->persist($this);
            $em->flush();
            return $this;
        }
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
