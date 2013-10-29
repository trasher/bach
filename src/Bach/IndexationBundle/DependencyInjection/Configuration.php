<?php
/**
 * Bach IndexationBundle dependency injection configuration
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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Bach IndexationBundle dependency injection configuration
 *
 * This is the class that validates and merges configuration
 * from your app/config files.
 * See {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 *
 * @category Indexation
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
        $rootNode = $treeBuilder->root('bach_indexation');

        $rootNode->children()
            ->arrayNode('types')
            ->isRequired()
            ->requiresAtLeastOneElement()
            ->prototype('scalar')->end()
            ->end()
            ->arrayNode('paths')
            ->isRequired()
            ->requiresAtLeastOneElement()
            ->prototype('scalar')->end()
            ->end();

        return $treeBuilder;
    }
}
