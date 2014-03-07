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

    private $_xml_status = null;

    private $_ssl;
    private $_host;
    private $_port;
    private $_path;
    private $_cache_dir;
    private $_root_dir;

    /**
     * Constructor
     *
     * @param boolean $ssl   Solr SSL host
     * @param string  $host  Solr host
     * @param string  $port  Solr port
     * @param string  $path  Solr path
     * @param string  $cache Application cache directory
     * @param string  $root  Application root directory
     */
    public function __construct( $ssl, $host, $port, $path, $cache, $root )
    {
        $this->_ssl = $ssl;
        $this->_host = $host;
        $this->_port = $port;
        $this->_path = $path;
        $this->_cache_dir = $cache;
        $this->_root_dir = $root;

        $this->_loadStatus();
    }

    /**
     * Loads status from Solr app
     *
     * @return void
     */
    private function _loadStatus()
    {
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

        $this->_xml_status = $xml;
    }

    /**
     * On unserialization
     *
     * @return void
     */
    public function __wakeup()
    {
        $this->_loadStatus();
    }

    /**
     * On serialization
     *
     * @return void
     */
    public function __sleep()
    {
        return array(
            '_ssl',
            '_host',
            '_port',
            '_path'
        );
    }

    /**
     * Get system path to cores' directory.
     *
     * @return string
     */
    public function getCoresPath()
    {
        $xpath = "//lst[@name='status']/lst[1]/@name";
        $result = $this->_xml_status->xpath($xpath);
        $core_name = (string)$result[0];

        $data_dir = $this->getDataDir($core_name);
        $cores_path = str_replace(
            $core_name . '/' . $this->getDefaultDataDir() . '/',
            '',
            $data_dir
        );

        return $cores_path;
    }

    /**
     * Get core temporary path
     *
     * @return string
     */
    public function getTempCorePath()
    {
        return $this->_cache_dir . '/tmpCores/';
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
     * @param string $type Core type
     *
     * @return string
     */
    public function getCoreTemplatePath($type = '')
    {
        $path = $this->_root_dir . '/config/templates/cores/';
        switch ( $type ) {
        case 'MatriculesFileFormat':
            $path .= 'matricules';
            break;
        default:
            $path .= 'archives';
            break;
        }
        return $path;
    }

    /**
     * Get name of core data directory (indexes...).
     *
     * @return string
     */
    public function getDefaultDataDir()
    {
        return 'data';
    }

    /**
     * Get name of core config directory (schema.xml, solrconfig.xml...).
     *
     * @return string
     */
    public function getDefaultConfigDir()
    {
        return 'conf/';
    }

    /**
     * Get default name for configuration file
     *
     * @return string
     */
    public function getDefaultConfigFileName()
    {
        return 'solrconfig.xml';
    }

    /**
     * Get name of schema file (usually schema.xml)
     *
     * @return string
     */
    public function getDefaultSchemaFileName()
    {
        return 'schema.xml';
    }

    /**
     * Get data directory
     *
     * @param string $core Core name
     *
     * @return string
     */
    public function getDataDir($core)
    {
        $xpath = "//lst[@name='" . $core . "']/str[@name='dataDir']";
        $result = $this->_xml_status->xpath($xpath);
        return (string)$result[0];
    }

    /**
     * Get instance directory
     *
     * @param string $core Core name
     *
     * @return string
     */
    public function getInstanceDir($core)
    {
        $xpath = "//lst[@name='" . $core . "']/str[@name='instanceDir']";
        $result = $this->_xml_status->xpath($xpath);
        return (string)$result[0];
    }

    /**
     * Get config dir
     *
     * @param string $core Core name
     *
     * @return string
     */
    public function getConfDir($core)
    {
        $instance_dir = $this->getInstanceDir($core);
        return $instance_dir . 'conf/';
    }

    /**
     * Get schema file path from Solr
     *
     * @param String $coreName Core name
     *
     * @return String
     */
    public function getSchemaPath($coreName)
    {
        $path = $this->getConfDir($coreName);

        $xpath = "//lst[@name='" . $coreName . "']/str[@name='schema']";
        $result = $this->_xml_status->xpath($xpath);

        $path .= $result[0];
        return $path;
    }

    /**
     * Get data configuration file name
     *
     * @return string
     */
    public function getDefaultDataConfigFileName()
    {
        return 'data-config.xml';
    }
}
