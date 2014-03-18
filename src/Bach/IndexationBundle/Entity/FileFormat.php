<?php
/**
 * Bach abstract file format
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
use Doctrine\Common\NotifyPropertyChanged;
use Doctrine\Common\PropertyChangedListener;
use Bach\IndexationBundle\Entity\Document;

/**
 * Bach abstract file format
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 *
 * @ORM\MappedSuperclass
 * @ORM\ChangeTrackingPolicy("NOTIFY")
 */
abstract class FileFormat implements NotifyPropertyChanged
{
    private $_listeners = array();

    /**
     * @ORM\ManyToOne(targetEntity="Document")
     * @ORM\JoinColumn(name="doc_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $document;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
     * Array of removed linked entities
     */
    protected $removed;

    /**
      * The constructor
      *
      * @param array $data The input data
      */
    public function __construct($data)
    {
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
     * @param array $data Data to parse
     *
     * @return void
     */
    protected function parseData($data)
    {
        foreach ($data as $key=>$datum) {
            if (property_exists($this, $key)) {
                $this->$key = $datum;
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
     * Set doc_id
     *
     * @param Document $document Document
     *
     * @return FileFormat
     */
    public function setDocument(Document $document = null)
    {
        $this->document = $document;
        return $this;
    }

    /**
     * Get document
     *
     * @return Document
     */
    public function getDocument()
    {
        return $this->document;
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

    /**
     * Get removed associated entities
     *
     * @return ArrayCollection
     */
    public function getRemoved()
    {
        return $this->removed;
    }
}
