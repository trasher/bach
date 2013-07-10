<?php
/**
 * Bach core configuration reader
 *
 * PHP version 5
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Anph\AdministrationBundle\Entity\SolrCore;

use DOMDocument;

/**
 * Bach core configuration reader
 *
 * Reads the Solr core config xml file to retreive information
 * like core path, URL and so on.
 *
 * PHP version 5
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class BachCoreAdminConfigReader
{
    const CONFIG_FILE_NAME = "BachSolrCoreConfig.xml";

    private $_doc;

    /**
     * Constructor. Reads the config XML file.
     */
    public function __construct()
    {
        $filepath = __DIR__.'/../../Resources/config/' . self::CONFIG_FILE_NAME;
        libxml_use_internal_errors(true);
        $this->_doc = simplexml_load_file($filepath);

        if ( $this->_doc === false ) {
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
     *
     * @return string
     */
    public function getCoresPath()
    {
        return $this->_doc->solrCoresPath;
    }

    /**
     * Get core temporary path
     *
     * @return string
     */
    public function getTempCorePath()
    {
        //FIXME: parametize
        return '/var/www/bach/app/cache/tmpCores/';
    }

    /**
     * Get Solr URL for sending core administration queries.
     *
     * @return string
     */
    public function getCoresURL()
    {
        return $this->_doc->solrCoresURL;
    }

    /**
     * Get core template. This template is used for create a new core.
     *
     * @return string
     */
    public function getCoreTemplatePath()
    {
        return (string)$this->_doc->solrCoreTemplatePath;
    }

    /**
     * Get name of core data directory (indexes...).
     *
     * @return string
     */
    public function getCoreDataDir()
    {
        return $this->_doc->solrCoreDataDirectoryName;
    }

    /**
     * Get name of core config directory (schema.xml, solrconfig.xml...).
     *
     * @return string
     */
    public function getCoreConfigDir()
    {
        return $this->_doc->solrCoreConfigDirectoryName;
    }

    /**
     * Get name of configuration file (usually solrconfig.xml)
     *
     * @return string
     */
    public function getConfigFileName()
    {
        return $this->_doc->solrConfigFileName;
    }

    /**
     * Get name of schema file (usually schema.xml)
     *
     * @return string
     *
     * @deprecated See getSolrSchemaFileName
     */
    public function getSchemaFileName()
    {
        return $this->_doc->solrSchemaFileName;
    }

    /**
     * Get schema file path from Solr
     *
     * @param String $coreName Core name
     *
     * @return String
     */
    public function getSolrSchemaFileName($coreName)
    {
        //http://trojan:8080/solr/admin/cores?action=STATUS
        $url = $this->getCoresURL() . '/admin/cores?action=STATUS';
        $context  = stream_context_create(
            array(
                'http' => array(
                    'header' => 'Accept: application/xml'
                )
            )
        );
        $xml = file_get_contents($url, false, $context);
        $xml = simplexml_load_string($xml);

        $path = null;

        $xpath = "//lst[@name='" . $coreName . "']/str[@name='instanceDir']";
        $result = $xml->xpath($xpath);

        $path = $result[0] . 'conf/';

        $xpath = "//lst[@name='" . $coreName . "']/str[@name='schema']";
        $result = $xml->xpath($xpath);

        $path .= $result[0];
        return $path;
    }

    /**
     * Get data configuration file name
     *
     * @return string
     */
    public function getDataConfigFileName()
    {
        return $this->_doc->solrDataConfigFileName;
    }

    /**
     * Get database parameters
     *
     * @return string
     */
    public function getDatabaseParameters()
    {
        $data = array();
        $elts = (array)$this->_doc->databaseConfig;
        foreach ( $elts as $k=>$v  ) {
            $data[$k] = $v;
        }
        return $data;
    }
}
