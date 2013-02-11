<?php
namespace Anph\AdministrationBundle\Entity\SolrCore;

use DOMDocument;

/**
 * Reads the Solr core config xml file for retreive information like cores' path, solr URL etc.  
 *
 */
class BachCoreAdminConfigReader
{
    const CONFIG_FILE_NAME = "BachSolrCoreConfig.xml";
    
    private $doc;
    
    /**
     * Constructor. Reads the config XML file.
     */
    public function __construct() {
        $this->doc = new DOMDocument();
        $this->doc->load(__DIR__.'/../../Resources/config/' . self::CONFIG_FILE_NAME);
    }
    
    /**
     * Get system path to cores' directory.
     * @return string
     */
    public function getCoresPath()
    {
        return $this->doc->getElementsByTagName('solrCoresPath')->item(0)->nodeValue;
    }
    
    /**
     * Get Solr URL for sending core administration queries.
     * @return string
     */
    public function getCoresURL()
    {
        return $this->doc->getElementsByTagName('solrCoresURL')->item(0)->nodeValue;
    }
    
    /**
     * Get core template. This template is used for create a new core.
     * @return string
     */
    public function getCoreTemplatePath()
    {
        return $this->doc->getElementsByTagName('solrCoreTemplatePath')->item(0)->nodeValue;
    }
    
    /**
     * Get name of core data directory (indexes...).
     * @return string
     */
    public function getCoreDataDir()
    {
        return $this->doc->getElementsByTagName('solrCoreDataDirectoryName')->item(0)->nodeValue;
    }
    
    /**
     * Get name of core config directory (schema.xml, solrconfig.xml...).
     * @return string
     */
    public function getCoreConfigDir()
    {
        return $this->doc->getElementsByTagName('solrCoreConfigDirectoryName')->item(0)->nodeValue;
    }
    
    /**
     * Get name of configuration file (usually solrconfig.xml)
     * @return string
     */
    public function getConfigFileName()
    {
        return $this->doc->getElementsByTagName('solrConfigFileName')->item(0)->nodeValue;
    }
    
    /**
     * Get name of schema file (usually schema.xml)
     * @return string
     */
    public function getSchemaFileName()
    {
        return $this->doc->getElementsByTagName('solrSchemaFileName')->item(0)->nodeValue;
    }
}
