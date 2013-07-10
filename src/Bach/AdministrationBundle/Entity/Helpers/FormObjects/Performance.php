<?php
namespace Bach\AdministrationBundle\Entity\Helpers\FormObjects;

use Bach\AdministrationBundle\Entity\SolrPerformance\SolrPerformance;

class Performance
{
    public $queryResultWindowsSize;
    public $documentCacheClass;
    public $documentCacheSize;
    public $documentCacheInitialSize;
    public $queryResultMaxDocsCached;
    public $queryResultClassSize;
    public $queryResultCacheSize;
    public $queryResultInitialCacheSize;
    public $queryResultAutowarmCount;
    
    public function __construct($coreName = null)
    {
        if ($coreName != null) {
            $sp = new SolrPerformance($coreName);
            $this->queryResultWindowsSize = $sp->getQueryResultWindowsSize();
            $parameters = $sp->getDocumentCacheParameters();
            $this->documentCacheClass = $parameters[0];
            $this->documentCacheSize = $parameters[1];
            $this->documentCacheInitialSize = $parameters[2];
            $this->queryResultMaxDocsCached = $sp->getQueryResultMaxDocsCached();
            $parameters = $sp->getQueryResultCacheParameters();
            $this->queryResultClassSize = $parameters[0];
            $this->queryResultCacheSize = $parameters[1];
            $this->queryResultInitialCacheSize = $parameters[2];
            $this->queryResultAutowarmCount = $parameters[3];
        }
    }
    
    public function saveAll($coreName)
    {
        $sp = new SolrPerformance($coreName);
        $sp->setDocumentCacheParameters(
                $this->documentCacheClass,
                $this->documentCacheSize,
                $this->documentCacheInitialSize
                );
        $sp->setQueryResultMaxDocsCached($this->queryResultMaxDocsCached);
        $sp->setQueryResultCacheParameters(
                $this->queryResultClassSize,
                $this->queryResultCacheSize,
                $this->queryResultInitialCacheSize,
                $this->queryResultAutowarmCount
                );
        $sp->save();
    }
}
