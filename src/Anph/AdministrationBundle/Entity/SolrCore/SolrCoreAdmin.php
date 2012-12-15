<?php
namespace Anph\AdministrationBundle\Entity\SolrCore;
use Aura\Http\Message\Response\Stack, Aura\Http\Message\Request;

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
        if (!$this->createCoreDir($coreName)) {
            return false;
        }
        $response = $this->sendGetRequest(SolrCoreAdmin::CORE_HTTP . '?' .
                        'action=CREATE&' .
                        'name=' . $coreName . '&' .
                        'instanceDir=' . $coreName . '&' .
                        'config=' . SolrCoreAdmin::SOLRCONFIG_FILE_NAME . '&' .
                        'schema=' . SolrCoreAdmin::SCHEMA_FILE_NAME . '&' .
                        'dataDir=' . SolrCoreAdmin::DATA_DIR);
        $responseObj = new SolrCoreResponse($response);
        return $responseObj->getStatus() == 0 ? true : $responseObj;
    }

    public function getStatus($coreName = null)
    {
        if ($coreName != null) {
            $response = $this->sendGetRequest(SolrCoreAdmin::CORE_HTTP . '?' .
                                        'action=STATUS&' .
                                        'core=' . $coreName);
        } else {
           $response = $this->sendGetRequest(SolrCoreAdmin::CORE_HTTP . '?' .
                                       'action=STATUS&');
        }
        $responseObj = new SolrCoreResponse($response);
        return $responseObj->getStatus() == 0 ? true : $responseObj;
    }

    public function reload($coreName)
    {
        $response = $this->sendGetRequest(SolrCoreAdmin::CORE_HTTP . '?' .
                                    'action=RELOAD&' .
                                    'core' . $coreName);
        $responseObj = new SolrCoreResponse($response);
        return $responseObj->getStatus() == 0 ? true : $responseObj;
    }

    public function rename($oldCoreName, $newCoreName)
    {
        $response = $this->sendGetRequest(SolrCoreAdmin::CORE_HTTP . '?' .
                                    'action=RENAME&' .
                                    'core=' . $oldCoreName .'&' .
                                    'other=' . $newCoreName);
        $responseObj = new SolrCoreResponse($response);
        return $responseObj->getStatus() == 0 ? true : $responseObj;
    }

    public function swap($core1, $core2)
    {
        $response = $this->sendGetRequest(SolrCoreAdmin::CORE_HTTP . '?' .
                                    'action=SWAP&' .
                                    'core=' . $oldCoreName .'&' .
                                    'other=' . $newCoreName);
        $responseObj = new SolrCoreResponse($response);
        return $responseObj->getStatus() == 0 ? true : $responseObj;
    }

    public function unload($coreName)
    {
        $response = $this->sendGetRequest(SolrCoreAdmin::CORE_HTTP . '?' .
                                    'action=UNLOAD&' .
                                    'core=' . $coreName);
        $responseObj = new SolrCoreResponse($response);
        return $responseObj->getStatus() == 0 ? true : $responseObj;
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
            // Following case does not work properly (Solr bug)
            case SolrCoreAdmin::DELETE_CORE:
                $url .= 'deleteInstanceDir=true';
                break;
        }
        $response = $this->sendGetRequest($url);
        $responseObj = new SolrCoreResponse($response);
        return $responseObj->getStatus() == 0 ? true : $responseObj;
    }

    private function createCoreDir($coreName)
    {
        $dest = SolrCoreAdmin::CORE_PATH . $coreName;
        if (!is_dir($dest)) {
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
    private function sendGetRequest($url)
    {
        $request = $this->http->newRequest();
        $request->setMethod(Request::METHOD_GET);
        $request->setUrl($url);
        $response = $this->http->send($request);
        return $response[0]->content;
    }
}
