<?php
/**
 * Bach tag cloud management
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
 * Bach tag cloud management
 *
 * @ORM\Entity
 * @ORM\Table(name="tag_cloud")
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class TagCloud
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
     * @var integer
     *
     * @ORM\Column(name="number", type="integer")
     */
    protected $number;

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
     * Set active
     *
     * @param boolean $active Active
     *
     * @return TagCloud
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * Set number
     *
     * @param integer $number Number of occurences in cloud
     *
     * @return TagCloud
     */
    public function setNumber($number)
    {
        $this->number = $number;
        return $this;
    }

    /**
     * Get number
     *
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Load cloud configuration
     *
     * @param EntityManager $em Entity manager
     *
     * @return TagCloud
     */
    public function loadCloud($em)
    {
        $qb = $em->createQueryBuilder()
            ->add('select', 't')
            ->add('from', 'Bach\HomeBundle\Entity\TagCloud t');

        $query = $qb->getQuery();
        $results = $query->getResult();

        $tagcloud = null;
        if ( count($results) > 0 ) {
            return $results[0];
        } else {
            $this->setSolrFieldsNames(array());
            $this->setNumber(20);
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
        return _('Tag cloud');
    }
}
