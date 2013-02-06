<?php
namespace Anph\AdministrationBundle\Entity\SolrCore;

use Anph\AdministrationBundle\Entity\SolrCore\BachCoreAdminConfigReader;
use Aura\Http\Message\Response\Stack;
use Aura\Http\Message\Request;
use Exception;

/**
 * This class allows to manage Solr cores
 */
class SolrCoreAdmin
{
    /*const CORE_PATH = '/var/solr/';
    const CORE_HTTP = 'http://localhost:8080/solr/admin/cores';
    const CORE_DIR_TEMPLATE_PATH = '/var/solr/coreTemplate';
    const DATA_DIR = 'data';
    const CONFIG_DIR = 'conf';
    const SOLRCONFIG_FILE_NAME = 'solrconfig.xml';
    const SCHEMA_FILE_NAME = 'schema.xml';*/
    const DELETE_INDEX = 0;
    const DELETE_DATA = 1;
    const DELETE_CORE = 2;

    private $http;
    private $reader;
    
    /**
     * Constructor. Creates a necessary object to send queries.
     */
    public function __construct(BachCoreAdminConfigReader $reader = null)
    {
        if ($reader == null) {
            $this->reader = new BachCoreAdminConfigReader();
        } else {
            $this->reader = $reader;
        }
        $this->http = include 'vendor/aura/http/scripts/instance.php';
    }

    /**
     * Create core with specified name. If a core directory or core with such name
     * already exists this function returns false otherwise it returns SolrCoreResponse object.
     * @param string $coreName
     * @param string $coreInstanceDir directory of core instance
     * @param boolean $evenIfInstanceDirAlreadyExist 
     * @return boolean|\Anph\AdministrationBundle\Entity\SolrCore\SolrCoreResponse
     */
    public function create($coreName, $coreInstanceDir, $evenIfInstanceDirAlreadyExist = false)
    {
        $coreInstanceDirPath = $this->reader->getCoresPath() . $coreInstanceDir;
        // Test if the core does not already exist.
        if ($this->isCoreExist($coreName)) {
            return false;
        }
        // Test if we want create core even if the directory $coreInstanceDir already exist.
        if ($evenIfInstanceDirAlreadyExist) {
            if (!is_dir($coreInstanceDirPath) && !$this->createCoreDir($coreInstanceDirPath)) {
                return false;
            }
        } else {
            if (is_dir($coreInstanceDirPath)) {
                return false;
            } else {
                if (!$this->createCoreDir($coreInstanceDirPath)) {
                    return false;
                }
            }
        }
        return $this->send($this->reader->getCoresURL() . '?action=CREATE&' .
                            'name=' . $coreName . '&' .
                            'instanceDir=' . $coreInstanceDir . '&' .
                            'config=' . $this->reader->getConfigFileName() . '&' .
                            'schema=' . $this->reader->getSchemaFileName() . '&' .
                            'dataDir=' . $this->reader->getCoreDataDir());
    }

    /**
     * Get status of one or all cores. If $coreName parameter does not specified,
     * the status of all cores will be return.
     * @param string $coreName
     * @return \Anph\AdministrationBundle\Entity\SolrCore\SolrCoreResponse
     */
    public function getStatus($coreName = null)
    {
        if ($coreName != null) {
           return $this->send($this->reader->getCoresURL() . '?action=STATUS&' .
                                'core=' . $coreName);
        } else {
           return $this->send($this->reader->getCoresURL() . '?action=STATUS');
        }
    }

    /**
     * Reload core.
     * @param string $coreName
     * @return \Anph\AdministrationBundle\Entity\SolrCore\SolrCoreResponse
     */
    public function reload($coreName)
    {
        return $this->send($this->reader->getCoresURL() . '?action=RELOAD&' .
                            'core=' . $coreName);
    }

    /**
     * Renames a core. If core does not exist, returns false.
     * @param string $oldCoreName
     * @param string $newCoreName
     * @return \Anph\AdministrationBundle\Entity\SolrCore\SolrCoreResponse|boolean
     */
    public function rename($oldCoreName, $newCoreName)
    {
        if (!$this->isCoreExist($newCoreName)) {
            
            return $this->send($this->reader->getCoresURL() . '?action=RENAME&' .
                                'core=' . $oldCoreName .'&' .
                                'other=' . $newCoreName);
        }
        
        return false;
    }

