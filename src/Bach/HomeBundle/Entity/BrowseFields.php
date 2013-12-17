<?php
/**
 * Bach browsing fields
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
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Bach browsing fields management
 *
 * @ORM\Entity
 * @ORM\Table(name="browse_fields")
 * @ORM\HasLifecycleCallbacks()
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class BrowseFields
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
     * @var string
     *
     * @ORM\Column(name="solr_field_name", type="string", length=100)
     */
    protected $solr_field_name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    protected $active;

    /**
     * @var string
     *
     * @ORM\Column(name="fr_label", type="string", length=255)
     */
    protected $fr_label;

    /**
     * @var string
     *
     * @ORM\Column(name="en_label", type="string", length=255)
     */
    protected $en_label;

    /**
     * @var int
     *
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer")
     */
    protected $position;

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
     * Set solr_field_name
     *
     * @param string $solrFieldName Field name
     *
     * @return BrowseFields
     */
    public function setSolrFieldName($solrFieldName)
    {
        $this->solr_field_name = $solrFieldName;
        return $this;
    }

    /**
     * Get solr_field_name
     *
     * @return string
     */
    public function getSolrFieldName()
    {
        return $this->solr_field_name;
    }

    /**
     * Set active
     *
     * @param boolean $active Active
     *
     * @return BrowseFields
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set fr_label
     *
     * @param string $frLabel French label
     *
     * @return BrowseFields
     */
    public function setFrLabel($frLabel)
    {
        $this->fr_label = $frLabel;
        return $this;
    }

    /**
     * Get fr_label
     *
     * @return string
     */
    public function getFrLabel()
    {
        return $this->fr_label;
    }

    /**
     * Set en_label
     *
     * @param string $enLabel English label
     *
     * @return BrowseFields
     */
    public function setEnLabel($enLabel)
    {
        $this->en_label = $enLabel;
        return $this;
    }

    /**
     * Get en_label
     *
     * @return string
     */
    public function getEnLabel()
    {
        return $this->en_label;
    }

    /**
     * Retrieve localized facet label
     *
     * @param string $lang Language code
     *
     * @return string
     */
    public function getLabel($lang)
    {
        switch ($lang) {
        case 'fr':
        case 'fr_FR':
            return $this->getFrLabel();
            break;
        case 'en':
        case 'en_US':
        default:
            return $this->getEnLabel();
            break;
        }
    }

    /**
     * Set position
     *
     * @param int $position Facet position
     *
     * @return BrowseFields
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * String representation
     *
     * @return string
     */
    public function __toString()
    {
        if ( $this->getSolrFieldName() ) {
            return $this->getSolrFieldName();
        } else {
            return _('New browsing field');
        }
    }
}

