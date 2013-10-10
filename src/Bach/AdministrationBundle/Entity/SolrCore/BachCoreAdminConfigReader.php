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

namespace Bach\AdministrationBundle\Entity\SolrCore;

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
    private $_filepath;

    private $_ssl;
    private $_host;
    private $_port;
    private $_path;

    /**
     * Constructor. Reads the config XML file.
     *
     * @param boolean $ssl  Solr SSL host
     * @param string  $host Solr host
     * @param string  $port Solr port
     * @param string  $path Solr path
     */
    public function __construct( $ssl, $host, $port, $path )
    {
        $this->_ssl = $ssl;
        $this->_host = $host;
        $this->_port = $port;
        $this->_path = $path;

        $this->_filepath = __DIR__.'/../../Resources/config/' .
            self::CONFIG_FILE_NAME;
        libxml_use_internal_errors(true);
        $this->_loadDoc();

        if ( $this->_doc === false ) {
            $msg = __METHOD__ . ' | An error occured loading XML file ' .
                $this->_filepath . ":\n";
            foreach ( libxml_get_errors() as $error ) {
                $msg .= "\t" . $error->message . "\n";
            }
            throw new \RuntimeException($msg);
        }
    }

    /**
     * Loads XML document
     *
     * @return void
     */
    private function _loadDoc()
    {
        $this->_doc = simplexml_load_file($this->_filepath);
    }

    /**
     * On unserialization
     *
     * @return void
     */
    public function __wakeup()
    {
        $this->_loadDoc();
    }

    /**
     * On serialization
     *
     * @return void
     */
    public function __sleep()
    {
        return array(
            '_host',
            '_port',
            '_path',
            '_filepath'
        );
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
        $url_pattern = 'http%ssl://%host:%port%path';
        $url = str_replace(
            array(
                '%ssl',
                '%host',
                '%port',
                '%path'
            ),
            array(
                ($this->_ssl == true) ? 's' : '',
                $this->_host,
                $this->_port,
                $this->_path
            ),
            $url_pattern
        );
        return $url;
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
