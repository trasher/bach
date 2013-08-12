<?php

namespace Bach\HomeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
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
