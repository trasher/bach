<?php
namespace Anph\AdministrationBundle\Entity\Helpers\ViewObjects;

use Anph\AdministrationBundle\Entity\SolrCore\SolrCoreAdmin;

class CoreStatus
{
    public $startTime;
    public $coreDir;
    public $dataDir;
    public $uptime;
    public $numDocs;
    public $maxDoc;
    public $size;
    
    public function __construct($coreName)
    {
        $sca = new SolrCoreAdmin();
        $status = $sca->getStatus($coreName);
        $status = $status->getCoreStatus($coreName);
        $this->startTime = $status->getStartTime()->format('d/m/Y H:i');
        $this->coreDir = $status->getInstanceDir();
        $this->dataDir = $status->getDataDir();
        $this->uptime = $status->getUptime();
        $this->numDocs = $status->getNumDocs();
        $this->maxDoc = $status->getMaxDoc();
        $this->size = $status->getSizeInBytes(); 
    }
}
