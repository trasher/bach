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
 */
abstract class FileFormat
{
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

}
