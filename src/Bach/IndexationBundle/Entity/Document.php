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
use Doctrine\Common\Collections\ArrayCollection;
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
     * Get absolute path to document
     *
     * @return string
     */
    public function getAbsolutePath()
    {
        $path = null;
        if ( $this->path !== null ) {
            $path = $this->getUploadRootDir() . '/' . $this->path;
        }
        return $path;
    }

    /**
     * Retrieve upload dir absolute path
     *
     * FIXME: parametize!
     *
     * @return string
     */
    protected function getUploadRootDir()
    {
        return __DIR__ . '/../../../../web/uploads' . '/documents';
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
            $this->path = sha1(uniqid(mt_rand(), true)) . '.' .
                $this->file->guessExtension();
             $this->name = $this->file->getClientOriginalName();
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

        // s'il y a une erreur lors du déplacement du fichier, une exception
        // va automatiquement être lancée par la méthode move(). Cela va empêcher
        // proprement l'entité d'être persistée dans la base de données si
        // erreur il y a
        $this->file->move($this->getUploadRootDir(), $this->path);

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
        if ( $file = $this->getAbsolutePath()) {
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
     * @return void
     */
    public function setFile($file)
    {
        $this->file = $file;
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
}
