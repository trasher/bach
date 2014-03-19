<?php
/**
 * Bach IndexationBundle dependency injection extension
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
namespace Bach\IndexationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Bach IndexationBundle dependency injection extension
 *
 * This is the class that loads and manage bundle configuration
 * See {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class BachIndexationExtension extends Extension
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

        $types = $config['types'];
        $types_paths = $config['paths'];

        if ( $container->getParameter('feature.matricules') === false ) {
            unset($types[array_search('matricules', $types)]);
            unset($types_paths['matricules']);
        }

        $container->setParameter('bach.types', $types);
        $container->setParameter('bach.typespaths', $types_paths);

        $loader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yml');
    }
}
