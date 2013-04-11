<?php

namespace MajoredIn\JobSearchBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class MajoredInJobSearchExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('advanced_search_defaults.yml');
        $loader->load('autocomplete.yml');
        $loader->load('cache.yml');
        $loader->load('canonicalizer.yml');
        $loader->load('feed_reader.yml');
        $loader->load('job_api_connector.yml');
        $loader->load('job_query_factory.yml');
        $loader->load('location_manager.yml');
        $loader->load('major_alias_manager.yml');
        $loader->load('major_manager.yml');
        $loader->load('twig.yml');
    }
}
