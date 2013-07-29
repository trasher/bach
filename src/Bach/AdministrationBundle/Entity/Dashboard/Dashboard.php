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

use DOMDocument;

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
        return $a;
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
        return $a;
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
        return $a;
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
        return $a;
    }
}
