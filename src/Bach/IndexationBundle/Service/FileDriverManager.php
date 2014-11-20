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
    private $_stmts = array();
    public $used_mem = array();

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
        $start_memory = memory_get_usage();

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

                $stmt = null;
                if ( isset($this->_stmts['select_eadheader']) ) {
                    $stmt = $this->_stmts['select_eadheader'];
                } else {
                    $select = $this->_zdb->select('ead_header')
                        ->limit(1)
                        ->where(
                            array(
                                'headerId' => ':headerId'
                            )
                        );
                    $stmt = $this->_zdb->sql->prepareStatementForSqlObject(
                        $select
                    );
                    $this->_stmts['select_eadheader'] = $stmt;
                }

                $eadh_results = $stmt->execute(
                    array(
                        'where1' => $eadheader['headerId']
                    )
                );
                $eadh = $eadh_results->current();

                if ( $eadh === false ) {
                    //EAD header does not exists yet. Store it.
                    $now = new \DateTime();
                    $eadheader['created'] = $now->format('Y-m-d h:m:s');
                    $eadheader['updated'] = $now->format('Y-m-d h:m:s');

                    $stmt = null;
                    if ( isset($this->_stmts['insert_header']) ) {
                        $stmt = $this->_stmts['insert_header'];
                    } else {
                        $insert_fields = array();
                        foreach ( array_keys($eadheader) as $field ) {
                            $insert_fields[$field] = ':' . $field;
                        }
                        $insert = $this->_zdb->insert('ead_header')
                            ->values($insert_fields);
                        $stmt = $this->_zdb->sql->prepareStatementForSqlObject(
                            $insert
                        );
                        $this->_stmts['insert_header'] = $stmt;
                    }
                    $add = $stmt->execute($eadheader);

                    if ( !$add->count() > 0 ) {
                        throw new \RuntimeException(
                            'An error occured storing EAD header ' .
                            $eadheader['headerId']
                        );
                    }
                    $headerid = $this->_zdb->getAutoIncrement('ead_header');
                } else {
                    $headerid = $eadh['id'];
                    $header_obj = new Entity\EADHeader(
                        $eadh,
                        false
                    );
                    $header_obj->hydrate(
                        $eadheader
                    );
                    if ( $header_obj->hasChanges() ) {
                        $stmt = null;
                        if ( isset($this->_stmts['update_header']) ) {
                            $stmt = $this->_stmts['update_header'];
                        } else {
                            $values = $header_obj->toArray();
                            $update_fields = array();
                            foreach ( array_keys($values) as $field ) {
                                $update_fields[$field] = ':' . $field;
                            }
                            $update = $this->_zdb->update('ead_header')
                                ->set($update_fields)
                                ->where(array('id' => ':id'));
                            $stmt = $this->_zdb->sql->prepareStatementForSqlObject(
                                $update
                            );
                            $this->_stmts['update_header'] = $stmt;
                        }

                        $values['where1'] = $headerid;
                        $add = $stmt->execute($values);
                    }
                }

                //handle archdesc
                $mapper->setEadId($eadheader['headerId']);
                $archdesc = $mapper->translate($results['archdesc']);

                $stmt = null;
                if ( isset($this->_stmts['select_record']) ) {
                    $stmt = $this->_stmts['select_record'];
                } else {
                    $select = $this->_zdb->select('ead_file_format')
                        ->limit(1)
                        ->where(
                            array(
                                'fragmentid' => ':fragmentid'
                            )
                        );
                    $stmt = $this->_zdb->sql->prepareStatementForSqlObject(
                        $select
                    );
                    $this->_stmts['select_record'] = $stmt;
                }

                $archdesc_results = $stmt->execute(
                    array(
                        'where1' => $archdesc['fragmentid']
                    )
                );
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
                    $archdescid = $eada['uniqid'];

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

                    $stmt = null;
                    if ( isset($this->_stmts['select_record']) ) {
                        $stmt = $this->_stmts['select_record'];
                    } else {
                        $select = $this->_zdb->select('ead_file_format')
                            ->limit(1)
                            ->where(
                                array(
                                    'fragmentid' => ':fragmentid'
                                )
                            );
                        $stmt = $this->_zdb->sql->prepareStatementForSqlObject(
                            $select
                        );
                        $this->_stmts['select_record'] = $stmt;
                    }

                    $fragment_results = $stmt->execute(
                        array(
                            'where1' => $fragment['fragmentid']
                        )
                    );
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
                            $fragment,
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

                    $stmt = null;
                    if ( isset($this->_stmts['select_record']) ) {
                        $stmt = $this->_stmts['select_record'];
                    } else {
                        $select = $this->_zdb->select('matricules_file_format')
                            ->limit(1)
                            ->where(
                                array(
                                    'id' => ':id'
                                )
                            );
                        $stmt = $this->_zdb->sql->prepareStatementForSqlObject(
                            $select
                        );
                        $this->_stmts['select_record'] = $stmt;
                    }

                    $db_records = $stmt->execute(
                        array(
                            'where1' => $record['id'][0]['value']
                        )
                    );
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

        $this->used_mem[$docid] = memory_get_usage() - $start_memory;
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
                $elements[$entry['type']][] = array(
                    'value'         => $entry[$value_prop],
                    'id'            => $entry['id'],
                    'attributes'    => $attributes
                );
            } else {
                $value = null;
                if ( $value_prop !== null ) {
                    $value = $entry[$value_prop];
                }
                $elements[] = array(
                    'value'         => $value,
                    'id'            => $entry['id'],
                    'attributes'    => $attributes
                );
            }
        }
        return $elements;
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
        if ( isset($this->_stmts['insert_record']) ) {
            $stmt = $this->_stmts['insert_record'];
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
            $this->_stmts['insert_record'] = $stmt;
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
     * @return MatriculesFileFormat
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

            $stmt = null;
            if ( isset($this->_stmts['update_record']) ) {
                $stmt = $this->_stmts['update_record'];
            } else {
                $update_fields = array();
                foreach ( array_keys($values) as $field ) {
                    $update_fields[$field] = ':' . $field;
                }
                $update = $this->_zdb->update('matricules_file_format')
                    ->set($update_fields)
                    ->where(array('uniqid' => ':uniqid'));
                $stmt = $this->_zdb->sql->prepareStatementForSqlObject(
                    $update
                );
                $this->_stmts['update_record'] = $stmt;
            }

            $values['where1'] = $data['uniqid'];
            $add = $stmt->execute($values);
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

        $this->_addSubElements(
            $fragid,
            $indexes,
            $dates,
            $daos,
            $parents_titles
        );

        return $fragid;
    }

    /**
     * Adds sub elements
     *
     * @param string $fid     Fragment id
     * @param array  $indexes Indexes
     * @param array  $dates   Dates
     * @param array  $daos    Daos
     * @param array  $ptitles Parents titles
     *
     * @return void
     */
    private function _addSubElements($fid, $indexes, $dates, $daos, $ptitles)
    {
        if ( count($indexes) > 0 ) {
            //handle indexes
            $this->_storeSubElements(
                'ead_indexes',
                $indexes,
                $fid
            );
        }

        if ( count($dates) > 0 ) {
            //handle dates
            $this->_storeSubElements(
                'ead_dates',
                $dates,
                $fid
            );
        }

        if ( count($daos) > 0 ) {
            //handle daos
            $this->_storeSubElements(
                'ead_daos',
                $daos,
                $fid
            );
        }

        if ( count($ptitles) > 0 ) {
            //handle parents_titles
            $this->_storeSubElements(
                'ead_parent_title',
                $ptitles,
                $fid
            );
        }

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

        $where = array(
            'where1' => $fragment['uniqid']
        );

        //handle indexes
        $stmt = null;
        if ( isset($this->_stmts['select_indexes']) ) {
            $stmt = $this->_stmts['select_indexes'];
        } else {
            $select = $this->_zdb->select('ead_indexes')
                ->where(
                    array(
                        'eadfile_id' => ':eadfile_id'
                    )
                );
            $stmt = $this->_zdb->sql->prepareStatementForSqlObject(
                $select
            );
            $this->_stmts['select_indexes'] = $stmt;
        }
        $indexes = $stmt->execute($where);
        $fragment['descriptors'] = $this->_elementsAsArray(
            $indexes,
            'name',
            array('type', 'name'),
            true
        );

        //handle dates
        $stmt = null;
        if ( isset($this->_stmts['select_dates']) ) {
            $stmt = $this->_stmts['select_dates'];
        } else {
            $select = $this->_zdb->select('ead_dates')
                ->where(
                    array(
                        'eadfile_id' => ':eadfile_id'
                    )
                );
            $stmt = $this->_zdb->sql->prepareStatementForSqlObject(
                $select
            );
            $this->_stmts['select_dates'] = $stmt;
        }
        $dates = $stmt->execute($where);
        $fragment['cDate'] = $this->_elementsAsArray(
            $dates,
            'date',
            array('date', 'begin', 'end')
        );

        //handle daos
        $stmt = null;
        if ( isset($this->_stmts['select_daos']) ) {
            $stmt = $this->_stmts['select_daos'];
        } else {
            $select = $this->_zdb->select('ead_daos')
                ->where(
                    array(
                        'eadfile_id' => ':eadfile_id'
                    )
                );
            $stmt = $this->_zdb->sql->prepareStatementForSqlObject(
                $select
            );
            $this->_stmts['select_daos'] = $stmt;
        }
        $daos = $stmt->execute($where);
        $fragment['daolist'] = $this->_elementsAsArray(
            $daos,
            null,
            array()
        );

        if ( $fragment['parents'] ) {
            $stmt = null;
            if ( isset($this->_stmts['select_ptitles']) ) {
                $stmt = $this->_stmts['select_ptitles'];
            } else {
                $select = $this->_zdb->select('ead_parent_title')
                    ->where(
                        array(
                            'eadfile_id' => ':eadfile_id'
                        )
                    );
                $stmt = $this->_zdb->sql->prepareStatementForSqlObject(
                    $select
                );
                $this->_stmts['select_ptitles'] = $stmt;
            }
            $parents_titles = $stmt->execute($where);
            $pids = explode('/', $fragment['parents']);
            $i = 0;
            foreach ( $parents_titles as $ptitle ) {
                $pid = $pids[$i];
                $fragment['parents_titles'][$pid] = $ptitle['unittitle'];
                $i++;
            }
        }

        $obj = new Entity\EADFileFormat(
            $fragment,
            false
        );
        $obj->hydrate($newvals);

        if ( $obj->hasChanges() ) {
            $values = $obj->toArray();

            unset($values['indexes']);
            unset($values['dates']);
            unset($values['daos']);
            unset($values['parents_titles']);

            $stmt = null;
            if ( isset($this->_stmts['update_record']) ) {
                $stmt = $this->_stmts['update_record'];
            } else {
                $update_fields = array();
                foreach ( array_keys($values) as $field ) {
                    $update_fields[$field] = ':' . $field;
                }
                $update = $this->_zdb->update('ead_file_format')
                    ->set($update_fields)
                    ->where(array('uniqid' => ':uniqid'));
                $stmt = $this->_zdb->sql->prepareStatementForSqlObject(
                    $update
                );
                $this->_stmts['update_record'] = $stmt;
            }

            $values['where1'] = $fragment['uniqid'];
            $add = $stmt->execute($values);

            $removed = $obj->getRemoved();
            if ( count($removed) > 0 ) {
                foreach ( $removed as $r ) {
                    $meta = $this->_entityManager->getClassMetadata(get_class($r));
                    //table name from entity
                    $table_name = $meta->getTablename();

                    $delete = $this->_zdb->delete($table_name)
                        ->where(
                            array(
                                'id' => $r->getId()
                            )
                        );
                    $this->_zdb->execute($delete);
                }
            }

            //take care of new related data
            $indexes = $obj->getIndexes();
            $indexes_to_store = array();
            foreach ( $indexes as $index ) {
                if ( $index->getId() === null ) {
                    $indexes_to_store[] = $index->toArray();
                }
            }

            $dates = $obj->getDates();
            $dates_to_store = array();
            foreach ( $dates as $date ) {
                if ( $date->getId() === null ) {
                    $dates_to_store[] = $date->toArray();
                }
            }

            $daos = $obj->getDaos();
            $daos_to_store = array();
            foreach ( $daos as $dao ) {
                if ( $dao->getId() === null ) {
                    $daos_to_store[] = $dao->toArray();
                }
            }

            $p_titles = $obj->getParentsTitles();
            $p_titles_to_store = array();
            foreach ( $p_titles as $p_title ) {
                if ( $p_title->getId() === null ) {
                    $p_titles_to_store[] = $p_title->toArray();
                }
            }

            $this->_addSubElements(
                $fragment['uniqid'],
                $indexes_to_store,
                $dates_to_store,
                $daos_to_store,
                $p_titles_to_store
            );

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
        $stmt = null;
        if ( isset($this->_stmts['store_subs_' . $table]) ) {
            $stmt = $this->_stmts['store_subs_' . $table];
        } else {
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
            $this->_stmts['store_subs_' . $table] = $stmt;
        }

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
