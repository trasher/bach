<?php

/*
 * This file is part of the bach project.
 */

namespace Bach\IndexationBundle\Generator;

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

    public function generate($namespace, $format, $datatype)
    {
        $dir = __DIR__.'/../Entity/Driver';

        if (!file_exists($dir.'/'.strtoupper($format))) {
            mkdir($dir.'/'.strtoupper($format), 0777,true);
        }

        $dir = $dir.'/'.strtoupper($format);

        $parameters = array(
            'namespace'             => $namespace,
            'format'                => $format,
            'format_uppercase'        => strtoupper($format)
        );

        if (!file_exists($dir.'/Driver.php')) {
            $this->renderFile($this->skeletonDir, 'Driver.php', $dir.'/Driver.php', $parameters);
        }

        $parserDir = $dir.'/Parser';

        $parameters = array(
            'namespace'             => $namespace.'\Parser\\'.strtoupper($datatype)
        );

        if (!file_exists($parserDir.'/'.strtoupper($datatype))) {
            mkdir($parserDir.'/'.strtoupper($datatype), 0777,true);
            $this->renderFile($this->skeletonDir, 'Parser.php', $parserDir.'/'.strtoupper($datatype).'/Parser.php', $parameters);
        }
    }
}
