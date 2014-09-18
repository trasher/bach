<?php
/**
 * Bach 1.0.0 migration file
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
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */

namespace Bach\Migrations;

require_once 'BachMigration.php';

use Doctrine\DBAL\Schema\Schema;
use Bach\HomeBundle\Entity\Comment;

/**
 * Bach 1.0.0 migration file
 *
 * @category Migrations
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class Version100 extends BachMigration
{

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

        $table = $schema->getTable('comments');

        $fkeys = $table->getForeignKeys();
        foreach ( $fkeys as $fkey ) {
            if ( $fkey->getColumns()[0] == 'eadfile_id' ) {
                $table->removeForeignKey($fkey->getName());
            }
        }

        $table->addColumn('docid', 'string', array('nullable' => true, 'length' => 500));
        //comment is required here to prevent Doctrine to change eadfile_id to related!!
        $table->addColumn('related', 'integer', array('comment' => 'Related document type'));
        $table->dropColumn('eadfile_id');
        //FIXME: before dropping column, we should copy its data to docid
    }

    /**
     * Post up instructions
     *
     * @param Schema $schema Database schema
     *
     * @return void
     */
    public function postUp(Schema $schema)
    {
        $this->checkDbPlatform();
        $this->connection->executeQuery(
            'UPDATE comments SET related = ' . Comment::REL_ARCHIVES
        );
    }

    /**
     * Downs database schema
     *
     * @param Schema $schema Database schema
     *
     * @return void
     */
    public function down(Schema $schema)
    {
        $this->checkDbPlatform();

        $table = $schema->getTable('comments');

        $table->dropColumn('related');
        $table->dropColumn('docid');
        $table->addColumn('eadfile_id', 'integer');
        $table->addForeignKeyConstraint(
            $schema->getTable('ead_file_format'),
            array('eadfile_id'),
            array('uniqid')
        );
        //FIXME: we should bring back data from docid column to eadfile_id
    }

}
