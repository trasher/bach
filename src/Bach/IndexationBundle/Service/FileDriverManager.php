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
     * @param boolean  $flush        Whether to flush
     * @param string   $preprocessor Preprocessor, if any (defaults to null)
     *
     * @return FileFormat the normalized file object
     */
    public function convert(DataBag $bag, $format, $doc, $flush,
        $preprocessor = null
    ) {
        if ( !array_key_exists($format, $this->_drivers) ) {
            throw new \DomainException('Unsupported file format: ' . $format);
        }

        $mapper = null;
        $fileformat_class = null;
        $doctrine_entity = null;

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

        //Zend test
        try {
            $this->_zdb->connection->beginTransaction();

            if ( $format === 'ead' ) {
                //handle eadheader
                $eadheader = $mapper->translateHeader(
                    $results['eadheader']
                );

                $select = $this->_zdb->select('ead_header')
                    ->limit(1)
                    ->columns(['id'])
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
                    $add = $this->_zdb->execute($insert, true);
                    if ( !$add->count() > 0 ) {
                        throw new \RuntimeException(
                            'An error occured storing EAD header ' .
                            $eadheader['headerId']
                        );
                    }
                    $headerid = $this->_zdb->getAutoIncrement('ead_header');
                } else {
                    $headerid = $eadh->id;
                }

                //handle archdesc
                $mapper->setEadId($eadheader['headerId']);
                $archdesc = new \Bach\IndexationBundle\Entity\EADFileFormat(
                    $mapper->translate(
                        $results['archdesc']
                    )
                );
                $archdesc = $archdesc->toArray();

                $select = $this->_zdb->select('ead_file_format')
                    ->limit(1)
                    ->columns(['uniqid'])
                    ->where(
                        array(
                            'fragmentid' => $eadheader['headerId'] . '_description'
                        )
                    );
                $archdesc_results = $this->_zdb->execute($select);
                $eada = $archdesc_results->current();

                if ( $eada === false ) {
                    //EAD archdesc does not exists yet. Store it.
                    $indexes = $archdesc['indexes'];
                    unset($archdesc['indexes']);
                    $dates = $archdesc['dates'];
                    unset($archdesc['dates']);
                    $daos = $archdesc['daos'];
                    unset($archdesc['daos']);
                    $parents_titles = $archdesc['parents_titles'];
                    unset($archdesc['parents_titles']);
                    $archdesc['eadheader_id'] = $headerid;
                    var_dump($archdesc);

                    $insert = $this->_zdb->insert('ead_file_format')
                        ->values($archdesc);
                    $add = $this->_zdb->execute($insert);
                    if ( !$add->count() > 0 ) {
                        throw new \RuntimeException(
                            'An error occured storing EAD archdesc ' .
                            $eadheader['headerId'] . '_description'
                        );
                    }
                    $archdescid = $this->_zdb->getAutoIncrement('ead_file_format');

                    if ( count($indexes) > 0 ) {
                        //handle indexes
                    }

                    if ( count($dates) > 0 ) {
                        $insert = $this->_zdb->insert('ead_dates');
                        $insert->values(
                            array(
                                'id'            => ':id',
                                'date'          => ':date',
                                'normal'        => ':normal',
                                'label'         => ':label',
                                'calendar'      => ':calendar',
                                'type'          => ':type',
                                'begin'         => ':begin',
                                'dend'          => ':dend',
                                'eadfile_id'    => ':eadfile_id'
                            )
                        );
                        $stmt = $this->_zdb->sql->prepareStatementForSqlObject(
                            $insert
                        );

                        foreach ( $dates as $date ) {
                            $date['eadfile_id'] = $archdescid;
                            $stmt->execute($date);
                        }
                    }

                    if ( count($daos) > 0 ) {
                        //handle daos
                    }

                    if ( count($parents_titles) > 0 ) {
                        //handle parents_titles
                    }

                } else {
                    $archdescid = $eada->uniqid;
                }

            }
            //$this->_zdb->commit();
            $this->_zdb->connection->rollBack();
        } catch ( \Exception $e ) {
            $this->_zdb->connection->rollBack();
            throw $e;
        }
        //End Zend test

        //disable SQL Logger...
        /*$this->_entityManager->getConnection()->getConfiguration()
            ->setSQLLogger(null);

        $repo = $this->_entityManager->getRepository($doctrine_entity);

        //EAD specific
        if ( $format === 'ead' ) {
            $headerrepo = $this->_entityManager
                ->getRepository('BachIndexationBundle:EADHeader');

            $results['eadheader'] = $mapper->translateHeader(
                $results['eadheader']
            );

            $eadheader = $headerrepo->findOneByHeaderId(
                $results['eadheader']['headerId']
            );

            if ( $eadheader === null ) {
                $eadheader = new \Bach\IndexationBundle\Entity\EADHeader(
                    $results['eadheader']
                );
            } else {
                $eadheader->hydrate($results['eadheader']);
            }

            $mapper->setEadId($eadheader->getHeaderId());
            $this->_entityManager->persist($eadheader);
            unset($headerrepo);

            $archdesc = $repo->findOneByFragmentid(
                $eadheader->getHeaderId() . '_description'
            );

            if ( $archdesc === null ) {
                $archdesc = new \Bach\IndexationBundle\Entity\EADFileFormat(
                    $mapper->translate(
                        $results['archdesc']
                    )
                );
                $archdesc->setEadheader($eadheader);
                $archdesc->setDocument($doc);
            } else {
                $archdesc->hydrate(
                    $mapper->translate(
                        $results['archdesc']
                    )
                );
                $archdesc->setDocument($doc);
            }
            $this->_entityManager->persist($archdesc);

            $results = $results['elements'];
        }

        foreach ($results as &$result) {
            $result = $mapper->translate($result);

            $exists = null;
            if ( isset($result['fragmentid']) ) {
                $exists = $repo->findOneByFragmentid($result['fragmentid']);
            } else {
                $exists = $repo->findOneById($result['id'][0]['value']);
            }

            $out = $this->_fileFormatFactory->build(
                $result,
                $fileformat_class,
                $exists
            );

            if ( $eadheader !== null ) {
                $out->setEadheader($eadheader);
            }
            if ( $archdesc !== null ) {
                $out->setArchdesc($archdesc);
            }

            $removed = $out->getRemoved();
            if ( count($removed) > 0 ) {
                foreach ( $removed as $r ) {
                    $this->_entityManager->remove($r);
                }
            }

            $out->setDocument($doc);
            $this->_entityManager->persist($out);

            $count++;
        }

        if ( $flush ) {
            $this->_entityManager->flush();
            $this->_entityManager->clear();
        }*/
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
