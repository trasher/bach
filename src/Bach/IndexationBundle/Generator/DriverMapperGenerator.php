<?php

/*
 * This file is part of the bach project.
 */

namespace Bach\IndexationBundle\Generator;

use Symfony\Component\Filesystem\Filesystem;
use Sensio\Bundle\GeneratorBundle\Generator\Generator;

/**
 * Generates a MapperDriver.
 *
 * @author Anaphore PI Team
 */
class DriverMapperGenerator extends Generator
{
    private $filesystem;
    private $skeletonDir;

    public function __construct(Filesystem $filesystem, $skeletonDir)
    {
        $this->filesystem = $filesystem;
        $this->skeletonDir = $skeletonDir;
    }

    public function generate($namespace, $mapper)
    {
        $dir = __DIR__.'/../Entity/Mapper';

        $parameters = array(
            'namespace' => $namespace,
            'mapper'    => $mapper
        );

        $this->renderFile($this->skeletonDir, 'DriverMapper.php', $dir.'/'.$mapper.'.php', $parameters);
    }
}
