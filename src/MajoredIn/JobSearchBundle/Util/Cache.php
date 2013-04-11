<?php

namespace MajoredIn\JobSearchBundle\Util;

use Doctrine\Common\Cache\CacheProvider;

class Cache implements CacheInterface
{
    protected $doctrineCache;
    
    public function __construct(CacheProvider $doctrineCache)
    {
        $this->doctrineCache = $doctrineCache;
    }
    
    function fetch($id)
    {
        return $this->doctrineCache->fetch($id);
    }

    function contains($id)
    {
        return $this->doctrineCache->contains($id);
    }

    function save($id, $data, $lifeTime = 0)
    {
        return $this->doctrineCache->save($id, $data, $lifeTime);
    }

    function delete($id)
    {
        return $this->doctrineCache->delete($id);
    }
}
