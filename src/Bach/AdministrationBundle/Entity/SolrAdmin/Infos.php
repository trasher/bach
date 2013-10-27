<?php
/**
 * Bach solr admin informations
 *
 * PHP version 5
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\AdministrationBundle\Entity\SolrAdmin;

/**
 * Bach solr admin informations
 *
 * PHP version 5
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class Infos
{
    //available hanlders: system, logging, threads, properties
    private $_url = '%solr/admin/info/system?wt=phps';

    private $_ssl;
    private $_host;
    private $_port;
    private $_path;

    private $_infos;

    /**
     * Constructor
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

        $url_pattern = 'http%ssl://%host:%port%path';
        $solr_url = str_replace(
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

        $this->_url = str_replace(
            '%solr',
            $solr_url,
            $this->_url
        );
    }

    /**
     * Load informations
     *
     * @return void
     */
    public function loadSystemInfos()
    {
        $this->_infos = unserialize(file_get_contents($this->_url));
    }

    /**
     * Get Solr version
     *
     * @return string
     */
    public function getSolrVersion()
    {
        return $this->_infos['lucene']['solr-spec-version'];
    }

    /**
     * Get JVM name and version
     *
     * @return string
     */
    public function getJvmInfos()
    {
        return $this->_infos['jvm']['name'] . ' ' . $this->_infos['jvm']['version'];
    }

    /**
     * Get system informations
     *
     * @return string
     */
    public function getSystemInfos()
    {
        return $this->_infos['system']['name'] . ' ' .
            $this->_infos['system']['version'];
    }

    /**
     * Get load average
     *
     * @return double
     */
    public function getLoadAverage()
    {
        return $this->_infos['system']['systemLoadAverage'];
    }

    /**
     * Get total virtual memory
     *
     * @return int
     */
    public function getTotalVirtMem()
    {
        return $this->formatBytes(
            $this->_infos['system']['totalPhysicalMemorySize']
        );
    }

    /**
     * Get free virtual memory
     *
     * @return int
     */
    public function getFreeVirtMem()
    {
        return $this->formatBytes(
            $this->_infos['system']['freePhysicalMemorySize']
        );
    }

    /**
     * Get used virtual memory
     *
     * @return int
     */
    public function getUsedVirtMem()
    {
        return $this->formatBytes(
            $this->_infos['system']['totalPhysicalMemorySize'] -
            $this->_infos['system']['freePhysicalMemorySize']
        );
    }

    /**
     * Get total swap amount
     *
     * @return int
     */
    public function getTotalSwap()
    {
        return $this->formatBytes(
            $this->_infos['system']['totalSwapSpaceSize']
        );
    }

    /**
     * Get free swap size
     *
     * @return int
     */
    public function getFreeSwap()
    {
        return $this->formatBytes(
            $this->_infos['system']['freeSwapSpaceSize']
        );
    }

    /**
     * Get used swap size
     *
     * @return int
     */
    public function getUsedSwap()
    {
        return $this->formatBytes(
            $this->_infos['system']['totalSwapSpaceSize'] -
            $this->_infos['system']['freeSwapSpaceSize']
        );
    }

    /**
     * Get total JVM memory
     *
     * @return int
     */
    public function getTotalJvmMem()
    {
        return $this->formatBytes(
            $this->_infos['jvm']['memory']['raw']['total'],
            0,
            'mega'
        );
    }

    /**
     * Get free JVM memory
     *
     * @return int
     */
    public function getFreeJvmMem()
    {
        return $this->formatBytes(
            $this->_infos['jvm']['memory']['raw']['free'],
            0,
            'mega'
        );
    }

    /**
     * Get used JVM memory
     *
     * @return int
     */
    public function getUsedJvmMem()
    {
        return $this->formatBytes(
            $this->_infos['jvm']['memory']['raw']['used'],
            0,
            'mega'
        );
    }

    /**
     * Format Kb
     *
     * @param int     $bytes     Bytes amount
     * @param int     $precision Precision
     * @param sting   $unit      Unit type to return
     * @param boolean $units     With units or not
     *
     * @return float
     */
    function formatBytes($bytes, $precision = 0, $unit = 'giga', $units = false)
    {
        $kilobyte = 1024;
        $megabyte = $kilobyte * 1024;
        $gigabyte = $megabyte * 1024;
        $terabyte = $gigabyte * 1024;

        $result = $bytes;
        $txt_unit = 'B';
        switch ( $unit ) {
        case 'kilo':
            $result = round($bytes / $kilobyte, $precision);
            $txt_unit = 'KB';
            break;
        case 'mega':
            $result =  round($bytes / $megabyte, $precision);
            $txt_unit = 'MB';
            break;
        case 'giga':
            $result = round($bytes / $gigabyte, $precision);
            $txt_unit = 'GB';
            break;
        case 'tera':
            $result = round($bytes / $terabyte, $precision);
            $txt_unit = 'TB';
            break;
        }

        if ( $units === true ) {
            return $result . ' ' . $txt_unit;
        } else {
            return $result;
        }
    }
}
