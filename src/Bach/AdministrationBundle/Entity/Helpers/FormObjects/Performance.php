<?php
/**
 * Performance form object
 *
 * PHP version 5
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\AdministrationBundle\Entity\Helpers\FormObjects;

use Bach\AdministrationBundle\Entity\SolrPerformance\SolrPerformance;

/**
 * Performance form object
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
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

    private $_xmlp;

    /**
     * Constructor
     *
     * @param XMLProcess $xmlp     XMLProcess instance
     * @param string     $coreName Core name
     */
    public function __construct($xmlp, $coreName = null)
    {
        $this->_xmlp = $xmlp;
        if ($coreName != null) {
            $sp = new SolrPerformance($xmlp, $coreName);
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

    /**
     * Save
     *
     * @param string $coreName Core name
     *
     * @return void
     */
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
