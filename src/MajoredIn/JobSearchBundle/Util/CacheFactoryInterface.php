<?php

namespace MajoredIn\JobSearchBundle\Util;

interface CacheFactoryInterface
{
    public function getCache($namespace = '');
}
