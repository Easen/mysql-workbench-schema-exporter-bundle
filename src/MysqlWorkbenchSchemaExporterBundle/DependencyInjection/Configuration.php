<?php

namespace MysqlWorkbenchSchemaExporterBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('mysql_workbench_schema_exporter');
        $rootNode->children()
            ->arrayNode('schema')
                ->requiresAtLeastOneElement()
                ->useAttributeAsKey('name')
                ->prototype('array')
                ->children()
                    ->scalarNode('bundle')
                        ->isRequired()
                    ->end()
                    ->scalarNode('formatter')
                        ->defaultValue('doctrine2-annotation')
                    ->end()
                    ->arrayNode('params')
                    ->end()
                    ->scalarNode('output')
                        ->defaultValue('Entity/')
                    ->end()
                    ->scalarNode('file')
                        ->defaultValue('Resources/workbench/%s.mwb')
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
