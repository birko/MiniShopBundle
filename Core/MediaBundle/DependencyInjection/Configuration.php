<?php

namespace Core\MediaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

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
        $rootNode = $treeBuilder->root('core_media');
        $rootNode = $this->getImageConfig($rootNode);

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        return $treeBuilder;
    }

    private function getImageConfig(ArrayNodeDefinition $rootNode)
    {
        $rootNode->children()
                    ->arrayNode('images')
                        ->useAttributeAsKey('name')
                        ->addDefaultChildrenIfNoneSet("thumb")
                        ->prototype('array')
                            ->children()
                                ->scalarNode('name')->defaultValue("thumb")->end()
                                ->scalarNode('width')->defaultValue(160)->end()
                                ->scalarNode('height')->defaultValue(120)->end()
                                ->scalarNode('method')->defaultValue("resize")->end()
                                ->variableNode('fill')->defaultValue(array(0, 0, 0))->end()
                                ->booleanNode('sepia')->defaultValue(false)->end()
                                ->booleanNode('greyscale')->defaultValue(false)->end()
                                ->booleanNode('enlarge')->defaultValue(false)->end()
                                ->booleanNode('unsharp')->defaultValue(false)->end()
                                ->scalarNode('watermark')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end();

        return $rootNode;
    }
}
