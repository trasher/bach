<?php
/**
 * Bach 1.0.1 migration file
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
 * @category Migrations
 * @package  Bach
 * @author   Vincent Fleurette <vincent.fleurette@anaphore.eu>
 * @author   Sebastien Chaptal <sebastien.chaptal@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */

namespace Bach\Migrations;

require_once 'BachMigration.php';

use Doctrine\DBAL\Schema\Schema;
use Bach\HomeBundle\Entity\Comment;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Bach 1.0.1 migration file
 *
 * @category Migrations
 * @package  Bach
 * @author   Vincent Fleurette <vincent.fleurette@anaphore.eu>
 * @author   Sebastien Chaptal <sebastien.chaptal@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class Version101 extends BachMigration implements ContainerAwareInterface
{
    private $_container;

    /**
     * Sets container
     *
     * @param ContainerInterface $container Container
     *
     * @return void
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->_container = $container;
    }

    /**
     * Ups database schema
     *
     * @param Schema $schema Database schema
     *
     * @return void
     */
    public function up(Schema $schema)
    {
        $this->checkDbPlatform();

        $columns = array(
            'id' => array(
                'type'     => 'string',
                 'options' => array(
                     'length' => 17,
                )
            ),
            'url' => array(
                'type'     => 'string',
                 'options' => array(
                     'length'   => 255,
                     'notnull' => false,
                )
            ),
            'c_url' => array(
                'type'     => 'string',
                 'options' => array(
                     'length'   => 255,
                     'notnull' => false,
                )
            ),
            '`server name`' => array(
                'type'     => 'string',
                 'options' => array(
                    'length'   => 255,
                    'notnull' => false,
                )
            ),
            'type' => array(
                'type'     => 'integer',
                 'options' => array(
                     'length'   => 11,
                     'notnull' => false,
                )
            ),
            'perfdata' => array(
                'type'     => 'blob',
                'options' => array()
            ),
            'cookie' => array(
                'type'     => 'string',
                'options' => array()
            ),
            'post' => array(
                'type' => 'blob',
                'options' => array()
            ),
            'get' => array(
                'type' => 'blob',
                'options' => array()
            ),
            'pmu' => array(
                'type' => 'integer',
                 'options' => array(
                    'length' => '11',
                    'notnull' => false,
                )
            ),
            'wt' => array(
                'type' => 'integer',
                'options' => array(
                    'length' => '11',
                    'notnull' => false,
                )
            ),
            'cpu' => array(
                'type' => 'integer',
                 'options' => array(
                    'length' => '11',
                     'notnull' => false,
                )
            ),
            'server_id' => array(
                'type' => 'string',
                 'options' => array(
                    'length' => '255'
                )
            ),
            'aggregateCalls_include' => array(
                'type' => 'string',
                 'options' => array(
                    'length' => '255',
                    'notnull' => false,
                )
            ),
            'timestamp' => array(
                'type'    => 'datetime',
                'options' => array()
            )
        );
        $this->createTable($schema, 'details', $columns, 'id');

        $table = $schema->getTable('facets');
        $table->addColumn(
            'on_home',
            'boolean'
        );

        $this->connection->executeQuery(
            "UPDATE facets SET `solr_field_name` = 'cDateBegin'" .
            " WHERE `solr_field_name` = 'cDate'"
        );
    }

    /**
     * Downs database schema
     *
     * @param Schema $schema Database Schema
     *
     * @return void
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('details');
        $table = $schema->getTable('facets');
        $table->dropColumn('on_home');
        $this->connection->executeQuery(
            "UPDATE facets SET `solr_field_name` = 'cDate'" .
            " WHERE `solr_field_name` = 'cDateBegin'"
        );

    }
}
