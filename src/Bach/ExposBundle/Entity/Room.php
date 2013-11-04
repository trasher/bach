<?php
/**
 * Bach expositions room
 *
 * PHP version 5
 *
 * @category Expos
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\ExposBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Exposition room
 *
 * @ORM\Table(name="expo_room")
 * @ORM\Entity(repositoryClass="Bach\ExposBundle\Entity\RoomRepository")
 *
 * @category Expos
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class Room
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=20, nullable=true)
     */
    protected $url;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var integer
     *
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer")
     */
    protected $position;

    /**
     * @ORM\ManyToOne(targetEntity="Exposition", inversedBy="rooms")
     * @ORM\JoinColumn(name="exposition_id", referencedColumnName="id")
     */
    protected $exposition;

    /**
     * @ORM\OneToMany(targetEntity="Panel", mappedBy="room", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    protected $panels;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->panels = new ArrayCollection();
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
     * Set name
     *
     * @param string $name Room name
     *
     * @return Room
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
     * Set url
     *
     * @param string $url Room URL
     *
     * @return Room
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set description
     *
     * @param string $description Room brief description
     *
     * @return Room
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Attach exposition
     *
     * @param Exposition $expo Exposition
     *
     * @return Room
     */
    public function setExposition(Exposition $expo)
    {
        $this->exposition = $expo;
        return $this;
    }

    /**
     * Get epxosition
     *
     * @return Exposition
     */
    public function getExposition()
    {
        return $this->exposition;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Add panel
     *
     * @param Panel $panel Panel
     *
     * @return Room
     */
    public function addPanel(Panel $panel)
    {
        $this->panels[] = $panel;
        return $this;
    }

    /**
     * Get panels
     *
     * @return ArrayCollection
     */
    public function getPanels()
    {
        return $this->panels;
    }

    /**
     * String representation
     *
     * @return string
     */
    public function __toString()
    {
        if ( $this->getName() ) {
            return $this->getName();
        } else {
            return _('New room');
        }
    }
}
