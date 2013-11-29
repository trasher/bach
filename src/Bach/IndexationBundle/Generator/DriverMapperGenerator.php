<?php
/**
 * Bach driver mapper generator
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
 * Bach driver mapper generator
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class DriverMapperGenerator extends Generator
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
     * @param string $mapper    Mapper name
     *
     * @return void
     */
    public function generate($namespace, $mapper)
    {
        $dir = __DIR__.'/../Entity/Mapper';

        $parameters = array(
            'namespace' => $namespace,
            'mapper'    => $mapper
        );

        $this->renderFile(
            $this->_skeletonDir,
            'DriverMapper.php',
            $dir.'/'.$mapper.'.php',
            $parameters
        );
    }
}
