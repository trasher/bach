<?php

namespace Bach\IndexationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @UniqueEntity("document_id")
 */
class ArchFileIntegrationTask
{
    const STATUS_NONE = 0;
    const STATUS_OK = 1;
    const STATUS_KO = 2;

    /**
    * @ORM\Id
    * @ORM\Column(type="integer")
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    protected $taskId;

    /**
    * @ORM\Column(type="string", length=200, nullable=true)
    */
    protected $preprocessor;

    /**
    * @ORM\Column(type="integer", length=1)
    */
    protected $status;

    /**
     * @ORM\OneToOne(targetEntity="Document", inversedBy="task")
     * @ORM\JoinColumn(name="document_id", referencedColumnName="id")
     */
    protected $document;

    /**
     * The constructor
     *
     * @param Document $document Stored document
     */
    public function __construct($document)
    {
        $this->document = $document;
        $this->status = self::STATUS_NONE;
    }

    /**
     * Get taskId
     *
     * @return integer
     */
    public function getTaskId()
    {
        return $this->taskId;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->document->getName();
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return realpath($this->document->getAbsolutePath());
    }

    /**
     * Get format
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->document->getExtension();
    }

    /**
    * Set preprocessor
    *
    * @param string $preprocessor Pre processor
    *
    * @return ArchFileIntegrationTask
    */
    public function setPreprocessor($preprocessor)
    {
        $this->preprocessor = $preprocessor;
        return $this;
    }

    /**
     * Get preprocessor
     *
     * @return string
     */
    public function getPreprocessor()
    {
        return $this->preprocessor;
    }

    /**
     * Set status
     *
     * @param integer $status Stauts
     *
     * @return ArchFileIntegrationTask
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set associated document
     *
     * @param Document $document Document
     *
     * @return ArchFileIntegrationTask
     */
    public function setDocument($document)
    {
        $this->document = $document;
        return $this;
    }

    /**
     * Get associated document
     *
     * @return Document
     */
    public function getDocument()
    {
        return $this->document;
    }
}
