<?php

namespace MajoredIn\WordpressBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\Definition\Processor;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class MajoredInWordpressExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('Util\constant_definer.yml');
        $loader->load('Util\state_manager.yml');
        $loader->load('Util\wordpress_api.yml');
        
        $container->setParameter('mi_wordpress.install_path', $config['install_path']);
        
        $container->setParameter('mi_wordpress.master_db.host', $config['database']['master']['host']);
        $container->setParameter('mi_wordpress.master_db.port', $config['database']['master']['port']);
        $container->setParameter('mi_wordpress.master_db.dbname', $config['database']['master']['dbname']);
        $container->setParameter('mi_wordpress.master_db.user', $config['database']['master']['user']);
        $container->setParameter('mi_wordpress.master_db.password', $config['database']['master']['password']);
        $container->setParameter('mi_wordpress.master_db.charset', $config['database']['master']['charset']);
        
        $container->setParameter('mi_wordpress.slave_db.host', $config['database']['slave']['host']);
        $container->setParameter('mi_wordpress.slave_db.port', $config['database']['slave']['port']);
        $container->setParameter('mi_wordpress.slave_db.dbname', $config['database']['slave']['dbname']);
        $container->setParameter('mi_wordpress.slave_db.user', $config['database']['slave']['user']);
        $container->setParameter('mi_wordpress.slave_db.password', $config['database']['slave']['password']);
        $container->setParameter('mi_wordpress.slave_db.charset', $config['database']['slave']['charset']);
        
        $container->setParameter('mi_wordpress.auth_key', $config['auth_key']);
        $container->setParameter('mi_wordpress.secure_auth_key', $config['secure_auth_key']);
        $container->setParameter('mi_wordpress.logged_in_key', $config['logged_in_key']);
        $container->setParameter('mi_wordpress.nonce_key', $config['nonce_key']);
        $container->setParameter('mi_wordpress.auth_salt', $config['auth_salt']);
        $container->setParameter('mi_wordpress.secure_auth_salt', $config['secure_auth_salt']);
        $container->setParameter('mi_wordpress.logged_in_salt', $config['logged_in_salt']);
        $container->setParameter('mi_wordpress.nonce_salt', $config['nonce_salt']);
        
        $container->setParameter('mi_wordpress.table_prefix', $config['table_prefix']);
    }
}
