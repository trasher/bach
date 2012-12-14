<?php
namespace Anph\AdministrationBundle\Entity\SolrCore;

use Aura\Http\Message\Response\Stack;
use Aura\Http\Message\Request;
use Exception;

class SolrCoreAdmin
{
    const CORE_PATH = '/var/solr/';
    const CORE_HTTP = 'http://localhost:8080/solr/admin/cores';
    const CORE_DIR_TEMPLATE = 'coreTemplate';
    const DATA_DIR = 'data';
    const CONFIG_DIR = 'conf';
    const SOLRCONFIG_FILE_NAME = 'solrconfig.xml';
    const SCHEMA_FILE_NAME = 'schema.xml';
    const DELETE_INDEX = 0;
    const DELETE_DATA = 1;
    const DELETE_CORE = 2;

    private $http;
    
    public function __construct()
    {
        $this->http = include 'vendor/aura/http/scripts/instance.php';
    }

    public function create($coreName)
    {
        if (!SolrCoreAdmin::createCoreDir($coreName)) {
            return false;
        }
        
        return $this->send(SolrCoreAdmin::CORE_HTTP . '?' .
                    'action=CREATE&' .
                    'name=' . $coreName . '&' .
                    'instanceDir=' . $coreName . '&' .
                    'config=' . SolrCoreAdmin::SOLRCONFIG_FILE_NAME . '&' .
                    'schema=' . SolrCoreAdmin::SCHEMA_FILE_NAME . '&' .
                    'dataDir=' . SolrCoreAdmin::DATA_DIR);
    }

    public function getStatus($coreName = null)
    {
        if ($coreName != null) {
           return $this->send(SolrCoreAdmin::CORE_HTTP . '?' .
                                'action=STATUS&' .
                                'core=' . $coreName);
        } else {
           return $this->send(SolrCoreAdmin::CORE_HTTP . '?' .
                                'action=STATUS');
        }
    }

    public function reload($coreName)
    {
        return $this->send(SolrCoreAdmin::CORE_HTTP . '?' .
                                'action=RELOAD&' .
                                'core=' . $coreName);
    }

    public function rename($oldCoreName, $newCoreName)
    {
        if (!$this->isCoreExist($newCoreName)) {
                return $this->send(SolrCoreAdmin::CORE_HTTP . '?' .
                        'action=RENAME&' .
                        'core=' . $oldCoreName .'&' .
                        'other=' . $newCoreName);
        }
        return false;
    }

    public function swap($core1, $core2)
    {
        return $this->send(SolrCoreAdmin::CORE_HTTP . '?' .
                                'action=SWAP&' .
                                'core=' . $oldCoreName .'&' .
                                'other=' . $newCoreName);
    }

    public function unload($coreName)
    {
        return $this->send(SolrCoreAdmin::CORE_HTTP . '?' .
                                'action=UNLOAD&' .
                                'core=' . $coreName);
    }

    public function delete($coreName, $type = SolrCoreAdmin::DELETE_CORE)
    {
        $url = SolrCoreAdmin::CORE_HTTP . '?' .
                'action=UNLOAD&' .
                'core=' . $coreName . '&';
        switch ($type) {
            case SolrCoreAdmin::DELETE_INDEX:
                $url .= 'deleteIndex=true';
                break;
            case SolrCoreAdmin::DELETE_DATA:
                $url .= 'deleteDataDir=true';
                break;
            // Following case does not work properly due to Solr bug
            case SolrCoreAdmin::DELETE_CORE:
                $url .= 'deleteInstanceDir=true';
                break;
        }
        return $this->send($url);
    }

    private function createCoreDir($coreName)
    {
        $dest = SolrCoreAdmin::CORE_PATH . $coreName;
        if (!is_dir($dest) && !$this->isCoreExist($coreName)) {
            $src = SolrCoreAdmin::CORE_PATH . SolrCoreAdmin::CORE_DIR_TEMPLATE;
            $output = shell_exec('cp -r -a ' . $src . ' ' . $dest);
            return true;
        }
        return false;
    }
/*
    private function deleteCoreDir($path) {
        $res = !empty($path) && is_file($path);
        if ($res) {
            return @unlink($path);
        } else {
            return (array_reduce(glob($path.'/*'), function ($r, $i) { return $r && deleteDir($i); }, true)) && @rmdir($path);
        }
    }
   */
    
    private function isCoreExist()
    {
        try {
            $status = $this->getStatus();
            if ($status->isOk()) {
                $cores = $status->getCoreNames();
                $isExist = false;
                foreach ($cores as $c) {
                    if ($c == $newCoreName) {
                        return true;
                    }
                }
                return false;
            } else {
                throw new Exception('Can not obtain Solr cores status');
            }
        } catch (Exception $e) {
            echo 'Caught exception : ' .  $e->getMessage() . '\n';
        }
    }
    
    private function send($url)
    {
        $request = $this->http->newRequest();
        $request->setMethod(Request::METHOD_GET);
        $request->setUrl($url);
        $stack = $this->http->send($request);
        return new SolrCoreResponse($stack[0]->content);
    }
}
