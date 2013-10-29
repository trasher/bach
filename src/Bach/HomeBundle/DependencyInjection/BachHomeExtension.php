<?php
/**
 * Bach HomeBundle dependency injection extension
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\HomeBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Bach HomeBundle dependency injection extension
 *
 * This is the class that loads and manage bundle configuration
 * See {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class BachHomeExtension extends Extension
{

    /**
     * Load configuration
     *
     * @param array            $configs   Configuration values
     * @param ContainerBuilder $container Container
     *
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yml');

        foreach ( $configs[0]['files'] as $name=>$path ) {
            if ( !defined('BACH_FILES_' . strtoupper($name)) ) {
                define('BACH_FILES_' . strtoupper($name), realpath($path));
            }
        }
    }
}
