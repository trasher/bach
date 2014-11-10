<?php
/**
 * Convert an input file into a FileFormat object
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
 * @author   Anaphore PI Team <uknown@unknown.com>
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */

namespace Bach\IndexationBundle\Service;

use Bach\IndexationBundle\Entity\FileDriver;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM\EntityManager;
use Bach\IndexationBundle\Entity\FileFormat;
use Bach\IndexationBundle\Entity;
use Bach\IndexationBundle\Entity\DataBag;
use Bach\IndexationBundle\Service\ZendDb;

/**
 * Convert an input file into a FileFormat object
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Anaphore PI Team <uknown@unknown.com>
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class FileDriverManager
{
    private $_drivers = array();
    private $_conf = array();
    private $_fileFormatFactory;
    private $_preProcessorFactory;
    private $_entityManager;
    private $_heritage;
    private $_zdb;
    private $_updaterecord_stmt;

    /**
     * Constructor
     *
     * @param FileFormatFactory   $fileFormatFactory   File format Factory
     * @param PreProcessorFactory $preProcessorFactory Pre processor factory
     * @param EntityManager       $entityManager       The entity manager
     * @param boolean             $heritage            Heritage status
     * @param ZendDb              $zdb                 Zend database wrapper
     */
    public function __construct(FileFormatFactory $fileFormatFactory,
        PreProcessorFactory $preProcessorFactory, EntityManager $entityManager,
        $heritage, ZendDb $zdb
    ) {
        $this->_importConfiguration();
        $this->_searchDrivers();
        $this->_fileFormatFactory = $fileFormatFactory;
        $this->_preProcessorFactory = $preProcessorFactory;
        $this->_entityManager = $entityManager;
        $this->_heritage = $heritage;
        $this->_zdb = $zdb;
    }

    /**
     * Convert an input file into FileFormat object
     *
     * @param DataBag  $bag          Data bag
     * @param string   $format       File format
     * @param Document $doc          Document
     * @param boolean  $transaction  Whether to use DB transaction or not
     * @param string   $preprocessor Preprocessor, if any (defaults to null)
     *
     * @return FileFormat the normalized file object
     */
    public function convert(DataBag $bag, $format, $doc, $transaction = true,
        $preprocessor = null
    ) {
        $baseMemory = memory_get_usage();

        if ( !array_key_exists($format, $this->_drivers) ) {
            throw new \DomainException('Unsupported file format: ' . $format);
        }

        $mapper = null;
        $fileformat_class = null;
        $doctrine_entity = null;
        $docid = $doc->getId();

        $this->_getConfiguration(
            $format,
            $mapper,
            $fileformat_class,
            $doctrine_entity,
            $preprocessor
        );

        $driver = $this->_drivers[$format];

        if ( !is_null($preprocessor) ) {
            $bag = $this->_preProcessorFactory->preProcess(
                $bag,
                $preprocessor
            );
        }

        //EAD specific
        if ( $format === 'ead' ) {
            $driver->setHeritage($this->_heritage);
        }

        $results = $driver->process($bag);

        $eadheader = null;
        $headerid = null;
        $archdesc = null;

        $count = 0;

        try {
            if ( $transaction ) {
                $this->_zdb->connection->beginTransaction();
            }

            if ( $format === 'ead' ) {
                //handle eadheader
                $eadheader = $mapper->translateHeader(
                    $results['eadheader']
                );

                $select = $this->_zdb->select('ead_header')
                    ->limit(1)
                    ->where(
                        array(
                            'headerId' => $eadheader['headerId']
                        )
                    );
                $eadh_results = $this->_zdb->execute($select);
                $eadh = $eadh_results->current();

                if ( $eadh === false ) {
                    //EAD header does not exists yet. Store it.
                    $now = new \DateTime();
                    $eadheader['created'] = $now->format('Y-m-d h:m:s');
                    $eadheader['updated'] = $now->format('Y-m-d h:m:s');
                    $insert = $this->_zdb->insert('ead_header')
                        ->values($eadheader);
                    $add = $this->_zdb->execute($insert);
                    if ( !$add->count() > 0 ) {
                        throw new \RuntimeException(
                            'An error occured storing EAD header ' .
                            $eadheader['headerId']
                        );
                    }
                    $headerid = $this->_zdb->getAutoIncrement('ead_header');
                } else {
                    $headerid = $eadh->id;
                    $header_obj = new Entity\EADHeader(
                        $eadh,
                        false
                    );
                    $header_obj->hydrate(
                        $eadheader
                    );
                    if ( $header_obj->hasChanges() ) {
                        $update = $this->_zdb->update('ead_header')
                            ->set($header_obj->toArray())
                            ->where(array('id' => $headerid));
                        $add = $this->_zdb->execute($update);
                    }
                }

                //handle archdesc
                $mapper->setEadId($eadheader['headerId']);
                $archdesc = $mapper->translate($results['archdesc']);

                $select = $this->_zdb->select('ead_file_format')
                    ->limit(1)
                    ->where(
                        array(
                            'fragmentid' => $archdesc['fragmentid']
                        )
                    );
                $archdesc_results = $this->_zdb->execute($select);
                $eada = $archdesc_results->current();

                if ( $eada === false ) {
                    $archdesc_obj = new Entity\EADFileFormat(
                        $archdesc
                    );
                    $archdesc = $archdesc_obj->toArray();
                    //EAD archdesc does not exists yet. Store it.
                    $archdescid = $this->_storeEadFragment(
                        $archdesc,
                        $headerid,
                        null,
                        $docid
                    );
                } else {
                    $archdescid = $eada->uniqid;

                    $archdesc_obj = $this->_updateEadFragment(
                        $eada,
                        $archdesc,
                        $header_obj,
                        null,
                        $doc
                    );
                }

                $results = $results['elements'];

                foreach ($results as &$result) {
                    $fragment = $mapper->translate(
                        $result
                    );

                    $select = $this->_zdb->select('ead_file_format')
                        ->limit(1)
                        ->where(
                            array(
                                'fragmentid' => $fragment['fragmentid']
                            )
                        );
                    $fragment_results = $this->_zdb->execute($select);
                    $eadf = $fragment_results->current();

                    if ( $eadf === false ) {
                        $fragment = new Entity\EADFileFormat(
                            $fragment
                        );
                        $fragment = $fragment->toArray();

                        //EAD fragment does not exists yet. Store it.
                        $this->_storeEadFragment(
                            $fragment,
                            $headerid,
                            $archdescid,
                            $docid
                        );
                    } else {
                        $this->_updateEadFragment(
                            $eadf,
                            $archdesc,
                            $header_obj,
                            $archdesc_obj,
                            $doc
                        );
                    }

                    $count++;
                }
            } elseif ( $format === 'matricules' ) {
                foreach ($results as &$result) {
                    $record = $mapper->translate(
                        $result
                    );

                    $select = $this->_zdb->select('matricules_file_format')
                        ->limit(1)
                        ->where(
                            array(
                                'id' => $record['id'][0]['value']
                            )
                        );
                    $db_records = $this->_zdb->execute($select);
                    $db_record = $db_records->current();

                    if ( $db_record === false ) {
                        $record = new Entity\MatriculesFileFormat(
                            $record
                        );
                        $record = $record->toArray();

                        //record does not exists yet. Store it.
                        $this->_storeRecord(
                            'matricules_file_format',
                            $record,
                            $docid
                        );
                    } else {
                        $this->_updateMatRecord(
                            $db_record,
                            $record,
                            $doc
                        );
                    }
                }
            }

            if ( $transaction ) {
                $this->_zdb->connection->commit();
            }
        } catch ( \Exception $e ) {
            if ( $transaction ) {
                $this->_zdb->connection->rollBack();
            }
            throw $e;
        }

        $size = memory_get_usage() - $baseMemory;
        $consumed = $this->formatBytes(memory_get_usage() - $baseMemory);
        $peak = $this->formatBytes(memory_get_peak_usage());

        echo "\nConsumed memory: " . $consumed . ' - Peak: ' . $peak . "\n";
    }

    /**
     * Converts elements from database to a compatible translated array
     *
     * @param array  $entries        Db entries
     * @param string $value_prop     Name for value property
     * @param array  $not_attributes Db columns that are not attributes
     * @param bool   $descriptors    If we're working on descriptors
     *
     * @return array
     */
    private function _elementsAsArray($entries, $value_prop, $not_attributes,
        $descriptors = false
    ) {
        $elements = array();
        $not_attributes = array_merge(
            $not_attributes,
            array(
                'id',
                'eadfile_id',
            )
        );
        foreach ( $entries as $entry ) {
            $attributes = array();
            foreach ( $entry as $name=>$value ) {
                if ( !in_array($name, $not_attributes)
                    && $value !== null
                ) {
                    $attributes[$name] = $value;
                }
            }

            if ( $descriptors === true ) {
                $elements[$entry->type][] = array(
                    'value'         => $entry->$value_prop,
                    'attributes'    => $attributes
                );
            } else {
                 $elements[] = array(
                    'value'         => $entry->$value_prop,
                    'attributes'    => $attributes
                );
            }
        }
        return $elements;
    }

    /**
     * Format bytes to human readable value
     *
     * @param int $bytes Bytes
     *
     * @return string
     */
    public function formatBytes($bytes)
    {
        $multiplicator = 1;
        if ( $bytes < 0 ) {
            $multiplicator = -1;
            $bytes = $bytes * $multiplicator;
        }
        $unit = array('b','kb','mb','gb','tb','pb');
        $fmt = @round($bytes/pow(1024, ($i=floor(log($bytes, 1024)))), 2)
            * $multiplicator . ' ' . $unit[$i];
        return $fmt;
    }

    /**
     * Stores a record in database
     *
     * @param string $table Database table name
     * @param array  $data  Data to store
     * @param int    $docid Document db id
     *
     * @return int
     */
    private function _storeRecord($table, $data ,$docid)
    {
        $data['doc_id'] = $docid;

        $stmt = null;
        if ( $this->_updaterecord_stmt !== null ) {
            $stmt = $this->_updaterecord_stmt;
        } else {
            $insert_fields = array();
            foreach ( array_keys($data) as $field ) {
                $insert_fields[$field] = ':' . $field;
            }
            $insert = $this->_zdb->insert($table)
                ->values($insert_fields);
            $stmt = $this->_zdb->sql->prepareStatementForSqlObject(
                $insert
            );
            $this->_updaterecord_stmt = $stmt;
        }

        $add = $stmt->execute($data);
        if ( !$add->count() > 0 ) {
            throw new \RuntimeException(
                'An error occured storing record!'
            );
        }
        $fragid = $this->_zdb->getAutoIncrement($table);
        return $fragid;
    }

    /**
     * Updates a matricule record in database
     *
     * @param array    $data    Data to store
     * @param array    $newvals New values fom conversion
     * @param Document $doc     Document element
     *
     * @return EADFileFormat
     */
    private function _updateMatRecord($data, $newvals,
        Entity\Document $doc
    ) {
        unset($data['doc_id']);

        $converted_data = array();
        foreach ( $data as $k=>$v ) {
            //converts dates from yyyy-mm-dd to yyyy
            $reg = '/^(\d{3,4})-?(\d{2})?-?(\d{2})?$/';
            if ( preg_match($reg, $v, $matches) ) {
                $v = $matches[1];
            }

            $converted_data[$k][] = array(
                'value'         => $v,
                'attributes'    => array()
            );

        }

        $obj = new Entity\MatriculesFileFormat(
            $converted_data,
            false
        );
        $obj->setDocument($doc);
        $obj->hydrate($newvals);

        if ( $obj->hasChanges() ) {
            $values = $obj->toArray();

            $update = $this->_zdb->update('matricules_file_format')
                ->set($values)
                ->where(array('uniqid' => $data['uniqid']));
            $add = $this->_zdb->execute($update);
        }

        return $obj;
    }


    /**
     * Stores an EAD fragment in database
     *
     * @param EADFileFormat $fragment   Fragment to store
     * @param int           $headerid   eadheader db id
     * @param int           $archdescid archdesc db id
     * @param int           $docid      Document db id
     *
     * @return int
     */
    private function _storeEadFragment($fragment, $headerid, $archdescid, $docid)
    {
        $indexes = $fragment['indexes'];
        unset($fragment['indexes']);
        $dates = $fragment['dates'];
        unset($fragment['dates']);
        $daos = $fragment['daos'];
        unset($fragment['daos']);
        $parents_titles = $fragment['parents_titles'];
        unset($fragment['parents_titles']);

        $fragment['eadheader_id'] = $headerid;
        $fragment['archdesc_id'] = $archdescid;

        try {
            $fragid = $this->_storeRecord(
                'ead_file_format',
                $fragment,
                $docid
            );
        } catch ( \RuntimeException $e) {
            throw new \RuntimeException(
                'An error occured storing EAD fragment ' .
                $fragment['fragmentid']
            );
        }

        if ( count($indexes) > 0 ) {
            //handle indexes
            $this->_storeSubElements(
                'ead_indexes',
                $indexes,
                $fragid
            );
        }

        if ( count($dates) > 0 ) {
            //handle dates
            $this->_storeSubElements(
                'ead_dates',
                $dates,
                $fragid
            );
        }

        if ( count($daos) > 0 ) {
            //handle daos
            $this->_storeSubElements(
                'ead_daos',
                $daos,
                $fragid
            );
        }

        if ( count($parents_titles) > 0 ) {
            //handle parents_titles
            $this->_storeSubElements(
                'ead_parent_title',
                $parents_titles,
                $fragid
            );
        }

        return $fragid;
    }

    /**
     * Updates a fragment in database
     *
     * @param array         $fragment Fragment to store
     * @param array         $newvals  New values from conversion
     * @param EADHeader     $header   eadheader element
     * @param EADFileFormat $archdesc archdesc element
     * @param Document      $doc      document element
     *
     * @return EADFileFormat
     */
    private function _updateEadFragment($fragment, $newvals,
        Entity\EADHeader $header, $archdesc, Entity\Document $doc
    ) {
        unset($fragment['doc_id']);
        $fragment['document'] = $doc;
        unset($fragment['eadheader_id']);
        $fragment['eadheader'] = $header;

        unset($fragment['archdesc_id']);
        if ( $archdesc !== null ) {
            $fragment['archdesc'] = $archdesc;
        }

        //handle indexes
        $select = $this->_zdb->select('ead_indexes')
            ->where(
                array(
                    'eadfile_id' => $fragment['uniqid']
                )
            );
        $indexes = $this->_zdb->execute($select);
        $fragment['descriptors'] = $this->_elementsAsArray(
            $indexes,
            'name',
            array('type', 'name'),
            true
        );

        //handle dates
        $select = $this->_zdb->select('ead_dates')
            ->where(
                array(
                    'eadfile_id' => $fragment['uniqid']
                )
            );
        $dates = $this->_zdb->execute($select);
        $fragment['cDate'] = $this->_elementsAsArray(
            $dates,
            'date',
            array('date', 'begin', 'end')
        );

        //TODO: take care of other linked elements!

        $obj = new Entity\EADFileFormat(
            $fragment,
            false
        );
        $obj->hydrate($newvals);

        if ( $obj->hasChanges() ) {
            $values = $obj->toArray();

            $indexes = $values['indexes'];
            unset($values['indexes']);
            $dates = $values['dates'];
            unset($values['dates']);
            $daos = $values['daos'];
            unset($values['daos']);
            $parents_titles = $values['parents_titles'];
            unset($values['parents_titles']);

            $update = $this->_zdb->update('ead_file_format')
                ->set($values)
                ->where(array('uniqid' => $fragment['uniqid']));
            $add = $this->_zdb->execute($update);

            //TODO: take care of removals!
        }

        return $obj;
    }


    /**
     * Store fragment sub elements
     *
     * @param string $table    Table name
     * @param array  $elements Elements to store
     * @param int    $fragid   Fragment ID
     *
     * @return void
     */
    private function _storeSubElements($table, $elements, $fragid)
    {
        $insert = $this->_zdb->insert($table);
        $fields = array_keys($elements[0]);
        $insert_fields = array();
        foreach ( $fields as $field ) {
            $insert_fields[$field] = ':' . $field;
        }
        $insert_fields['eadfile_id'] = ':eadfile_id';

        $insert->values($insert_fields);
        $stmt = $this->_zdb->sql->prepareStatementForSqlObject(
            $insert
        );

        foreach ( $elements as $element ) {
            $element['eadfile_id'] = $fragid;
            $stmt->execute($element);
        }
    }

    /**
     * Load driver configuration
     *
     * @param string $format           Data type
     * @param string $mapper           Mapper name
     * @param string $fileformat_class File format class
     * @param string $doctrine_entity  Doctrine entity name
     * @param string $preprocessor     Preprocessor
     *
     * @return void
     */
    private function _getConfiguration($format, &$mapper, &$fileformat_class,
        &$doctrine_entity, &$preprocessor
    ) {
        //Import driver configuration
        if (array_key_exists('drivers', $this->_conf)) {
            if (array_key_exists($format, $this->_conf['drivers'])) {
                $format_conf = $this->_conf['drivers'][$format];
                if (array_key_exists('mapper', $format_conf)) {
                    try {
                        $reflection = new \ReflectionClass(
                            $format_conf['mapper']
                        );

                        $expected = 'Bach\IndexationBundle' .
                            '\DriverMapperInterface';
                        $interfaces = $reflection->getInterfaceNames();

                        if ( !in_array($expected, $interfaces)) {
                            throw new \RuntimeException(
                                'Found mapper does not implements ' . $expected
                            );
                        }
                        $mapper = $reflection->newInstance();
                    } catch (\RuntimeException $e) {
                        throw $e;
                    }
                }

                if ( array_key_exists('fileformat', $format_conf) ) {
                    $fileformat_class = $format_conf['fileformat'];
                } else {
                    throw new \RuntimeException(
                        'Driver configuration for ' . $format .
                        ' is missing the fileformat entry.'
                    );
                }

                if ( array_key_exists('doctrine', $format_conf) ) {
                    $doctrine_entity = $format_conf['doctrine'];
                } else {
                    throw new \RuntimeException(
                        'Driver configuration for ' . $format .
                        ' is missing the doctrine entry.'
                    );
                }

                if ( array_key_exists('preprocessor', $format_conf)
                    && is_null($preprocessor)
                ) {
                    $preprocessor = $format_conf['preprocessor'];
                }
            }
        }

    }

    /**
     * Register a FileDriver into the manager
     *
     * @param FileDriver $driver Driver to register
     *
     * @return void
     */
    private function _registerDriver(FileDriver $driver)
    {
        if ( !array_key_exists($driver->getFileFormatName(), $this->_drivers) ) {
            $this->_drivers[$driver->getFileFormatName()] = $driver;
        } else {
            throw new \RuntimeException(
                "A driver for this file format is already loaded"
            );
        }
    }

    /**
     * Perform a research of available drivers
     *
     * @return void
     */
    private function _searchDrivers()
    {
        $finder = new Finder();
        $finder->directories()->in(__DIR__.'/../Entity/Driver')->depth('== 0');

        foreach ($finder as $file) {
            try {
                $reflection = new \ReflectionClass(
                    'Bach\IndexationBundle\Entity\Driver\\'.
                    $file->getBasename().'\\Driver'
                );
                if ('Bach\IndexationBundle\Entity\FileDriver' == $reflection->getParentClass()->getName()) {
                    $configuration = array();
                    if ( array_key_exists('drivers', $this->_conf) ) {
                        $basename = strtolower($file->getBasename());
                        if ( array_key_exists($basename, $this->_conf['drivers'])) {
                            $configuration = $this->_conf['drivers'][$basename];
                        }
                    }
                    $this->_registerDriver(
                        $reflection->newInstanceArgs(array($configuration))
                    );
                }
            } catch(\RuntimeException $e) {
                throw $e;
            }
        }
    }

    /**
     * Import drivers configuration file
     *
     * @return void
     */
    private function _importConfiguration()
    {
        $this->_conf = Yaml::parse(
            __DIR__.'/../Resources/config/drivers.yml'
        );
    }
}
