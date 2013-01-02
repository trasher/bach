<?php
namespace Anph\AdministrationBundle\Entity\SolrCore;

use Aura\Http\Message\Response\Stack;
use Aura\Http\Message\Request;
use Exception;

/**
 * This class allows to manage Solr cores
 */
class SolrCoreAdmin
{
    const CORE_PATH = '/var/solr/';
    const CORE_HTTP = 'http://localhost:8080/solr/admin/cores';
    const CORE_DIR_TEMPLATE_PATH = '/var/solr/coreTemplate';
    const DATA_DIR = 'data';
    const CONFIG_DIR = 'conf';
    const SOLRCONFIG_FILE_NAME = 'solrconfig.xml';
    const SCHEMA_FILE_NAME = 'schema.xml';
    const DELETE_INDEX = 0;
    const DELETE_DATA = 1;
    const DELETE_CORE = 2;

    private $http;
    
    /**
     * Constructor. Creates a necessary object to send queries.
     */
    public function __construct()
    {
        $this->http = include 'vendor/aura/http/scripts/instance.php';
    }

    /**
     * Create core with specified name. If a core directory or corewith such name
     * already exists this function returns false otherwise it returns SolrCoreResponse object.
     * @param string $coreName
     * @return boolean|\Anph\AdministrationBundle\Entity\SolrCore\SolrCoreResponse
     */
    public function create($coreName)
    {
        if ($this->isCoreExist($coreName) || !$this->createCoreDir($coreName)) {
            return false;
        }
        
        return $this->send(SolrCoreAdmin::CORE_HTTP . '?action=CREATE&' .
                            'name=' . $coreName . '&' .
                            'instanceDir=' . $coreName . '&' .
                            'config=' . SolrCoreAdmin::SOLRCONFIG_FILE_NAME . '&' .
                            'schema=' . SolrCoreAdmin::SCHEMA_FILE_NAME . '&' .
                            'dataDir=' . SolrCoreAdmin::DATA_DIR);
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
            
           return $this->send(SolrCoreAdmin::CORE_HTTP . '?action=STATUS&' .
                                'core=' . $coreName);
        } else {
            
           return $this->send(SolrCoreAdmin::CORE_HTTP . '?action=STATUS');
        }
    }

    /**
     * Reload core.
     * @param string $coreName
     * @return \Anph\AdministrationBundle\Entity\SolrCore\SolrCoreResponse
     */
    public function reload($coreName)
    {
        return $this->send(SolrCoreAdmin::CORE_HTTP . '?action=RELOAD&' .
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
            
            return $this->send(SolrCoreAdmin::CORE_HTTP . '?action=RENAME&' .
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
        return $this->send(SolrCoreAdmin::CORE_HTTP . '?action=SWAP&' .
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
        return $this->send(SolrCoreAdmin::CORE_HTTP . '?action=UNLOAD&' .
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
    public function delete($coreName, $type = SolrCoreAdmin::DELETE_CORE)
    {
        $url = SolrCoreAdmin::CORE_HTTP . '?action=UNLOAD&' .
                'core=' . $coreName . '&';
        switch ($type) {
            case SolrCoreAdmin::DELETE_INDEX:
                $url .= 'deleteIndex=true';
                break;
            case SolrCoreAdmin::DELETE_DATA:
                $url .= 'deleteDataDir=true';
                break;
            case SolrCoreAdmin::DELETE_CORE:
                $response = $this->unload($coreName);
                $boolResponse = $this->deleteCoreDir($coreName);
                return $boolResponse;
        }
        
        return $this->send($url);
    }

    /**
     * Create core directory with the same name as core name. If a such directory already exist, returns false.
     * @param string $coreName
     * @return boolean
     */
    private function createCoreDir($dirName)
    {
        $dest = SolrCoreAdmin::CORE_PATH . $dirName;
        if (!is_dir($dest)) {
            exec('cp -r -a "' . SolrCoreAdmin::CORE_DIR_TEMPLATE_PATH . '" "' . $dest . '"', $output, $status);
            
            return $status == 0 ? true : false;
        }
        
        return false;
    }

    /**
     * Deletes core directory and all sub-directories. Returns true in successe or false in case of failure
     * @param string $dirName
     * @return boolean
     */
    private function deleteCoreDir($dirName)
    {
        $path = SolrCoreAdmin::CORE_PATH . $dirName;
        if (is_dir($path)) {
            exec('rm -r "' . $path . '"', $output, $status);
            
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
