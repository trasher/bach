<?php
/**
 * Bach PMB File Format entity
 *
 * PHP version 5
 *
 * Copyright (c) 2014, Anaphore
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are
 * met:
 *
 *     (1) Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *     (2) Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *     (3)The name of the author may not be used to
 *    endorse or promote products derived from this software without
 *    specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT,
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
 * STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING
 * IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category Indexation
 * @package  Bach
 * @author   Vincent Fleurette <vincent.fleurette@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
namespace Bach\IndexationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Bach PMB File Format entity
 *
 * @category Indexation
 * @package  Bach
 * @author   Vincent Fleurette <vincent.fleurette@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 *
 * @ORM\Entity
 * @ORM\Table(name="pmb_file_format")
 */

class PMBFileFormat extends FileFormat
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=10)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $uniqid;
    /**
     * @ORM\Column(type="string")
     */
    protected $idNotice;
    /**
     * @ORM\Column(type="string")
     */
    protected $title_proper;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $clean_title_author_different;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $parallel_title;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $title_complement;

    /**
     * @ORM\Column(type="string")
     */
    protected $cod_unimarc;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $part_of;

    /**
     * @ORM\Column(type="string", nullable=true, length=50)
     */
    protected $part_num;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $editor;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $collection;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $numcollection;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $subcollection;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $year;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $mention_edition;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $other_editor;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $isbn;

    /**
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    protected $material_importance;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $other_physical_characteristics;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $format;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $price;

    /**
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    protected $material_support;

    /**
     * @ORM\Column(type="text", nullable=true, length=1000)
     */
    protected $note_general;

    /**
     * @ORM\Column(type="text", nullable=true, length=1000)
     */
    protected $textcontent;

    /**
     * @ORM\Column(type="text", nullable=true, length=1000)
     */
    protected $extract;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $indexing_decimal;

    /**
     * @ORM\Column(type="text", nullable=true, length=1000)
     */
    protected $keyword;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $link_ressource_elect;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $format_elect_ressource;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $urlimg;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $fragment;

    /**
     * @ORM\OneToMany(targetEntity="PMBTitle", mappedBy="pmbfile", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    protected $title;

    /**
     * @ORM\OneToMany(targetEntity="PMBAuthor", mappedBy="pmbfile", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    protected $authors;

    /**
     * @ORM\OneToMany(targetEntity="PMBCategory", mappedBy="pmbfile", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    protected $category;

    /**
     * @ORM\OneToMany(targetEntity="PMBLanguage", mappedBy="pmbfile", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    protected $language;

    /**
     * Extra fields not in database
     */
    public static $extra_entities = array(
        'authors'  => 'name',
        'category' => 'category',
        'language' => 'content'
    );
    /**
     * Dynamic descriptors discover
     *
     * array(
     *  db column name not null => solr function called (defined in schema.xml)
     * )
     */
    public static $dynamic_descriptors = array(
        'type_auth'     => 'makeAuthorsDynamics',
        'function' => 'makeAuthorsFuncDynamics'
    );
    /**
     * Fields that will be excluded from fulltext field
     */
    public static $nonfulltext = array(
        'uniqid',
        'idNotice',
        'year',
        'created',
        'updated'
    );

    /**
     * Fields that are mutlivalued
     */
    public static $multivalued = array(
        'title',
        'authors',
        'category',
        'language'
    );

    /**
     * Fields included in spell field
     */
    public static $spellers = array(
        'title_proper',
        'indexing_decimal',
        'editor',
        'authors',
        'category',
        'language',
        'collection'
    );

    /**
     * Fields included in suggestions field
     */
    public static $suggesters = array(
        'title_proper',
        'indexing_decimal',
        'editor',
        'authors',
        'category',
        'language',
        'collection'
    );

    /**
     * Fields types, if not string
     */
    public static $types = array(
        'title_proper'           => 'text',
        'indexing_decimal'    => 'text',
        'editor'                => 'text',
        'collection'             => 'text',
        'title'                  => 'text',
        'authors'                => 'text',
        'category'               => 'text',
        'language'               => 'text',
        'fulltext'               => 'text'
    );

    /**
     * The constructor
     *
     * @param array $data The input data
     */
    public function __construct($data)
    {
        $this-> authors  = new ArrayCollection();
        $this-> category = new ArrayCollection();
        $this-> language = new ArrayCollection();
        $this-> title    = new ArrayCollection();
        parent::__construct($data);
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
            if ( $key === 'authors' ) {
                $this->parseAuthors($value);
            } else if ( $key === 'category' ) {
                $this->parseCategory($value);
            } else if ($key === 'language' ) {
                $this->parseLanguage($value);
            } else if ( $key == 'title' ) {
                $this->parseTitle($value);
            } else if (property_exists($this, $key)) {
                if ( isset($value[0])) {
                    if ( $key == 'year' ) {
                        try {
                            $year = new \DateTime(
                                $value[0]['value'] . '-01-01'
                            );
                            if ( $this->$key === null || $this->$key->format('Y') !== $year->format('Y') ) {
                                $this->onPropertyChanged($key, $this->$key, $year);
                                $this->$key = $year;
                            }
                        } catch ( \Exception $e ) {
                        }
                    } else if ( $this->$key !== $value[0]['value'] ) {
                            $this->onPropertyChanged($key, $this->$key, $value[0]['value']);
                            $this->$key = $value[0]['value'];
                    }
                }
            } else {
                throw new \RuntimeException("Missing property for entry " . $key);
            }
        }
    }


    /**
     * Parse authors objects from bag
     *
     * @param array $data Authors data
     *
     * @return void
     */
    protected function parseAuthors($data)
    {
        $authors = clone $this->authors;
        $has_changed = false;
        //check for removal
        foreach ( $this->authors as $author ) {
            $found = false;
            foreach ($data as $entry) {
                if ( $author->getName() === $entry['value']
                    && $author->getTypeAuth() === $entry['attributes']['type']
                    && $author->getFunction() === PMBAuthor::convertCodeFunction($entry['attributes']['function'])
                ) {
                    $found = true;
                    break;
                }
            }
            if ( !$found ) {
                $this->removeAutor($author);
                $this->removed[] = $author;
                $has_changed = true;
            }
        }
        //check for new
        foreach ( $data as $entry ) {
            $unique = true;
            foreach ( $this->authors as $i ) {
                if ( $i->getName() == $entry['value']
                    && $i->getTypeAuth() == $entry['attributes']['type']
                    && $i->getFunction() == PMBAuthor::convertCodeFunction($entry['attributes']['function'])
                ) {
                    $unique = false;
                    break;
                }
            }

            if ( $unique === true ) {
                $newauthor = new PMBAuthor(
                    $entry['attributes']['type'],
                    $entry['value'],
                    $entry['attributes']['function'],
                    $this
                );
                $this->addAuthor($newauthor);
                $has_changed = true;
            }
        }

        //notify if something has changed
        if ( $has_changed ) {
            $this->onPropertyChanged('authors', $authors, $this->authors);
        }
    }

    /**
     * Parse category objects from bag
     *
     * @param array $data Category data
     *
     * @return void
     */
    protected function parseCategory($data)
    {
        $category = clone $this->category;
        $has_changed = false;
        //check for removal
        foreach ( $this->category as $category ) {
            $found = false;
            foreach ($data as $value) {
                if ( $category->getCategory() === $value['value']) {
                    $found = true;
                    break;
                }
            }
            if ( !$found ) {
                $this->removeCategory($category);
                $this->removed[] = $category;
                $has_changed = true;
            }
        }
        //check for new
        foreach ( $data as $value ) {
            $unique = true;
            foreach ( $this->category as $i ) {
                if ( $i->getCategory() == $value['value'] ) {
                    $unique = false;
                    break;
                }
            }

            if ( $unique === true ) {
                $result = new PMBCategory($value['value'], $this);
                $this->addCategory($result);
                $has_changed = true;
            }
        }

        //notify if something has changed
        if ( $has_changed ) {
            $this->onPropertyChanged('category', $category, $this->category);
        }

    }

    /**
     * Parse language objects from bag
     *
     * @param array $data Language data
     *
     * @return void
     */
    protected function parseLanguage($data)
    {
        $language = clone $this->language;
        $has_changed = false;

        //check for removal
        foreach ( $this->language as $thelanguage ) {
            $found = false;
            foreach ($data as $value) {
                if ( $thelanguage->getContent() === $value['value']
                    && $thelanguage->getType() === $value['attributes']['type']
                ) {
                    $found = true;
                    break;
                }
            }
            if ( !$found ) {
                $this->removeLanguage($thelanguage);
                $this->removed[] = $thelanguage;
                $has_changed = true;
            }
        }
        //check for new
        foreach ( $data as $value ) {
            $unique = true;
            foreach ( $this->language as $i ) {
                if ( $i->getContent() == $value['value']
                    && $i->getType() == $value['attributes']['type']
                ) {
                    $unique = false;
                    break;
                }
            }

            if ( $unique === true ) {
                $result = new PMBLanguage($value['attributes']['type'], $value['value'], $this);
                $this->addLanguage($result);
                $has_changed = true;
            }
        }

        //notify if something has changed
        if ( $has_changed ) {
            $this->onPropertyChanged('language', $language, $this->language);
        }

    }
    /**
     * Parse title objects from bag
     *
     * @param array $data Title data
     *
     * @return void
     */
    protected function parseTitle($data)
    {
        $Title = clone $this->Title;
        $has_changed = false;
        /*foreach ($data as $value) {

        }*/
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
     * Set idNotice
     *
     * @param string $idNotice idNotice
     *
     * @return PMBFileFormat
     */
    public function setIdNotice($idNotice)
    {
        $this->idNotice = $idNotice;

        return $this;
    }

    /**
     * Get idNotice
     *
     * @return string
     */
    public function getIdNotice()
    {
        return $this->idNotice;
    }

    /**
     * Set title_proper
     *
     * @param string $titleProper titleProper
     *
     * @return PMBFileFormat
     */
    public function setTitleProper($titleProper)
    {
        if ( $this->title_proper !== $titleProper ) {
            $this->onPropertyChanged(
                'title_proper',
                $this->title_proper,
                $titleProper
            );
            $this->title_proper = $titleProper;
        }
        return $this;
    }

    /**
     * Get title_proper
     *
     * @return string
     */
    public function getTitleProper()
    {
        return $this->title_proper;
    }

    /**
     * Set clean_title_author_different
     *
     * @param string $cleanTitleAuthorDifferent cleanTitleAuthorDifferent
     *
     * @return PMBFileFormat
     */
    public function setCleanTitleAuthorDifferent($cleanTitleAuthorDifferent)
    {
        if ( $this->clean_title_author_different !== $cleanTitleAuthorDifferent ) {
            $this->onPropertyChanged(
                'clean_title_author_different',
                $this->clean_title_author_different,
                $cleanTitleAuthorDifferent
            );
            $this->clean_title_author_different = $cleanTitleAuthorDifferent;
        }
        return $this;
    }

    /**
     * Get clean_title_author_different
     *
     * @return string
     */
    public function getCleanTitleAuthorDifferent()
    {
        return $this->clean_title_author_different;
    }

    /**
     * Set parallel_title
     *
     * @param string $parallelTitle parallelTitle
     *
     * @return PMBFileFormat
     */
    public function setParallelTitle($parallelTitle)
    {
        if ( $this->parallel_title !== $parallelTitle ) {
            $this->onPropertyChanged(
                'parallel_title',
                $this->parallel_title,
                $parallelTitle
            );
            $this->parallel_title = $parallelTitle;
        }
        return $this;
    }

    /**
     * Get parallel_title
     *
     * @return string
     */
    public function getParallelTitle()
    {
        return $this->parallel_title;
    }

    /**
     * Set title_complement
     *
     * @param string $titleComplement titleComplement
     *
     * @return PMBFileFormat
     */
    public function setTitleComplement($titleComplement)
    {
        if ( $this->title_complement !== $titleComplement ) {
            $this->onPropertyChanged(
                'title_complement',
                $this->title_complement,
                $titleComplement
            );
            $this->title_complement = $titleComplement;
        }
        return $this;
    }

    /**
     * Get title_complement
     *
     * @return string
     */
    public function getTitleComplement()
    {
        return $this->title_complement;
    }

    /**
     * Set cod_unimarc
     *
     * @param string $codUnimarc codUnimarc
     *
     * @return PMBFileFormat
     */
    public function setCodUnimarc($codUnimarc)
    {
        if ( $this->cod_unimarc !== $codUnimarc ) {
            $this->onPropertyChanged(
                'cod_unimarc',
                $this->cod_unimarc,
                $codUnimarc
            );
            $this->cod_unimarc = $codUnimarc;
        }
        return $this;
    }

    /**
     * Get cod_unimarc
     *
     * @return string
     */
    public function getCodUnimarc()
    {
        return $this->cod_unimarc;
    }

    /**
     * Set part_of
     *
     * @param string $partOf partOf
     *
     * @return PMBFileFormat
     */
    public function setPartOf($partOf)
    {
        if ( $this->part_of !== $part_of ) {
            $this->onPropertyChanged(
                'part_of',
                $this->part_of,
                $part_of
            );
            $this->part_of = $part_of;
        }
        return $this;
    }

    /**
     * Get part_of
     *
     * @return string
     */
    public function getPartOf()
    {
        return $this->part_of;
    }

    /**
     * Set part_num
     *
     * @param string $partNum partNum
     *
     * @return PMBFileFormat
     */
    public function setPartNum($partNum)
    {
        if ( $this->part_num !== $part_num ) {
            $this->onPropertyChanged(
                'part_num',
                $this->part_num,
                $part_num
            );
            $this->part_num = $part_num;
        }
        return $this;
    }

    /**
     * Get part_num
     *
     * @return string
     */
    public function getPartNum()
    {
        return $this->part_num;
    }

    /**
     * Set editor
     *
     * @param string $editor editor
     *
     * @return PMBFileFormat
     */
    public function setEditor($editor)
    {
        if ( $this->editor !== $editor ) {
            $this->onPropertyChanged(
                'editor',
                $this->editor,
                $editor
            );
            $this->editor = $editor;
        }
        return $this;
    }

    /**
     * Get editor
     *
     * @return string
     */
    public function getEditor()
    {
        return $this->editor;
    }

    /**
     * Set collection
     *
     * @param string $collection collection
     *
     * @return PMBFileFormat
     */
    public function setCollection($collection)
    {
        if ( $this->collection !== $collection ) {
            $this->onPropertyChanged(
                'collection',
                $this->collection,
                $collection
            );
            $this->collection = $collection;
        }
        return $this;
    }

    /**
     * Get collection
     *
     * @return string
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Set numcollection
     *
     * @param string $numcollection numcollection
     *
     * @return PMBFileFormat
     */
    public function setNumcollection($numcollection)
    {
        if ( $this->numcollection !== $numcollection ) {
            $this->onPropertyChanged(
                'numcollection',
                $this->numcollection,
                $numcollection
            );
            $this->numcollection = $numcollection;
        }
        return $this;
    }

    /**
     * Get numcollection
     *
     * @return string
     */
    public function getNumcollection()
    {
        return $this->numcollection;
    }

    /**
     * Set subcollection
     *
     * @param string $subcollection subcollection
     *
     * @return PMBFileFormat
     */
    public function setSubcollection($subcollection)
    {
        if ( $this->subcollection !== $subcollection ) {
            $this->onPropertyChanged(
                'subcollection',
                $this->subcollection,
                $subcollection
            );
            $this->subcollection = $subcollection;
        }
        return $this;
    }

    /**
     * Get subcollection
     *
     * @return string
     */
    public function getSubcollection()
    {
        return $this->subcollection;
    }

    /**
     * Set year
     *
     * @param \DateTime $year year
     *
     * @return PMBFileFormat
     */
    public function setYear($year)
    {
        if ( $this->year !== $year ) {
            $this->onPropertyChanged(
                'year',
                $this->year,
                $year
            );
            $this->year = $year;
        }
        return $this;
    }

    /**
     * Get year
     *
     * @return \DateTime
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set mention_edition
     *
     * @param string $mentionEdition mentionEdition
     *
     * @return PMBFileFormat
     */
    public function setMentionEdition($mentionEdition)
    {
        if ( $this->mention_edition !== $mention_edition ) {
            $this->onPropertyChanged(
                'mention_edition',
                $this->mention_edition,
                $mention_edition
            );
            $this->mention_edition = $mention_edition;
        }
        return $this;
    }

    /**
     * Get mention_edition
     *
     * @return string
     */
    public function getMentionEdition()
    {
        return $this->mention_edition;
    }

    /**
     * Set other_editor
     *
     * @param string $otherEditor otherEditor
     *
     * @return PMBFileFormat
     */
    public function setOtherEditor($otherEditor)
    {
        if ( $this->other_editor !== $otherEditor ) {
            $this->onPropertyChanged(
                'other_editor',
                $this->other_editor,
                $otherEditor
            );
            $this->other_editor = $otherEditor;
        }
        return $this;
    }

    /**
     * Get other_editor
     *
     * @return string
     */
    public function getOtherEditor()
    {
        return $this->other_editor;
    }

    /**
     * Set isbn
     *
     * @param string $isbn isbn
     *
     * @return PMBFileFormat
     */
    public function setIsbn($isbn)
    {
        if ( $this->isbn !== $isbn ) {
            $this->onPropertyChanged(
                'isbn',
                $this->isbn,
                $isbn
            );
            $this->isbn = $isbn;
        }
        return $this;
    }

    /**
     * Get isbn
     *
     * @return string
     */
    public function getIsbn()
    {
        return $this->isbn;
    }

    /**
     * Set material_importance
     *
     * @param string $materialImportance materialImportance
     *
     * @return PMBFileFormat
     */
    public function setMaterialImportance($materialImportance)
    {
        if ( $this->material_importance !== $materialImportance ) {
            $this->onPropertyChanged(
                'material_importance',
                $this->material_importance,
                $materialImportance
            );
            $this->material_importance = $materialImportance;
        }
        return $this;
    }

    /**
     * Get material_importance
     *
     * @return string
     */
    public function getMaterialImportance()
    {
        return $this->material_importance;
    }

    /**
     * Set other_physical_characteristics
     *
     * @param string $otherPhysicalCharacteristics otherPhysicalCharacteristics
     *
     * @return PMBFileFormat
     */
    public function setOtherPhysicalCharacteristics($otherPhysicalCharacteristics)
    {
        if ( $this->other_physical_characteristics !== $otherPhysicalCharacteristics ) {
            $this->onPropertyChanged(
                'autresCaracMaterielle',
                $this->other_physical_characteristics,
                $autresCaracMaterielle
            );
            $this->other_physical_characteristics = $otherPhysicalCharacteristics;
        }
        return $this;
    }

    /**
     * Get other_physical_characteristics
     *
     * @return string
     */
    public function getOtherPhysicalCharacteristics()
    {
        return $this->other_physical_characteristics;
    }

    /**
     * Set format
     *
     * @param string $format format
     *
     * @return PMBFileFormat
     */
    public function setFormat($format)
    {
        if ( $this->format !== $format ) {
            $this->onPropertyChanged(
                'format',
                $this->format,
                $format
            );
            $this->format = $format;
        }
        return $this;
    }

    /**
     * Get format
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set price
     *
     * @param string $price price
     *
     * @return PMBFileFormat
     */
    public function setPrice($price)
    {
        if ( $this->price !== $price ) {
            $this->onPropertyChanged(
                'price',
                $this->price,
                $price
            );
            $this->price = $price;
        }
        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set material_support
     *
     * @param string $materialSupport materialSupport
     *
     * @return PMBFileFormat
     */
    public function setMaterialSupport($materialSupport)
    {
        if ( $this->material_support !== $materialSupport ) {
            $this->onPropertyChanged(
                'material_support',
                $this->material_support,
                $materialSupport
            );
            $this->material_support = $materialSupport;
        }
        return $this;
    }

    /**
     * Get material_support
     *
     * @return string
     */
    public function getMaterialSupport()
    {
        return $this->material_support;
    }

    /**
     * Set note_general
     *
     * @param string $noteGeneral noteGeneral
     *
     * @return PMBFileFormat
     */
    public function setNoteGeneral($noteGeneral)
    {
        if ( $this->note_general !== $noteGeneral ) {
            $this->onPropertyChanged(
                'note_general',
                $this->note_general,
                $noteGeneral
            );
            $this->note_general = $noteGeneral;
        }
        return $this;
    }

    /**
     * Get note_general
     *
     * @return string
     */
    public function getNoteGeneral()
    {
        return $this->note_general;
    }

    /**
     * Set textcontent
     *
     * @param string $textcontent textcontent
     *
     * @return PMBFileFormat
     */
    public function setTextcontent($textcontent)
    {
        $this->textcontent = $textcontent;
        if ( $this->textcontent !== $textcontent ) {
            $this->onPropertyChanged(
                'textcontent',
                $this->textcontent,
                $textcontent
            );
            $this->textcontent = $textcontent;
        }
        return $this;
    }

    /**
     * Get textcontent
     *
     * @return string
     */
    public function getTextcontent()
    {
        return $this->textcontent;
    }

    /**
     * Set extract
     *
     * @param string $extract extract
     *
     * @return PMBFileFormat
     */
    public function setExtract($extract)
    {
        if ( $this->extract !== $extract ) {
            $this->onPropertyChanged(
                'extract',
                $this->extract,
                $extract
            );
            $this->extract = $extract;
        }
        return $this;
    }

    /**
     * Get extract
     *
     * @return string
     */
    public function getExtract()
    {
        return $this->extract;
    }

    /**
     * Set indexing_decimal
     *
     * @param string $indexingDecimal indexingDecimal
     *
     * @return PMBFileFormat
     */
    public function setIndexingDecimal($indexingDecimal)
    {
        if ( $this->indexing_decimal !== $indexingDecimal ) {
            $this->onPropertyChanged(
                'indexing_decimal',
                $this->indexing_decimal,
                $indexingDecimal
            );
            $this->indexing_decimal = $indexingDecimal;
        }
        return $this;
    }

    /**
     * Get indexing_decimal
     *
     * @return string
     */
    public function getIndexingDecimal()
    {
        return $this->indexing_decimal;
    }

    /**
     * Set keyword
     *
     * @param string $keyword keyword
     *
     * @return PMBFileFormat
     */
    public function setKeyword($keyword)
    {
        if ( $this->keyword !== $keyword ) {
            $this->onPropertyChanged(
                'keyword',
                $this->keyword,
                $keyword
            );
            $this->keyword = $keyword;
        }
        return $this;
    }

    /**
     * Get keyword
     *
     * @return string
     */
    public function getKeyword()
    {
        return $this->keyword;
    }

    /**
     * Set link_ressource_elect
     *
     * @param string $linkRessourceElect linkRessourceElect
     *
     * @return PMBFileFormat
     */
    public function setLinkRessourceElect($linkRessourceElect)
    {
        if ( $this->link_ressource_elect !== $linkRessourceElect ) {
            $this->onPropertyChanged(
                'link_ressource_elect',
                $this->link_ressource_elect,
                $linkRessourceElect
            );
            $this->link_ressource_elect = $linkRessourceElect;
        }
        return $this;
    }

    /**
     * Get link_ressource_elect
     *
     * @return string
     */
    public function getLinkRessourceElect()
    {
        return $this->link_ressource_elect;
    }

    /**
     * Set format_elect_ressource
     *
     * @param string $formatElectRessource formatElectRessource
     *
     * @return PMBFileFormat
     */
    public function setFormatElectRessource($formatElectRessource)
    {
        if ( $this->format_elect_ressource !== $formatElectRessource ) {
            $this->onPropertyChanged(
                'format_elect_ressource',
                $this->format_elect_ressource,
                $formatElectRessource
            );
            $this->format_elect_ressource = $formatElectRessource;
        }
        return $this;
    }

    /**
     * Get format_elect_ressource
     *
     * @return string
     */
    public function getFormatElectRessource()
    {
        return $this->format_elect_ressource;
    }

    /**
     * Set urlimg
     *
     * @param string $urlimg urlimg
     *
     * @return PMBFileFormat
     */
    public function setUrlimg($urlimg)
    {
        if ( $this->urlimg !== $urlimg ) {
            $this->onPropertyChanged(
                'urlimg',
                $this->urlimg,
                $urlimg
            );
            $this->urlimg = $urlimg;
        }
        return $this;
    }

    /**
     * Get urlimg
     *
     * @return string
     */
    public function getUrlimg()
    {
        return $this->urlimg;
    }

    /**
     * Set fragment
     *
     * @param string $fragment fragment
     *
     * @return PMBFileFormat
     */
    public function setFragment($fragment)
    {
        if ( $this->fragment !== $fragment ) {
            $this->onPropertyChanged(
                'fragment',
                $this->fragment,
                $fragment
            );
            $this->fragment = $fragment;
        }
        return $this;
    }

    /**
     * Get fragment
     *
     * @return string
     */
    public function getFragment()
    {
        return $this->fragment;
    }



    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }


    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Add title
     *
     * @param \Bach\IndexationBundle\Entity\PMBTitle $title title
     *
     * @return PMBFileFormat
     */
    public function addTitle(\Bach\IndexationBundle\Entity\PMBTitle $title)
    {
        $this->title[] = $title;

        return $this;
    }

    /**
     * Remove title
     *
     * @param \Bach\IndexationBundle\Entity\PMBTitle $title title
     *
     * @return void
     */
    public function removeTitle(\Bach\IndexationBundle\Entity\PMBTitle $title)
    {
        $this->title->removeElement($title);
    }

    /**
     * Get title
     *
     * @return \Doctrine\Common\Collections\Collection
     *
     * @return void
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Add authors
     *
     * @param \Bach\IndexationBundle\Entity\PMBAuthor $authors authors
     *
     * @return PMBFileFormat
     */
    public function addAuthor(\Bach\IndexationBundle\Entity\PMBAuthor $authors)
    {
        $this->authors[] = $authors;

        return $this;
    }

    /**
     * Remove authors
     *
     * @param \Bach\IndexationBundle\Entity\PMBAuthor $authors authors
     *
     * @return void
     */
    public function removeAuthor(\Bach\IndexationBundle\Entity\PMBAuthor $authors)
    {
        $this->authors->removeElement($authors);
    }

    /**
     * Get authors
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * Add category
     *
     * @param \Bach\IndexationBundle\Entity\PMBCategory $category category
     *
     * @return PMBFileFormat
     */
    public function addCategory(\Bach\IndexationBundle\Entity\PMBCategory $category)
    {
        $this->category[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param \Bach\IndexationBundle\Entity\PMBCategory $category category
     *
     * @return void
     */
    public function removeCategory(\Bach\IndexationBundle\Entity\PMBCategory $category)
    {
        $this->category->removeElement($category);
    }

    /**
     * Get category
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Add language
     *
     * @param \Bach\IndexationBundle\Entity\PMBLanguage $language language
     *
     * @return PMBFileFormat
     */
    public function addLanguage(\Bach\IndexationBundle\Entity\PMBLanguage $language)
    {
        $this->language[] = $language;

        return $this;
    }

    /**
     * Remove language
     *
     * @param \Bach\IndexationBundle\Entity\PMBLanguage $language language
     *
     * @return void
     */
    public function removeLanguage(\Bach\IndexationBundle\Entity\PMBLanguage $language)
    {
        $this->language->removeElement($language);
    }

    /**
     * Get language
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set document
     *
     * @param \Bach\IndexationBundle\Entity\Document $document document
     *
     * @return PMBFileFormat
     */
    public function setDocument(\Bach\IndexationBundle\Entity\Document $document = null)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get document
     *
     * @return \Bach\IndexationBundle\Entity\Document
     */
    public function getDocument()
    {
        return $this->document;
    }

}
