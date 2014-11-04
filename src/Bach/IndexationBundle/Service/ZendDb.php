<?php
/**
 * Zend database
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

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;

/**
 * Zend database
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
class ZendDb
{
    private $_db;
    private $_type_db;
    private $_sql;

    const MYSQL = 'mysql';
    const PGSQL = 'pgsql';
    const SQLITE = 'sqlite';

    const MYSQL_DEFAULT_PORT = 3306;
    const PGSQL_DEFAULT_PORT = 5432;

    /**
     * Main constructor
     *
     * @param string $driver      Database driver name
     * @param string $host        Database hostname
     * @param string $port        Database port
     * @param string $db_name     Database name
     * @param string $db_user     Database username
     * @param string $db_password Database user password
     */
    function __construct($driver, $host, $port, $db_name, $db_user, $db_password)
    {
        $this->_type_db = $driver;

        try {
            $_options = array(
                'driver'   => $driver,
                'hostname' => $host,
                'port'     => $port,
                'username' => $db_user,
                'password' => $db_password,
                'database' => $db_name,
                'charset'   => 'utf8'

            );

            $this->_db = new Adapter($_options);
            $this->_db->getDriver()->getConnection()->connect();
            $this->_sql = new Sql($this->_db);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Peform a select query on the whole table
     *
     * @param string $table Table name
     *
     * @return array
     */
    public function selectAll($table)
    {
        return $this->_db->query(
            'SELECT * FROM ' . $table,
            Adapter::QUERY_MODE_EXECUTE
        );
    }

    /**
     * Get columns for a specified table
     *
     * @param string $table Table name
     *
     * @return array
     */
    public function getColumns($table)
    {
        $metadata = new \Zend\Db\Metadata\Metadata($this->_db);
        $table = $metadata->getTable($table);
        return $table->getColumns();
    }

    /**
     * Is current database using Postgresql?
     *
     * @return boolean
     */
    public function isPostgres()
    {
        return $this->_type_db === 'pdo_pgsql';
    }

    /**
     * Instanciate a select query
     *
     * @param string $table Table name, without prefix
     * @param string $alias Tables alias, optionnal
     *
     * @return Select
     */
    public function select($table, $alias = null)
    {
        if ( $alias === null ) {
            return $this->_sql->select($table);
        } else {
            return $this->_sql->select(
                array(
                    $alias => $table
                )
            );
        }
    }

    /**
     * Instanciate an insert query
     *
     * @param string $table Table name, without prefix
     *
     * @return Insert
     */
    public function insert($table)
    {
        return $this->_sql->insert($table);
    }

    /**
     * Instanciate an update query
     *
     * @param string $table Table name, without prefix
     *
     * @return Insert
     */
    public function update($table)
    {
        return $this->_sql->update($table);
    }

    /**
     * Instanciate a delete query
     *
     * @param string $table Table name, without prefix
     *
     * @return Delete
     */
    public function delete($table)
    {
        return $this->_sql->delete($table);
    }

    /**
     * Execute query string
     *
     * @param SqlInterface $sql     SQL object
     * @param boolean      $verbose Show executed queries
     *
     * @return Stmt
     */
    public function execute($sql, $verbose = false)
    {
        try {
            $query_string = $this->_sql->getSqlStringForSqlObject($sql);
            $this->_last_query = $query_string;

            if ( $verbose === true ) {
                echo 'Executing query: ' . $query_string;
            }
            return $this->_db->query(
                $query_string,
                Adapter::QUERY_MODE_EXECUTE
            );
        } catch ( \Exception $e ) {
            throw $e;
        }
    }

    /**
     * Global getter method
     *
     * @param string $name name of the variable we want to retrieve
     *
     * @return mixed
     */
    public function __get($name)
    {
        switch ( $name ) {
        case 'db':
            return $this->_db;
            break;
        case 'sql':
            return $this->_sql;
            break;
        case 'driver':
            return $this->_db->getDriver();
            break;
        case 'connection':
            return $this->_db->getDriver()->getConnection();
            break;
        case 'platform':
            return $this->_db->getPlatform();
            break;
        case 'query_string':
            return $this->_last_query;
            break;
        case 'type_db':
            return $this->_type_db;
            break;
        }
    }

    /**
     * Get last autoincrement
     *
     * @param string $table Table name
     *
     * @return int
     */
    public function getAutoIncrement($table)
    {
        if ( $this->isPostgres() ) {
            return $this->driver->getLastGeneratedValue($table . '_id_seq');
        } else {
            return $this->driver->getLastGeneratedValue();
        }
    }
}
