<?php
/**
 * Bach EAD File Format entity
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
 * Bach Matricules File Format entity
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 *
 * @ORM\Entity
 * @ORM\Table(name="MatriculesFileFormat")
 */
class MatriculesFileFormat
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=10)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $uniqid;

    /**
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    protected $cote;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $date_enregistrement;

    /**
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    protected $lieu_enregistrement;

    /**
     * @ORM\Column(type="date")
     */
    protected $classe;

    /**
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    protected $nom;

    /**
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    protected $prenoms;

    /**
     * @ORM\Column(type="string", nullable=true, length=10)
     */
    protected $matricule;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $annee_naissance;

    /**
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    protected $lieu_naissance;

    /**
     * @ORM\Column(type="string", nullable=true, length=500)
     */
    protected $start_dao;

    /**
     * @ORM\Column(type="string", nullable=true, length=500)
     */
    protected $end_dao;

    /**
     * @ORM\ManyToOne(targetEntity="Document")
     * @ORM\JoinColumn(name="doc_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $doc_id;

    /**
     * The constructor
     *
     * @param array $data The input data
     */
    public function __construct($data)
    {
        $this->parseData($data);
    }

    /**
     * Fields that will be excluded from fulltext field
     */
    public static $nonfulltext = array(
        'uniqid',
        'cote',
        'date_enregistrement',
        'classe',
        'matricule',
        'annee_naissance',
        'start_dao',
        'end_dao'
    );

    /**
     * Fields types, if not string
     */
    public static $types = array(
        'date_enregistrement'   => 'date',
        'annee_naissance'       => 'date',
        'classe'                => 'date',
        'matricule'             => 'int'
    );

    /**
     * Fields that should not be used for facetting
     */
    public static $facet_excluded = array(
        '_version_',
        'txt_nom',
        'txt_prenoms',
        'fulltext',
        'suggestions'
    );

    /**
     * Expanded fields mappings
     */
    public static $expanded_mappings = array(
        array(
            'source'        => 'nom',
            'dest'          => 'txt_nom',
            'type'          => 'text_names',
            'multivalued'   => 'false',
            'indexed'       => 'true',
            'stored'        => 'true'
        ),
        array(
            'source'        => 'prenoms',
            'dest'          => 'txt_prenoms',
            'type'          => 'text_names',
            'multivalued'   => 'true',
            'indexed'       => 'true',
            'stored'        => 'true'
        )
    );

    /**
     * Proceed data parsing
     *
     * @param array $data Data
     *
     * @return void
     */
    protected function parseData($data)
    {
        foreach ($data as $key=>$datum) {
            if (property_exists($this, $key)) {
                if ( strlen($datum[0]['value']) == 4
                    && ($key === 'date_enregistrement'
                    || $key === 'annee_naissance'
                    || $key === 'classe')
                ) {
                    $datum[0]['value'] = new \DateTime(
                        $datum[0]['value'] . '-01-01'
                    );
                }
                $this->$key = $datum[0]['value'];
            } else {
                throw new \RuntimeException(
                    __CLASS__ . ' - Key ' . $key . ' is not known!'
                );
            }
        }
    }

    /**
     * Get uniqid
     *
     * @return integer
     */
    public function getUniqid()
    {
        return $this->uniqid;
    }

    /**
     * Set cote
     *
     * @param string $cote Cote
     *
     * @return MatriculesFileFormat
     */
    public function setCote($cote)
    {
        $this->cote = $cote;
        return $this;
    }

    /**
     * Get cote
     *
     * @return string
     */
    public function getCote()
    {
        return $this->cote;
    }

    /**
     * Set date_enregistrement
     *
     * @param string $dateEnregistrement Date
     *
     * @return MatriculesFileFormat
     */
    public function setDateEnregistrement($dateEnregistrement)
    {
        $this->date_enregistrement = $dateEnregistrement;
        return $this;
    }

    /**
     * Get date_enregistrement
     *
     * @return string
     */
    public function getDateEnregistrement()
    {
        return $this->date_enregistrement;
    }

    /**
     * Set lieu_enregistrement
     *
     * @param string $lieuEnregistrement Place
     *
     * @return MatriculesFileFormat
     */
    public function setLieuEnregistrement($lieuEnregistrement)
    {
        $this->lieu_enregistrement = $lieuEnregistrement;
        return $this;
    }

    /**
     * Get lieu_enregistrement
     *
     * @return string
     */
    public function getLieuEnregistrement()
    {
        return $this->lieu_enregistrement;
    }

    /**
     * Set classe
     *
     * @param string $classe Classe
     *
     * @return MatriculesFileFormat
     */
    public function setClasse($classe)
    {
        $this->classe = $classe;
        return $this;
    }

    /**
     * Get classe
     *
     * @return string
     */
    public function getClasse()
    {
        return $this->classe;
    }

    /**
     * Set nom
     *
     * @param string $nom Name
     *
     * @return MatriculesFileFormat
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenoms
     *
     * @param string $prenoms Surnames
     *
     * @return MatriculesFileFormat
     */
    public function setPrenoms($prenoms)
    {
        $this->prenoms = $prenoms;
        return $this;
    }

    /**
     * Get prenoms
     *
     * @return string
     */
    public function getPrenoms()
    {
        return $this->prenoms;
    }

    /**
     * Set matricule
     *
     * @param string $matricule Matricule
     *
     * @return MatriculesFileFormat
     */
    public function setMatricule($matricule)
    {
        $this->matricule = $matricule;
        return $this;
    }

    /**
     * Get matricule
     *
     * @return string 
     */
    public function getMatricule()
    {
        return $this->matricule;
    }

    /**
     * Set annee_naissance
     *
     * @param string $anneeNaissance Year of birth
     *
     * @return MatriculesFileFormat
     */
    public function setAnneeNaissance($anneeNaissance)
    {
        $this->annee_naissance = $anneeNaissance;
        return $this;
    }

    /**
     * Get annee_naissance
     *
     * @return string
     */
    public function getAnneeNaissance()
    {
        return $this->annee_naissance;
    }

    /**
     * Set lieu_naissance
     *
     * @param string $lieuNaissance Place of birth
     *
     * @return MatriculesFileFormat
     */
    public function setLieuNaissance($lieuNaissance)
    {
        $this->lieu_naissance = $lieuNaissance;
        return $this;
    }

    /**
     * Get lieu_naissance
     *
     * @return string
     */
    public function getLieuNaissance()
    {
        return $this->lieu_naissance;
    }

    /**
     * Set doc_id
     *
     * @param Document $docId Document id
     *
     * @return MatriculesFileFormat
     */
    public function setDocId(Document $docId = null)
    {
        $this->doc_id = $docId;
        return $this;
    }

    /**
     * Get doc_id
     *
     * @return Document
     */
    public function getDocId()
    {
        return $this->doc_id;
    }
}
