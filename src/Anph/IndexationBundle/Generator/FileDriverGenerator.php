<?php

/*
 * This file is part of the bach project.
 */

namespace Anph\IndexationBundle\Generator;

use Symfony\Component\Filesystem\Filesystem;
use Sensio\Bundle\GeneratorBundle\Generator\Generator;

/**
 * Generates a FileDriver.
 *
 * @author Anaphore PI Team
 */
class FileDriverGenerator extends Generator
{
    private $filesystem;
    private $skeletonDir;

    public function __construct(Filesystem $filesystem, $skeletonDir)
    {
        $this->filesystem = $filesystem;
        $this->skeletonDir = $skeletonDir;
    }

    public function generate($namespace, $driver, $format)
    {
    	$dir = __DIR__.'/../Entity/Driver';
        
        $parameters = array(
            'namespace' => $namespace,
            'driver'    => $driver,
            'format'    => $format
        );

        $this->renderFile($this->skeletonDir, 'FileDriver.php', $dir.'/'.$driver.'.php', $parameters);
    }
}
