<?php
/**
 * Bach geolocalisation informations
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
namespace Bach\IndexationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Bach geolocalisation informations
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 *
 * @ORM\Entity
 * @ORM\Table(name="geoloc")
 */
class Geoloc
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
     * @ORM\Column(name="place_id", type="integer")
     */
    protected $place_id;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=50)
     */
    protected $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="osm_id", type="integer")
     */
    protected $osm_id;

    /**
     * @var text
     *
     * @ORM\Column(name="bbox", type="text")
     */
    protected $bbox;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="indexed_name", type="string", length=255, unique=true)
     */
    protected $indexed_name;

    /**
     * @var text
     *
     * @ORM\Column(name="geojson", type="text")
     */
    protected $geojson;

    /**
     * @var string
     *
     * @ORM\Column(name="lat", type="decimal", scale=12, precision=18)
     */
    protected $lat;

    /**
     * @var string
     *
     * @ORM\Column(name="lon", type="decimal", scale=12, precision=18)
     */
    protected $lon;

    /**
      * The constructor
      *
      * @param string           $toponym Toponym
      * @param SimpleXMLElement $data    Result from Nominatim
      */
    public function __construct(Toponym $toponym, $data)
    {
        $this->indexed_name = $toponym->getOriginal();
        if ( $toponym->getName() !== null ) {
            $this->name = $toponym->getName();
        } else {
            $this->name = $toponym->getSpecificName();
        }
        $this->place_id = (string)$data['place_id'];
        $this->type = (string)$data['type'];
        $this->osm_id = (string)$data['osm_id'];
        $this->bbox = (string)$data['boundingbox'];
        $this->geojson = (string)$data['geojson'];
        $this->lat = (string)$data['lat'];
        $this->lon = (string)$data['lon'];
    }

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
     * Set place_id
     *
     * @param integer $placeId Place id from OSM
     *
     * @return Geoloc
     */
    public function setPlaceId($placeId)
    {
        $this->place_id = $placeId;
        return $this;
    }

    /**
     * Get place_id
     *
     * @return integer
     */
    public function getPlaceId()
    {
        return $this->place_id;
    }

    /**
     * Set type
     *
     * @param string $type OSM Type
     *
     * @return Geoloc
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set osm_id
     *
     * @param integer $osmId OSM Id
     *
     * @return Geoloc
     */
    public function setOsmId($osmId)
    {
        $this->osm_id = $osmId;
        return $this;
    }

    /**
     * Get osm_id
     *
     * @return integer
     */
    public function getOsmId()
    {
        return $this->osm_id;
    }

    /**
     * Set bbox
     *
     * @param string $bbox Bounding box
     *
     * @return Geoloc
     */
    public function setBbox($bbox)
    {
        $this->bbox = $bbox;
        return $this;
    }

    /**
     * Get bbox
     *
     * @return string
     */
    public function getBbox()
    {
        return $this->bbox;
    }

    /**
     * Set name
     *
     * @param string $name Place name
     *
     * @return Geoloc
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set indexed_name
     *
     * @param string $indexed_name Place name as indexed
     *
     * @return Geoloc
     */
    public function setIndexedName($indexed_name)
    {
        $this->indexed_name = $indexed_name;
        return $this;
    }

    /**
     * Get indexed_name
     *
     * @return string
     */
    public function getIndexedName()
    {
        return $this->indexed_name;
    }

    /**
     * Set geojson
     *
     * @param string $geojson Geojson polygon
     *
     * @return Geoloc
     */
    public function setGeojson($geojson)
    {
        $this->geojson = $geojson;
        return $this;
    }

    /**
     * Get geojson
     *
     * @return string
     */
    public function getGeojson()
    {
        return $this->geojson;
    }

    /**
     * Set lat
     *
     * @param string $lat Latitude
     *
     * @return Geoloc
     */
    public function setLat($lat)
    {
        $this->lat = $lat;
        return $this;
    }

    /**
     * Get lat
     *
     * @return string
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lon
     *
     * @param string $lon Longitude
     *
     * @return Geoloc
     */
    public function setLon($lon)
    {
        $this->lon = $lon;
        return $this;
    }

    /**
     * Get lon
     *
     * @return string
     */
    public function getLon()
    {
        return $this->lon;
    }
}
