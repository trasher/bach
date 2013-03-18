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
        $filepath = __DIR__.'/../../Resources/config/' . self::CONFIG_FILE_NAME;
        libxml_use_internal_errors(true);
        $this->doc = simplexml_load_file($filepath);

        if ( $this->doc === false ) {
            $msg = __METHOD__ . ' | An error occured loading XML file ' .
                $filepath . ":\n";
            foreach ( libxml_get_errors() as $error ) {
                $msg .= "\t" . $error->message . "\n";
            }
            throw new \RuntimeException($msg);
        }
    }

    /**
     * Get system path to cores' directory.
     * @return string
     */
    public function getCoresPath()
    {
        return $this->doc->solrCoresPath;
    }

    /**
     * Get Solr URL for sending core administration queries.
     * @return string
     */
    public function getCoresURL()
    {
        return $this->doc->solrCoresURL;
    }

    /**
     * Get core template. This template is used for create a new core.
     * @return string
     */
    public function getCoreTemplatePath()
    {
        return $this->doc->solrCoreTemplatePath;
    }

    /**
     * Get name of core data directory (indexes...).
     * @return string
     */
    public function getCoreDataDir()
    {
        return $this->odc->solrCoreDataDirectoryName;
    }

    /**
     * Get name of core config directory (schema.xml, solrconfig.xml...).
     * @return string
     */
    public function getCoreConfigDir()
    {
        return $this->doc->solrCoreConfigDirectoryName;
    }

    /**
     * Get name of configuration file (usually solrconfig.xml)
     * @return string
     */
    public function getConfigFileName()
    {
        return $this->doc->solrConfigFileName;
    }

    /**
     * Get name of schema file (usually schema.xml)
     * @return string
     */
    public function getSchemaFileName()
    {
        return $this->doc->solrSchemaFileName;
    }

    public function getDataConfigFileName()
    {
        return $this->doc->solrDataConfigFileName;
    }

    public function getDatabaseParameters()
    {
        $data = array();
        foreach ( $this->doc->databaseConfig as $e  ) {
            $data[$e->getName()] = (string)$e;
        }
        return $data;
    }
}
