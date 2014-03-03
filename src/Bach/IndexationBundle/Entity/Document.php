<?php

/**
 * Document
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Anaphore PI Team <uknown@unknown.com>
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\IndexationBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Document
 *
 * @category Indexation
 * @package  Bach
 * @author   Anaphore PI Team <uknown@unknown.com>
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu

 * @ORM\Entity(repositoryClass="DocumentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Document
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     */
    protected $docid;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $path;

    /**
     * @Assert\File(
     *    maxSize = "10M"
     * )
     */
    protected $file;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $extension;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $corename;

    /**
     * @ORM\OneToOne(targetEntity="ArchFileIntegrationTask", mappedBy="document")
     */
    protected $task;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $uploaded = false;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
     * Constructor
     *
     * @param boolean $uploaded Is document uploaded from web interface?
     */
    public function __construct($uploaded = false)
    {
        $this->uploaded = $uploaded;
    }

    /**
     * Get absolute path to document
     *
     * @return string
     */
    public function getAbsolutePath()
    {
        $path = null;
        if ( $this->uploaded && $this->path !== null ) {
            $path = $this->_upload_dir . '/' . $this->path;
        } else if ( !$this->uploaded && $this->path !== null ) {
            $path = $this->_store_dir . '/' . $this->path;
        }
        return $path;
    }

    /**
     * Set upload directory. Also creates subdirectory if missing.
     *
     * @param string $dir Application upload directory
     *
     * @return Document
     */
    public function setUploadDir($dir)
    {
        $this->_upload_dir = $dir . '/published_docs';
        if ( !file_exists($this->_upload_dir) ) {
            $res = mkdir($this->_upload_dir);
            if ( $res !== true ) {
                throw new \RuntimeException(
                    str_replace(
                        '%dir',
                        $this->_upload_dir,
                        _('Cannot create upload directory %dir')
                    )
                );
            }
        }
        return $this;
    }

    /**
     * Prepare upload, set path and name
     *
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     *
     * @return void
     */
    public function preUpload()
    {
        if (null !== $this->file) {
            if ( $this->uploaded ) {
                $this->path = $this->file->getClientOriginalName();
                $this->name = $this->file->getClientOriginalName();
            } else {
                $this->path = str_replace(
                    $this->_store_dir,
                    '',
                    $this->file->getPathName()
                );
                $this->name = $this->file->getFileName();
            }
        } else {
            throw new \RuntimeException('No file specified. Cannot continue.');
        }
    }

    /**
     * Upload file
     *
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     *
     * @return void
     */
    public function upload()
    {
        if (null === $this->file) {
            return;
        }

        if ( $this->uploaded ) {
            $this->file->move($this->_upload_dir, $this->path);
        }

        unset($this->file);
    }

    /**
     * Remove uploaded file
     *
     * @ORM\PostRemove()
     *
     * @return void
     */
    public function removeUpload()
    {
        if ( $this->uploaded && $file = $this->getAbsolutePath()) {
            unlink($file);
        }
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
     * @param string $name Name
     *
     * @return Document
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
     * Set path
     *
     * @param string $path Path
     *
     * @return Document
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get file
     *
     * @return SplFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set file
     *
     * @param SplFile $file File
     *
     * @return Document
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * Get document extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set extension
     *
     * @param string $extension Document extension
     *
     * @return Document
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
        return $this;
    }

    /**
     * Get docid
     *
     * @return string
     */
    public function getDocId()
    {
        return $this->docid;
    }

    /**
     * Set docid
     *
     * @param string $docid document id
     *
     * @return Document
     */
    public function setDocId($docid)
    {
        $this->docid = $docid;
        return $this;
    }

    /**
     * Try to generate unique document id
     *
     * @ORM\PrePersist
     *
     * @return Document
     */
    public function generateDocId()
    {
        if ( $this->extension === 'ead' ) {
            $xml = simplexml_load_file($this->file->getPathName());
            $this->docid = (string)$xml->eadheader->eadid;
        }

        if ( $this->created === null ) {
            $this->created = new \DateTime();
        }

        if ( $this->updated === null ) {
            $this->updated = new \DateTime();
        }

        return $this;
    }

    /**
     * Get core name
     *
     * @return string
     */
    public function getCorename()
    {
        return $this->corename;
    }

    /**
     * Set core name
     *
     * @param string $corename Core name
     *
     * @return Document
     */
    public function setCorename($corename)
    {
        $this->corename = $corename;
        return $this;
    }

    /**
     * Set document as not uploaded
     *
     * @return void
     */
    public function setNotUploaded()
    {
        $this->uploaded = false;
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
     * Set storage directory for current document type
     *
     * @param string $dir Directory
     *
     * @return Document
     */
    public function setStoreDir($dir)
    {
        $this->_store_dir = $dir;
        return $this;
    }
}
