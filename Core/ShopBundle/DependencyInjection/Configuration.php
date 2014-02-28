<?php

namespace Core\ShopBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('core_shop')
            ->children()
                ->arrayNode('address')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('required')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode("company")->defaultValue(false)->end()
                                ->booleanNode("email")->defaultValue(true)->end()
                                ->booleanNode("phone")->defaultValue(false)->end()
                                ->booleanNode("TIN")->defaultValue(false)->end()
                                ->booleanNode("OIN")->defaultValue(false)->end()
                                ->booleanNode("VATIN")->defaultValue(false)->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('order')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('process')
                             ->addDefaultsIfNotSet()
                             ->children()
                                ->booleanNode("labels")->defaultValue(true)->end()
                                ->booleanNode("shipping")->defaultValue(true)->end()
                                ->booleanNode("status")->defaultValue(true)->end()
                                ->booleanNode("export")->defaultValue(false)->end()
                             ->end()
                        ->end()
                        ->arrayNode('export')
                            ->prototype('array')
                            ->children()
                                ->variableNode("name")->end()
                                ->variableNode("type")->defaultValue("csv")->end()
                                ->variableNode("filter")->defaultValue("all")->end()
                                ->booleanNode("header")->defaultValue(false)->end()
                                ->arrayNode("fields")
                                ->prototype('array')
                                ->children()
                                    ->variableNode('title')->end()
                                    ->variableNode('alias')->defaultValue(null)->end()
                                    ->variableNode('value')->end()
                                    ->arrayNode('fields')
                                    ->prototype('array')
                                    ->children()
                                        ->variableNode('title')->end()
                                        ->variableNode('alias')->defaultValue(null)->end()
                                        ->variableNode('value')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                ->end()
            ->end();

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        return $treeBuilder;
    }
}
