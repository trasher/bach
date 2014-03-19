<?php
/**
 * Bach EAD header entity
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
use Doctrine\Common\NotifyPropertyChanged;
use Doctrine\Common\PropertyChangedListener;

/**
 * Bach EAD header entity
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\ChangeTrackingPolicy("NOTIFY")
 */
class EADHeader implements NotifyPropertyChanged
{
    private $_listeners = array();

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
    * @ORM\Column(type="string", nullable=true, length=100)
    */
    protected $headerId;

    /**
     * @ORM\Column(type="string", nullable=true, length=1000)
     */
    protected $headerTitle;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $headerSubtitle;

    /**
    * @ORM\Column(type="string", nullable=true, length=500)
    */
    protected $headerAuthor;

    /**
    * @ORM\Column(type="string", nullable=true, length=100)
    */
    protected $headerDate;

    /**
    * @ORM\Column(type="string", nullable=true, length=100)
    */
    protected $headerPublisher;

    /**
    * @ORM\Column(type="text", nullable=true)
    */
    protected $headerAddress;

    /**
    * @ORM\Column(type="string", nullable=true, length=3)
    */
    protected $headerLanguage;

    /**
     * @ORM\OneToMany(targetEntity="EADFileFormat", mappedBy="eadheader", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    protected $fragments;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
      * The constructor
      *
      * @param array $data Data
      */
    public function __construct($data)
    {
        $this->fragments = new ArrayCollection();
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
        $this->parseData($data);
    }

    /**
     * Adds a listener that wants to be notified about property changes.
     *
     * @param PropertyChangedListener $listener Listener
     *
     * @return void
     */
    public function addPropertyChangedListener(PropertyChangedListener $listener)
    {
        $this->_listeners[] = $listener;
    }

    /**
     * Proceed data parsing
     *
     * @param array $data Data
     *
     * @return void
     */
    protected function parseData($data)
    {
        foreach ($data as $key=>$value) {
            if (property_exists($this, $key)) {
                if ( $this->$key !== $value ) {
                    $this->onPropertyChanged($key, $this->$key, $value);
                    $this->$key = $value;
                }
            } else {
                throw new \RuntimeException(
                    __CLASS__ . ' - Key ' . $key . ' is not known!'
                );
            }
        }
    }

    /**
     * Notifies a property change
     *
     * @param string $propName Property name
     * @param string $oldValue Old value of property
     * @param string $newValue New value of property
     *
     * @return void
     */
    protected function onPropertyChanged($propName, $oldValue, $newValue)
    {
        if ( $this->_listeners) {
            foreach ( $this->_listeners as $listener ) {
                $listener->propertyChanged($this, $propName, $oldValue, $newValue);
            }
        }
        if ( $propName !== 'updated' ) {
            $now = new \DateTime();
            $this->onPropertyChanged('updated', $this->updated, $now);
            $this->updated = $now;
        }
    }

    /**
     * Hydrate existing entity
     *
     * @param array $data Data
     *
     * @return void
     */
    public function hydrate($data)
    {
        $this->parseData($data);
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
     * Set headerId
     *
     * @param string $headerId Header id
     *
     * @return UniversalFileFormat
     */
    public function setHeaderId($headerId)
    {
        $this->headerId = $headerId;

        return $this;
    }

    /**
     * Get headerId
     *
     * @return string
     */
    public function getHeaderId()
    {
        return $this->headerId;
    }

    /**
     * Set headerAuthor
     *
     * @param string $headerAuthor header author
     *
     * @return UniversalFileFormat
     */
    public function setHeaderAuthor($headerAuthor)
    {
        $this->headerAuthor = $headerAuthor;

        return $this;
    }

    /**
     * Get headerAuthor
     *
     * @return string
     */
    public function getHeaderAuthor()
    {
        return $this->headerAuthor;
    }

    /**
     * Set headerDate
     *
     * @param \DateTime $headerDate Header date
     *
     * @return UniversalFileFormat
     */
    public function setHeaderDate($headerDate)
    {
        $this->headerDate = $headerDate;
        return $this;
    }

    /**
     * Get headerDate
     *
     * @return \DateTime
     */
    public function getHeaderDate()
    {
        return $this->headerDate;
    }

    /**
     * Set headerPublisher
     *
     * @param string $headerPublisher Header publisher
     *
     * @return UniversalFileFormat
     */
    public function setHeaderPublisher($headerPublisher)
    {
        $this->headerPublisher = $headerPublisher;
        return $this;
    }

    /**
     * Get headerPublisher
     *
     * @return string
     */
    public function getHeaderPublisher()
    {
        return $this->headerPublisher;
    }

    /**
     * Set headerAddress
     *
     * @param string $headerAddress Header address
     *
     * @return UniversalFileFormat
     */
    public function setHeaderAddress($headerAddress)
    {
        $this->headerAddress = $headerAddress;
        return $this;
    }

    /**
     * Get headerAddress
     *
     * @return string
     */
    public function getHeaderAddress()
    {
        return $this->headerAddress;
    }

    /**
     * Get headerSubtitle
     *
     * @return string
     */
    public function getHeaderSubtitle()
    {
        return $this->headerSubtitle;
    }

    /**
     * Set headerSubtitle
     *
     * @param string $headerSubtitle Header subtitle
     *
     * @return UniversalFileFormat
     */
    public function setHeaderSubtitle($headerSubtitle)
    {
        $this->headerSubtitle = $headerSubtitle;
        return $this;
    }

    /**
     * Set headerLanguage
     *
     * @param string $headerLanguage Header language
     *
     * @return UniversalFileFormat
     */
    public function setHeaderLanguage($headerLanguage)
    {
        $this->headerLanguage = $headerLanguage;
        return $this;
    }

    /**
     * Get headerLanguage
     *
     * @return string
     */
    public function getHeaderLanguage()
    {
        return $this->headerLanguage;
    }

    /**
     * Set headerTitle
     *
     * @param string $headerTitle Title
     *
     * @return EADHeader
     */
    public function setHeaderTitle($headerTitle)
    {
        $this->headerTitle = $headerTitle;
        return $this;
    }

    /**
     * Get headerTitle
     *
     * @return string
     */
    public function getHeaderTitle()
    {
        return $this->headerTitle;
    }

    /**
     * Add fragment
     *
     * @param EADFileFormat $fragment EAD fragment
     *
     * @return EADHeader
     */
    public function addFragment(EADFileFormat $fragment)
    {
        $this->fragments[] = $fragment;
        return $this;
    }

    /**
     * Remove fragment
     *
     * @param EADFileFormat $fragment EAD fragment
     *
     * @return void
     */
    public function removeFragment(EADFileFormat $fragment)
    {
        $this->fragments->removeElement($fragment);
    }

    /**
     * Get fragments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFragments()
    {
        return $this->fragments;
    }

    /**
     * Set creation date
     *
     * @param DateTime $created Creation date
     *
     * @return Document
     */

    public function setCreated(\DateTime $created)
    {
        $this->created = $created;
        return $this;
    }

    /**
     * Get creation date
     *
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set modification date
     *
     * @param DateTime $updated Modification date
     *
     * @return Document
     */
    public function setUpdated(\DateTime $updated)
    {
        $this->updated = $updated;
        return $this;
    }

    /**
     * Get modification date
     *
     * @return DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }
}
