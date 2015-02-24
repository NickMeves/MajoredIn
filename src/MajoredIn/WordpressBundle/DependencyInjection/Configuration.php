<?php

namespace MajoredIn\WordpressBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('majored_in_wordpress');

        $rootNode
            ->children()
                ->scalarNode('install_path')->isRequired()->end()
                ->arrayNode('database')
                    ->children()
                        ->arrayNode('master')
                            ->children()
                                ->scalarNode('host')->isRequired()->end()
                                ->scalarNode('port')->isRequired()->end()
                                ->scalarNode('dbname')->isRequired()->end()
                                ->scalarNode('user')->isRequired()->end()
                                ->scalarNode('password')->isRequired()->end()
                                ->scalarNode('charset')->isRequired()->end()
                            ->end()
                        ->end()
                        ->arrayNode('slave')
                            ->children()
                                ->scalarNode('host')->isRequired()->end()
                                ->scalarNode('port')->isRequired()->end()
                                ->scalarNode('dbname')->isRequired()->end()
                                ->scalarNode('user')->isRequired()->end()
                                ->scalarNode('password')->isRequired()->end()
                                ->scalarNode('charset')->isRequired()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('auth_key')->isRequired()->end()
                ->scalarNode('secure_auth_key')->isRequired()->end()
                ->scalarNode('logged_in_key')->isRequired()->end()
                ->scalarNode('nonce_key')->isRequired()->end()
                ->scalarNode('auth_salt')->isRequired()->end()
                ->scalarNode('secure_auth_salt')->isRequired()->end()
                ->scalarNode('logged_in_salt')->isRequired()->end()
                ->scalarNode('nonce_salt')->isRequired()->end()
                ->scalarNode('table_prefix')->isRequired()->end()
                ->scalarNode('force_ssl_admin')->isRequired()->end()
            ->end();

        return $treeBuilder;
    }
}
