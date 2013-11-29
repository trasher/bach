<?php
/**
 * Bach file driver generator
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\IndexationBundle\Generator;

use Symfony\Component\Filesystem\Filesystem;
use Sensio\Bundle\GeneratorBundle\Generator\Generator;

/**
 * Bach file driver generator
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class FileDriverGenerator extends Generator
{
    private $_filesystem;
    private $_skeletonDir;

    /**
     * Constructor
     *
     * @param Filesystem $filesystem  ?
     * @param string     $skeletonDir Skeleton storage directory
     */
    public function __construct(Filesystem $filesystem, $skeletonDir)
    {
        $this->_filesystem = $filesystem;
        $this->_skeletonDir = $skeletonDir;
    }

    /**
     * Generate driver mapper
     *
     * @param string $namespace Namespace
     * @param string $format    Format
     * @param string $datatype  Data type
     *
     * @return void
     */
    public function generate($namespace, $format, $datatype)
    {
        $dir = __DIR__.'/../Entity/Driver';

        if (!file_exists($dir.'/'.strtoupper($format))) {
            mkdir($dir.'/'.strtoupper($format), 0755, true);
        }

        $dir = $dir.'/'.strtoupper($format);

        $parameters = array(
            'namespace'         => $namespace,
            'format'            => $format,
            'format_uppercase'  => strtoupper($format)
        );

        if (!file_exists($dir.'/Driver.php')) {
            $this->renderFile(
                $this->_skeletonDir,
                'Driver.php',
                $dir.'/Driver.php',
                $parameters
            );
        }

        $parserDir = $dir.'/Parser';

        $parameters = array(
            'namespace' => $namespace.'\Parser\\'.strtoupper($datatype)
        );

        if (!file_exists($parserDir.'/'.strtoupper($datatype))) {
            mkdir($parserDir . '/' . strtoupper($datatype), 0755, true);
            $this->renderFile(
                $this->_skeletonDir,
                'Parser.php',
                $parserDir.'/'.strtoupper($datatype).'/Parser.php',
                $parameters
            );
        }
    }
}
