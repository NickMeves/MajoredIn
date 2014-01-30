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
        $loader->load('Controller/advanced_search_defaults.yml');
        $loader->load('Controller/autocomplete.yml');
        $loader->load('EventListener/exclude_url_listener.yml');
        $loader->load('Model/location_manager.yml');
        $loader->load('Model/major_alias_manager.yml');
        $loader->load('Model/major_manager.yml');
        $loader->load('Model/excluded_url_manager.yml');
        $loader->load('Search/job_api_connector.yml');
        $loader->load('Search/job_query_factory.yml');
        $loader->load('Twig/twig.yml');
        $loader->load('Util/canonicalizer.yml');
        $loader->load('Util/feed_reader.yml');
        $loader->load('Util/exclude_queue.yml');
    }
}
