<?php
/**
 * Bach solr core administration
 *
 * PHP version 5
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Anph\AdministrationBundle\Entity\SolrCore;

use Anph\AdministrationBundle\Entity\SolrCore\BachCoreAdminConfigReader;
use Aura\Http\Message\Response\Stack;
use Aura\Http\Message\Request;
use Exception;
use DOMDocument;
use DOMElement;

/**
 * Bach solr core administration
 *
 * PHP version 5
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class SolrCoreAdmin
{
    const DELETE_INDEX = 0;
    const DELETE_DATA = 1;
    const DELETE_CORE = 2;

    private $_reader;

    /**
     * Constructor. Creates a necessary object to send queries.
     *
     * @param BachCoreAdminConfigReader $reader Config reader.
     */
    public function __construct(BachCoreAdminConfigReader $reader = null)
    {
        if ($reader == null) {
            $this->_reader = new BachCoreAdminConfigReader();
        } else {
            $this->_reader = $reader;
        }
    }

    /**
     * Proceeed full import
     *
     * @param string $coreName Solr core name
     *
     * @return SolrCoreResponse
     */
    public function fullImport($coreName)
    {
        return $this->_send(
            $this->_reader->getCoresURL() . '/' . $coreName . '/dataimport',
            array('command' => 'full--import')
        );
    }

    /**
     * Proceed delta import
     *
     * @param string $coreName Solr core name
     *
     * @return SolrCoreResponse
     */
    public function deltaImport($coreName)
    {
        return $this->_send(
            $this->_reader->getCoresURL() . '/' . $coreName . '/dataimport',
            array('command' => 'delta-import')
        );
    }

    /**
     * Retrieve import status
     *
     * @return SolrCoreResponse
     */
    public function getImportStatus()
    {
        return $this->_send(
            $this->_reader->getCoresURL() . '/' . $coreName . '/dataimport'
        );
    }

    /**
     * Create core with specified name. If a core directory or core of that name
     * already exists this function returns false otherwise it returns
     * SolrCoreResponse object.
     *
     * @param stirng  $coreName        Solr core name
     * @param string  $coreInstanceDir Directory of core instance
     * @param string  $tableName       Database table name
     * @param array   $fields          Database fields
     * @param boolean $evenIfDirExist  Create even if dir already exists
     *
     * @return boolean|SolrCoreResponse
     */
    public function create($coreName, $coreInstanceDir,
        $tableName, $fields, $evenIfDirExist = false
    ) {
        $coreInstanceDirPath = null;

        //check if cores dir is writeable
        if ( is_writeable($this->_reader->getCoresPath()) ) {
            //it is, we can directly create new core
            $coreInstanceDirPath = $this->_reader->getCoresPath() . $coreInstanceDir;
        } else {
            //cores dir is read only or does not exists locally,
            //let's use a temporary dir for new core creation
            $coreInstanceDirPath = $this->_reader->getTempCorePath() .
                $coreInstanceDir;
        }

        // Test if the core does not already exist.
        if ($this->_coreExist($coreName)) {
            return false;
        }
        //Test if we want create core even if the directory $coreInstanceDir
        //already exist.
        if ($evenIfDirExist) {
            if (!is_dir($coreInstanceDirPath)
                && !$this->_createCoreDir($coreInstanceDirPath, $tableName, $fields)
            ) {
                return false;
            }
        } else {
            if (is_dir($coreInstanceDirPath)) {
                return false;
            } else {
                $created = $this->_createCoreDir(
                    $coreInstanceDirPath,
                    $tableName,
                    $fields
                );
                if ( !$created ) {
                    return false;
                }
            }
        }
        return $this->_send(
            $this->_reader->getCoresURL() . '/admin/cores',
            array(
                'action'        => 'CREATE',
                'name'          => $coreName,
                'instanceDir'   => $coreInstanceDir,
                'config'        => $this->_reader->getConfigFileName(),
                'schema'        => $this->_reader->getSchemaFileName(),
                'dataDir'       => $this->_reader->getCoreDataDir()
            )
        );
    }

    /**
     * Get status of one or all cores. If $coreName parameter does not specified,
     * the status of all cores will be return.
     *
     * @param string $coreName Solr core name
     *
     * @return SolrCoreResponse
     */
    public function getStatus($coreName = null)
    {
        $options = array(
            'action' => 'STATUS'
        );
        if ($coreName != null) {
            $options['core'] = $coreName;
        }
        return $this->_send(
            $this->_reader->getCoresURL() . '/admin/cores',
            $options
        );
    }

    /**
     * Reload core.
     *
     * @param string $coreName Solr core name
     *
     * @return SolrCoreResponse
     */
    public function reload($coreName)
    {
        return $this->_send(
            $this->_reader->getCoresURL() . '/admin/cores',
            array(
                'action'    => 'RELOAD',
                'core'      => $coreName
            )
        );
    }

    /**
     * Renames a core. If core does not exist, returns false.
     *
     * @param string $oldCoreName Existing core name
     * @param string $newCoreName New core name
     *
     * @return SolrCoreResponse|boolean
     */
    public function rename($oldCoreName, $newCoreName)
    {
        if (!$this->_coreExist($newCoreName)) {
            return $this->_send(
                $this->_reader->getCoresURL() . '/admin/cores',
                array(
                    'action'    => 'RENAME',
                    'core='     => $oldCoreName,
                    'other'     => $newCoreName
                )
            );
        }
        
        return false;
    }

    /**
     * Swaps two cores.
     *
     * @param string $core1 First core name
     * @param string $core2 Second core name
     *
     * @return SolrCoreResponse
     */
    public function swap($core1, $core2)
    {
        return $this->_send(
            $this->_reader->getCoresURL() . '/admin/cores',
            array(
                'action'    => 'SWAP',
                'core'      => $core1,
                'other'     => $core2
            )
        );
    }

    /**
     * Removes a core from Solr.
     *
     * @param string $coreName Solr core name
     *
     * @return SolrCoreResponse
     */
    public function unload($coreName)
    {
        return $this->_send(
            $this->_reader->getCoresURL() . '/admin/cores',
            array(
                'action'    => 'UNLOAD',
                'core'      => $coreName
            )
        );
    }

    /**
     * Removes a core from Solr and deletes related files.
     * If $type parameter equals to:
     * DELETE_INDEX : deletes the index
     * DELETE_DATA : removes "data" and all sub-directories
     * DELETE_CORE : removes core directory and all sub-directories.
     *     NOTE: it does not work if you had changed core's name before
     *     (because core's directory does not equal to its name)
     *
     * @param string $coreName Solr core name
     * @param int    $type     Delete type
     *
     * @return SolrCoreResponse
     */
    public function delete($coreName, $type = self::DELETE_CORE)
    {
        $options = array(
            'action'    => 'UNLOAD',
            'core'      => $coreName
        );

        switch ($type) {
        case self::DELETE_INDEX:
            $options['deleteIndex'] = 'true';
            break;
        case self::DELETE_DATA:
            $options['deleteDataDir'] = 'true';
            break;
        case self::DELETE_CORE:
            // Get core status for retreive core instance directory.
            $responseStatus = $this->getStatus($coreName);
            $coreInstanceDir = $responseStatus->getCoreStatus($coreName)->getInstanceDir();
            // Unload core
            $response = $this->unload($coreName);
            if (!$response->isOk()) {
                return false;
            }
            // Delete core instance directory. If we do not succeed,
            //we recreate the core we have just unloaded
            $result = $this->_deleteCoreDir($coreInstanceDir);
            if (!$result) {
                $this->create($coreName, $coreInstanceDir);
            }
            return $result;
            break;
        default :
            return false;
        }

        return $this->_send(
            $this->_reader->getCoresURL() . '/admin/cores',
            $options
        );
    }

    /**
     * Get core schema file path
     *
     * @param string $coreName Solr core name
     *
     * @return string
     */
    public function getSchemaPath($coreName)
    {
        $coreInstanceDir = $this->getStatus($coreName)->getCoreStatus($coreName)->getInstanceDir();
        return $this->_reader->getSolrSchemaFileName($coreName);
    }

    /**
     * Get core config file path
     *
     * @param string $coreName Solr core name
     *
     * @return string
     */
    public function getConfigPath($coreName)
    {
        $coreInstanceDir = $this->getStatus($coreName)->getCoreStatus($coreName)->getInstanceDir();
        return $coreInstanceDir . $this->_reader->getCoreConfigDir() . '/' .
           $this->_reader->getConfigFileName();
    }

    /**
     * Create core directory with the same name as core name.
     * If directory already exist, returns false.
     *
     * @param string $coreInstanceDirPath Core instance path
     * @param string $tableName           Database table name
     * @param array  $fields              Database fields
     *
     * @return boolean
     */
    private function _createCoreDir($coreInstanceDirPath, $tableName, $fields)
    {
        if (!is_dir($coreInstanceDirPath)) {
            $template  = $this->_reader->getCoreTemplatePath();
            $cmd = 'cp -r "' . $template . '" "' . $coreInstanceDirPath .
                '" 2>&1';
            exec(
                $cmd,
                $output,
                $status
            );

            if ( $status !== 0 ) {
                throw new \RuntimeException(
                    implode("\n", $output) .
                    "\nrunning: " . $cmd
                );
            }

            $this->_addFieldsByDefault($coreInstanceDirPath, $fields);
            $this->_createDataConfigFile($coreInstanceDirPath, $tableName, $fields);
            return $status == 0 ? true : false;
        }
        return false;
    }

    /**
     * Add fields by default?
     *
     * @param string $coreInstanceDirPath Core instance dir
     * @param array  $fields              Fields?
     *
     * @return void
     */
    private function _addFieldsByDefault($coreInstanceDirPath, $fields)
    {
        $schemaFilePath = $coreInstanceDirPath . '/' .
            $this->_reader->getCoreConfigDir() . '/' .
            $this->_reader->getSchemaFileName();
        $doc = new DOMDocument();
        $doc->load($schemaFilePath);
        // Creation of fields
        $elt = $doc->getElementsByTagName('fields')->item(0);
        foreach ($fields as $f) {
            /**
             * FIXME: all fields should probably not be string,
             * also, some should be stored, mutlivalued, ...
             */
            $newFieldType = $doc->createElement('field');
            $newFieldType->setAttribute('name', $f);
            $newFieldType->setAttribute('type', 'string');
            $elt->appendChild($newFieldType);
        }
        //TODO: add fulltext field
        //TODO: add relevant copyField
        $doc->documentElement->appendChild($elt);
        $doc->save($schemaFilePath);
    }

    /**
     * Deletes core directory and all sub-directories.
     *
     * @param string $coreInstanceDirPath Directory name
     *
     * @return boolean
     */
    private function _deleteCoreDir($coreInstanceDirPath)
    {
        if (is_dir($coreInstanceDirPath)) {
            exec('rm -r "' . $coreInstanceDirPath . '"', $output, $status);
            return $status == 0 ? true : false;
        }
        return true;
    }

    /**
     * Verify whether a core exist. Throw an exception if can not
     * obtain Solr cores status.
     *
     * @param string $coreName Solr core name
     *
     * @throws Exception
     *
     * @return boolean
     */
    private function _coreExist($coreName)
    {
        try {
            $status = $this->getStatus();
            if ($status->isOk()) {
                $cores = $status->getCoreNames();
                $isExist = false;
                foreach ($cores as $c) {
                    if ($c == $coreName) {
                        return true;
                    }
                }
                return false;
            } else {
                throw new Exception('Can not obtain Solr cores status');
            }
        } catch (Exception $e) {
            echo 'Caught exception : ' .  $e->getMessage();
        }
    }

    /**
     * Sends an HTTP query (POST method) to Solr and returns
     * result as a SolrCoreResponse object.
     *
     * @param string $url     HTTP URL
     * @param array  $options Request options
     *
     * @return SolrCoreResponse
     */
    private function _send($url, $options = null)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if ( $options !== null && is_array($options) ) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $options);
        }

        $response = curl_exec($ch);
        if ( $response === false ) {
            throw new \RuntimeException(
                "Error on request:\n\tURI:" . $url . "\n\toptions:\n" .
                print_r($options, true)
            );
        }

        //get request infos
        $infos = curl_getinfo($ch);
        if ( $infos['http_code'] !== 200 ) {
            $trace = debug_backtrace();
            $caller = $trace[1];

            //FIXME: at this point, core has been created in temporary space,
            //but is failing to load in solr. User will have to copy the new
            //core at the right place, and then rerun core creation.

            throw new \RuntimeException(
                'Something went wrong in function ' . __CLASS__ . '::' .
                $caller['function'] . "\nHTTP Request URI: " . $url .
                "\nSent options: " . print_r($options, true) .
                "\nCheck cores status for more informations."
            );
        }

        return new SolrCoreResponse($response);
    }

    /**
     * Create data config file
     *
     * @param string $coreInstanceDirPath Core instance path
     * @param string $tableName           Database table name
     * @param array  $fields              Database fields
     *
     * @return void
     */
    private function _createDataConfigFile($coreInstanceDirPath, $tableName, $fields)
    {
        $dataConfigFilePath = $coreInstanceDirPath . '/' .
            $this->_reader->getCoreConfigDir() . '/' .
            $this->_reader->getDataConfigFileName();
        $doc = new DOMDocument();
        $doc->load($dataConfigFilePath);
        $databaseParameters = $this->_reader->getDatabaseParameters();
        $elt = $doc->getElementsByTagName('dataSource')->item(0);
        $elt->setAttribute('type', $databaseParameters['type']);
        $elt->setAttribute('driver', $databaseParameters['driver']);
        $elt->setAttribute('url', $databaseParameters['url']);
        $elt->setAttribute('user', $databaseParameters['user']);
        $elt->setAttribute('password', $databaseParameters['password']);
        $newField = $doc->createElement('field');
        $newField->setAttribute('column', $fields[0]);
        $newField->setAttribute('name', $fields[0]);
        $elt = $doc->getElementsByTagName('entity')->item(0);
        $elt->appendChild($newField);
        $query = 'SELECT ' . $fields[0];
        for ($i = 1; $i < count($fields); $i++) {
            $query .= ',' . $fields[$i];
            $newField = $doc->createElement('field');
            $newField->setAttribute('column', $fields[$i]);
            $newField->setAttribute('name', $fields[$i]);
            $elt->appendChild($newField);
        }
        $query .= ' FROM ' . $tableName; 
        $elt->setAttribute('query', $query);
        $doc->save($dataConfigFilePath);
    }
}