    /**
     * Swaps two cores.
     * @param string $core1
     * @param string $core2
     * @return \Anph\AdministrationBundle\Entity\SolrCore\SolrCoreResponse
     */
    public function swap($core1, $core2)
    {
        return $this->send($this->reader->getCoresURL() . '?action=SWAP&' .
                            'core=' . $core1 .'&' .
                            'other=' . $core2);
    }

    /**
     * Removes a core from Solr.
     * @param string $coreName
     * @return \Anph\AdministrationBundle\Entity\SolrCore\SolrCoreResponse
     */
    public function unload($coreName)
    {
        return $this->send($this->reader->getCoresURL() . '?action=UNLOAD&' .
                            'core=' . $coreName);
    }

    /**
     * Removes a core from Solr and deletes related files. If $type parameter equals to
     * DELETE_INDEX : deletes the index
     * DELETE_DATA : removes "data" and all sub-directories
     * DELETE_CORE : removes core directory and all sub-directories. NOTE: it does not work if you had changed
     * core's name before (because core's directory does not equal to its name)
     * @param string $coreName
     * @param int $type
     * @return \Anph\AdministrationBundle\Entity\SolrCore\SolrCoreResponse
     */
    public function delete($coreName, $type = self::DELETE_CORE)
    {
        $url = $this->reader->getCoresURL() . '?action=UNLOAD&' .
                'core=' . $coreName . '&';
        switch ($type) {
            case self::DELETE_INDEX:
                $url .= 'deleteIndex=true';
                return $this->send($url);
            case self::DELETE_DATA:
                $url .= 'deleteDataDir=true';
                return $this->send($url);
            case self::DELETE_CORE:
                // Get core status for retreive core instance directory.
                $responseStatus = $this->getStatus($coreName);
                $coreInstanceDir = $responseStatus->getCoreStatus($coreName)->getInstanceDir();
                // Unload core
                $response = $this->unload($coreName);
                if (!$response->isOk()) {
                    return false;
                }
                // Delete core instance directory. If we do not succeed, we recreate the core we have just unloaded
                $result = $this->deleteCoreDir($coreInstanceDir);
                if (!$result) {
                    $this->create($coreName, $coreInstanceDir);
                }
                return $result;
            default :
                return false;
        }
    }
    
    public function getSchemaPath($coreName)
    {
        $coreInstanceDir = $this->getStatus($coreName)->getCoreStatus($coreName)->getInstanceDir();
        return $coreInstanceDir . $this->reader->getCoreConfigDir() . '/' . $this->reader->getSchemaFileName();
    }

    /**
     * Create core directory with the same name as core name. If a such directory already exist, returns false.
     * @param string $coreName
     * @return boolean
     */
    private function createCoreDir($coreInstanceDirPath)
    {
        if (!is_dir($coreInstanceDirPath)) {
            exec('cp -r -a "' . $this->reader->getCoreTemplatePath() . '" "' . $coreInstanceDirPath . '"', $output, $status);
            
            return $status == 0 ? true : false;
        }
        return false;
    }

    /**
     * Deletes core directory and all sub-directories. Returns true in successe or false in case of failure
     * @param string $dirName
     * @return boolean
     */
    private function deleteCoreDir($coreInstanceDirPath)
    {
        if (is_dir($coreInstanceDirPath)) {
            exec('rm -r "' . $coreInstanceDirPath . '"', $output, $status);
            
            return $status == 0 ? true : false;
        }
        
        return true;
    }
    
    /**
     * Verify whether a core exist. Throw an exception if can not obtain Solr cores status.
     * @param string $coreName
     * @throws Exception
     * @return boolean
     */
    private function isCoreExist($coreName)
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
     * Sends an HTTP query (GET method) to Solr and returns result (SolrCoreResponse object).
     * @param string $url
     * @return \Anph\AdministrationBundle\Entity\SolrCore\SolrCoreResponse
     */
    private function send($url)
    {
        $request = $this->http->newRequest();
        $request->setMethod(Request::METHOD_GET);
        $request->setUrl($url);
        $stack = $this->http->send($request);
        return new SolrCoreResponse($stack[0]->content);
    }
}
