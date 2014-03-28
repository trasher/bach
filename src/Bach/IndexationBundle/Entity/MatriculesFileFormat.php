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
class MatriculesFileFormat extends FileFormat
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=10)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $uniqid;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $id;

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
     * Extra fields not in database
     */
    public static $extra_fields = array(
        'txt_prenoms' => 'prenoms_full'
    );

    /**
     * Fields that are mutlivalued
     */
    public static $multivalued = array(
        'prenoms'
    );

    /**
     * Fields that will be excluded from fulltext field
     */
    public static $nonfulltext = array(
        'uniqid',
        'id',
        'cote',
        'date_enregistrement',
        'classe',
        'matricule',
        'annee_naissance',
        'start_dao',
        'end_dao',
        'txt_prenoms'
    );

    /**
     * Fields included in spell field
     */
    public static $spellers = array(
        'nom',
        'prenoms',
        'lieu_enregistrement',
        'lieu_naissance'
    );

    /**
     * Fields included in suggestions field
     */
    public static $suggesters = array(
        'nom',
        'prenoms',
        'lieu_enregistrement',
        'lieu_naissance'
    );

    /**
     * Fields types, if not string
     */
    public static $types = array(
        'date_enregistrement'   => 'date',
        'annee_naissance'       => 'date',
        'classe'                => 'date',
        'matricule'             => 'int',
        'txt_prenoms'           => 'text_names'
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
        )
    );

    public static $dataconfig_attrs = array(
        'prenoms' => array(
            'splitBy' => ' '
        ),
        'txt_prenoms' => array(
            'source' => 'prenoms_full'
        )
    );

    public static $qry_fields = array(
        'prenoms_full' => 'prenoms'
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
            $has_changed = false;
            if (property_exists($this, $key)) {
                if ( isset($datum[0]) ) {
                    $value = $datum[0]['value'];
                    if ( $key === 'date_enregistrement'
                        || $key === 'annee_naissance'
                        || $key === 'classe'
                    ) {
                        if ( strlen($value) === 4 ) {
                            $value = new \DateTime(
                                $value . '-01-01'
                            );
                            if ( !$this->$key ) {
                                $has_changed = true;
                            } else {
                                $has_changed = $this->$key->format('Y-m-d')
                                    !== $value->format('Y-m-d');
                            }
                        } else {
                            //invalid year
                            $value = null;
                        }
                    } else {
                        $has_changed = ($this->$key !== $value);
                    }
                    if ( $has_changed ) {
                        $this->onPropertyChanged($key, $this->$key, $value);
                        $this->$key = $value;
                    }
                }
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
        if ( $this->cote !== $cote ) {
            $this->onPropertyChanged('cote', $this->cote, $cote);
            $this->cote = $cote;
        }
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
     * @param DateTime $dateEnregistrement Recording date
     *
     * @return MatriculesFileFormat
     */
    public function setDateEnregistrement(\DateTime $dateEnregistrement)
    {
        $old = null;
        if ( $this->date_enregistrement !== null ) {
            $old = $this->date_enregistrement->format('Y-m-d');
        }
        $new = $dateEnregistrement->format('Y-m-d');
        if ( $old !== $new ) {
            $this->onPropertyChanged(
                'date_enregistrement',
                $this->date_enregistrement,
                $dateEnregistrement
            );
            $this->date_enregistrement = $dateEnregistrement;
        }
        return $this;
    }

    /**
     * Get date_enregistrement
     *
     * @return DateTime
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
        if ( $this->lieu_enregistrement !== $lieuEnregistrement ) {
            $this->onPropertyChanged(
                'lieu_enregistrement',
                $this->lieu_enregistrement,
                $lieuEnregistrement
            );
            $this->lieu_enregistrement = $lieuEnregistrement;
        }
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
     * @param DateTime $classe Classe
     *
     * @return MatriculesFileFormat
     */
    public function setClasse(\DateTime $classe)
    {
        $old = null;
        if ( $this->classe !== null ) {
            $old = $this->classe->format('Y-m-d');
        }
        $new = $classe->format('Y-m-d');
        if ( $old !== $new ) {
            $this->onPropertyChanged(
                'classe',
                $this->classe,
                $classe
            );
            $this->classe = $classe;
        }
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
        if ( $this->nom !== $nom ) {
            $this->onPropertyChanged(
                'nom',
                $this->nom,
                $nom
            );
            $this->nom = $nom;
        }
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
        if ( $this->prenoms !== $prenoms ) {
            $this->onPropertyChanged(
                'prenoms',
                $this->prenoms,
                $prenoms
            );
            $this->prenoms = $prenoms;
        }
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
        if ( $this->matricule !== $matricule ) {
            $this->onPropertyChanged(
                'matricule',
                $this->matricule,
                $matricule
            );
            $this->matricule = $matricule;
        }
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
     * @param DateTime $anneeNaissance Year of birth
     *
     * @return MatriculesFileFormat
     */
    public function setAnneeNaissance(\DateTime $anneeNaissance)
    {
        $old = null;
        if ( $this->annee_naissance !== null ) {
            $old = $this->annee_naissance->format('Y-m-d');
        }
        $new = $anneeNaissance->format('Y-m-d');
        if ( $old !== $new ) {
            $this->onPropertyChanged(
                'annee_naissance',
                $this->annee_naissance,
                $anneeNaissance
            );
            $this->annee_naissance = $anneeNaissance;
        }
        return $this;
    }

    /**
     * Get annee_naissance
     *
     * @return DateTime
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
        if ( $this->lieu_naissance !== $lieuNaissance ) {
            $this->onPropertyChanged(
                'lieu_naissance',
                $this->lieu_naissance,
                $lieuNaissance
            );
            $this->lieu_naissance = $lieuNaissance;
        }
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

}
