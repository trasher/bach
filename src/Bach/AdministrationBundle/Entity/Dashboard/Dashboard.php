<?php
/**
 * Bach dashboard utilities
 *
 * PHP version 5
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\AdministrationBundle\Entity\Dashboard;

/**
 * Bach dashboard utilities
 *
 * PHP version 5
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class Dashboard
{
    /**
     * Get system total virtual memory
     *
     * @return string
     */
    public function getSystemTotalVirtualMemory()
    {
        $a = exec(
            'awk \'/MemTotal/ {printf( "%.2f\n", $2 / 1024 )}\' /proc/meminfo',
            $status,
            $a
        );
        return $this->formatBytes($a);
    }

    /**
     * Get system free virtual memory
     *
     * @return string
     */
    public function getSystemFreeVirtualMemory()
    {
        $a =exec(
            'awk \'/MemFree/ {printf( "%.2f\n", $2 / 1024 )}\' /proc/meminfo',
            $status,
            $a
        );

        return $this->formatBytes($a);
    }

    /**
     * Get system total swap
     *
     * @return string
     */
    public function getSystemTotalSwapMemory()
    {
        $a= exec(
            'awk \'/SwapTotal/ {printf( "%.2f\n", $2 / 1024 )}\' /proc/meminfo',
            $status,
            $a
        );
        return $this->formatBytes($a);
    }

    /**
     * Get system free swap
     *
     * @return string
     */
    public function getSystemFreeSwapMemory()
    {
        $a= exec(
            'awk \'/SwapFree/ {printf( "%.2f\n", $2 / 1024 )}\' /proc/meminfo',
            $status,
            $a
        );
        return $this->formatBytes($a);
    }

    /**
     * Format Kb
     *
     * @param int $bytes     Bytes amount
     * @param int $precision Precision
     *
     * @return float
     */
    function formatBytes($bytes, $precision = 0)
    {
        $kilobyte = 1024;
        $megabyte = $kilobyte * 1024;
        $gigabyte = $megabyte * 1024;
        $terabyte = $gigabyte * 1024;

        if (($bytes >= 0) && ($bytes < $kilobyte)) {
            return $bytes . ' B';
        } elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
            return round($bytes / $kilobyte, $precision) . ' KB';
        } elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
            return round($bytes / $megabyte, $precision) . ' MB';
        } elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
            return round($bytes / $gigabyte, $precision) . ' GB';
        } elseif ($bytes >= $terabyte) {
            return round($bytes / $terabyte, $precision) . ' TB';
        } else {
            return $bytes . ' B';
        }
    }
}
