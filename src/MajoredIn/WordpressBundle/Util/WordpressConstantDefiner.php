<?php

namespace MajoredIn\WordpressBundle\Util;

use Symfony\Component\DependencyInjection\Container;

class WordpressConstantDefiner implements ConstantDefinerInterface
{
    protected $container;
    
    protected static $defined = false;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    
    public function define()
    {
        if (!static::$defined) {
            define('MI_WP_PATH', $this->container->getParameter('mi_wordpress.install_path'));
            
            define('MI_MASTER_HOST', $this->container->getParameter('mi_wordpress.master_db.host'));
            define('MI_MASTER_PORT', $this->container->getParameter('mi_wordpress.master_db.port'));
            define('MI_MASTER_NAME', $this->container->getParameter('mi_wordpress.master_db.dbname'));
            define('MI_MASTER_USER', $this->container->getParameter('mi_wordpress.master_db.user'));
            define('MI_MASTER_PASSWORD', $this->container->getParameter('mi_wordpress.master_db.password'));
            define('MI_MASTER_CHARSET', $this->container->getParameter('mi_wordpress.master_db.charset'));
            
            define('MI_SLAVE_HOST', $this->container->getParameter('mi_wordpress.slave_db.host'));
            define('MI_SLAVE_PORT', $this->container->getParameter('mi_wordpress.slave_db.port'));
            define('MI_SLAVE_NAME', $this->container->getParameter('mi_wordpress.slave_db.dbname'));
            define('MI_SLAVE_USER', $this->container->getParameter('mi_wordpress.slave_db.user'));
            define('MI_SLAVE_PASSWORD', $this->container->getParameter('mi_wordpress.slave_db.password'));
            define('MI_SLAVE_CHARSET', $this->container->getParameter('mi_wordpress.slave_db.charset'));
            
            define('MI_AUTH_KEY', $this->container->getParameter('mi_wordpress.auth_key'));
            define('MI_SECURE_AUTH_KEY', $this->container->getParameter('mi_wordpress.secure_auth_key'));
            define('MI_LOGGED_IN_KEY', $this->container->getParameter('mi_wordpress.logged_in_key'));
            define('MI_NONCE_KEY', $this->container->getParameter('mi_wordpress.nonce_key'));
            define('MI_AUTH_SALT', $this->container->getParameter('mi_wordpress.auth_salt'));
            define('MI_SECURE_AUTH_SALT', $this->container->getParameter('mi_wordpress.secure_auth_salt'));
            define('MI_LOGGED_IN_SALT', $this->container->getParameter('mi_wordpress.logged_in_salt'));
            define('MI_NONCE_SALT', $this->container->getParameter('mi_wordpress.nonce_salt'));
            
            define('MI_TABLE_PREFIX', $this->container->getParameter('mi_wordpress.table_prefix'));
            
            static::$defined = true;
        }
    }
}
