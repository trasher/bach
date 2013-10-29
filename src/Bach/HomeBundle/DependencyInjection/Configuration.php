<?php
/**
 * Bach HomeBundle dependency injection configuration
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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Bach HomeBundle dependency injection configuration
 *
 * This is the class that validates and merges configuration
 * from your app/config files.
 * See {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class Configuration implements ConfigurationInterface
{

    /**
     * Get treeBuilder
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('bach_home');

        $default_base_path = realpath(__DIR__ . '/../../../../web/');

        //TODO: validate paths are ending with a '/' to normalize values
        $rootNode->children()
            ->arrayNode('files')
            ->children()
            ->scalarNode('videos')
            ->defaultValue($default_base_path . 'videos/')
            ->end()
            ->scalarNode('musics')
            ->defaultValue($default_base_path . 'musics/')
            ->end()
            ->scalarNode('misc')
            ->defaultValue($default_base_path . 'misc/')
            ->end();

        return $treeBuilder;
    }
}
