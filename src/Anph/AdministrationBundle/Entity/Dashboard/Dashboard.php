<?php
namespace Anph\AdministrationBundle\Entity\Dashboard;

use DOMDocument;

/**
 * Reads the Solr core config xml file for retreive information like cores' path, solr URL etc.  
 *
 */
class Dashboard
{
    

    
    /**
     * Constructor. Reads the config XML file.
     */
    
    public function __construct() {
        
    }
    
    /**
     * Get system path to cores' directory.
     * @return string
     */
    public function getSystemTotalVirtualMemory()
    {
        $a = exec('awk \'/MemTotal/ {printf( "%.2f\n", $2 / 1024 )}\' /proc/meminfo',$status,$a);
    	return $a;
    
    }
    
    public function getSystemFreeVirtualMemory()
    {
    	$a =exec('awk \'/MemFree/ {printf( "%.2f\n", $2 / 1024 )}\' /proc/meminfo',$status,$a);
        return $a;
    }
    
    
    public function getSystemTotalSwapMemory()
    {
    	
    	 $a= exec('awk \'/SwapTotal/ {printf( "%.2f\n", $2 / 1024 )}\' /proc/meminfo',$status,$a);
        return $a;
    }
    
    public function getSystemFreeSwapMemory()
    {
       $a= exec('awk \'/SwapFree/ {printf( "%.2f\n", $2 / 1024 )}\' /proc/meminfo',$status,$a);
        return $a;
    }
    
    
    
    
    
 
}
