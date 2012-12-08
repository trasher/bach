<?php
namespace Anph\AdministrationBundle\Entity\SolrCore;
class SolrCoreAdmin {
	const CORE_PATH = '/home/philippe/Documents/apache-solr-4.0.0/example/solr/';
	//const CORE_PATH = '/var/solr';
	const CORE_HTTP = 'http://localhost:8983/solr/admin/cores';
	const DATA_DIR = 'data';
	const CONFIG_DIR = 'conf';
	const CONFIG_FILE_NAME = 'solrconfig.xml';
	const SCHEMA_FILE_NAME = 'schema.xml';
	const DELETE_INDEX = 0;
	const DELETE_DATA = 1;
	const DELETE_CORE = 2;
	
	function __construct() {
		
	}
	
	public function create($coreName) {
		$corePath = SolrCoreAdmin::CORE_PATH . $coreName . '/';
		if (!$this->createCoreDir($corePath)) {
			return false;
		}
		$req = new HttpRequest(SolrCoreAdmin::CORE_HTTP, HttpRequest::METH_GET);
		$req->setOptions(array('action' => 'CREATE',
								'name' => $coreName,
								'instanceDir' => $coreName,
								'config' => SolrCoreAdmin::CONFIG_FILE_NAME,
								'schema' => SolrCoreAdmin::SCHEMA_FILE_NAME,
								'dataDir' => SolrCoreAdmin::DATA_DIR));
		try {
			$req->send();
			if ($req->getResponseCode() == 200) {
				return $req->getResponseBody();
			}
			//$this->deleteCoreDir($corePath);
			return false;
		} catch (HttpException $ex) {
			//$this->deleteCoreDir($corePath);
			echo $ex;
		}
	}
	
	public function getStatus($coreName = null) {
		$req = new HttpRequest(SolrCoreAdmin::CORE_HTTP, HttpRequest::METH_GET);
		if ($coreName != null) {
			$req->setOptions(array('action' => 'STATUS', 'core' => $coreName));
		} else {
			$req->setOptions(array('action' => 'STATUS'));
		}
		try {
			$req->send();
			if ($req->getResponseCode() == 200) {
				return $req->getResponseBody();
			}
		} catch (HttpException $ex) {
			echo $ex;
		}
	}
	
	public function reload($coreName) {
		$req = new HttpRequest(SolrCoreAdmin::CORE_HTTP, HttpRequest::METH_GET);
		$req->setOptions(array('action' => 'RELOAD', 'core' => $coreName));
		try {
			$req->send();
			if ($req->getResponseCode() == 200) {
				return $req->getResponseBody();
			}
		} catch (HttpException $ex) {
			echo $ex;
		}
	}
	
	public function rename($oldCoreName, $newCoreName) {
		$req = new HttpRequest(SolrCoreAdmin::CORE_HTTP, HttpRequest::METH_GET);
		$req->setOptions(array('action' => 'RENAME',
								'core' => $oldCoreName,
								'other' => $newCoreName));
		try {
			$req->send();
			if ($req->getResponseCode() == 200) {
				return $req->getResponseBody();
			}
		} catch (HttpException $ex) {
			echo $ex;
		}
	}
	
	public function swap($core1, $core2) {
		$req = new HttpRequest(SolrCoreAdmin::CORE_HTTP, HttpRequest::METH_GET);
		$req->setOptions(array('action' => 'SWAP',
				'core' => $oldCoreName,
				'other' => $newCoreName));
		try {
			$req->send();
			if ($req->getResponseCode() == 200) {
				return $req->getResponseBody();
			}
		} catch (HttpException $ex) {
			echo $ex;
		}
	}
	
	public function unload($coreName) {
		$req = new HttpRequest(SolrCoreAdmin::CORE_HTTP, HttpRequest::METH_GET);
		$req->setOptions(array('action' => 'UNLOAD', 'core' => $coreName));
		try {
			$req->send();
			if ($req->getResponseCode() == 200) {
				return $req->getResponseBody();
			}
		} catch (HttpException $ex) {
			echo $ex;
		}
	}
	
	public function delete($coreName, $type = SolrCoreAdmin::DELETE_CORE) {
		$req = new HttpRequest(SolrCoreAdmin::CORE_HTTP, HttpRequest::METH_GET);
		$req->setOptions(array('action' => 'UNLOAD', 'core' => $coreName));
		switch ($type) {
			case SolrCoreAdmin::DELETE_INDEX:
				$req->addQueryData(array('deleteIndex' => true));
				break;
			case SolrCoreAdmin::DELETE_DATA:
				$req->addQueryData(array('deleteDataDir' => true));
				break;
			// Following case does not work properly (Solr bug)
			case SolrCoreAdmin::DELETE_CORE:
				$req->addQueryData(array('deleteInstanceDir' => true));
				break;
		}
		
		$req->setOptions(array('action' => 'UNLOAD', 'core' => $coreName));
		try {
			$req->send();
			if ($req->getResponseCode() == 200) {
				return $req->getResponseBody();
			}
		} catch (HttpException $ex) {
			echo $ex;
		}
	}
	
	private function createCoreDir($path) {
		if (is_dir($path)) {
			return false;
		}
		if (!mkdir($path)) {
			return false;
		}
		if (!mkdir($path . SolrCoreAdmin::CONFIG_DIR)) {
			$this->deleteCoreDir($path);
			return false;
		}
		if (!mkdir($path . SolrCoreAdmin::DATA_DIR)) {
			$this->deleteCoreDir($path);
			return false;
		}
		if (($f = fopen($path . SolrCoreAdmin::CONFIG_DIR . '/' . SolrCoreAdmin::CONFIG_FILE_NAME, 'a+')) == false) {
			$this->deleteCoreDir($path);
			return false;
		} else {
			fclose($f);
		}
		if (($f = fopen($path . SolrCoreAdmin::CONFIG_DIR . '/' . SolrCoreAdmin::SCHEMA_FILE_NAME, 'a+')) == false) {
			$this->deleteCoreDir($path);
			return false;
		} else {
			fclose($f);
		}
		return true;
	}
	
	private function deleteCoreDir($path) {
		$res = !empty($path) && is_file($path);
		if ($res) {
			return @unlink($path);
		} else {
			return (array_reduce(glob($path.'/*'), function ($r, $i) { return $r && deleteDir($i); }, true)) && @rmdir($path);
		}
	}
}
