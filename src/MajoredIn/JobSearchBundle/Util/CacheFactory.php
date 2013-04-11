<?php

namespace MajoredIn\JobSearchBundle\Util;

use Doctrine\ORM\EntityManager;

class CacheFactory implements CacheFactoryInterface
{
    protected $doctrineCache;
    
    public function __construct(EntityManager $em)
    {
        $this->doctrineCache = $em->getConfiguration()->getResultCacheImpl();
    }
    
    public function getCache($namespace = '')
    {
        $doctrineCache = clone $this->doctrineCache;
        $doctrineCache->setNamespace($namespace);
        
        $cache = new Cache($doctrineCache);
        
        return $cache;
    }
}
