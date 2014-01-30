<?php

namespace MajoredIn\JobSearchBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ExcludedUrlRepository extends EntityRepository
{
    public function findUrlLike($string, $limit = null)
    {
        $query = $this->createQueryBuilder('e')
            ->where('e.url LIKE :like')
            ->setParameter('like', $string . '%')
            ->getQuery();
        
        if ($limit) {
            $query->setMaxResults($limit);
        }
        
        return $query->getResult();
    }
    
    public function findExcludedUrlbyUrl($url)
    {
        $query = $this->createQueryBuilder('e')
            ->where('e.url = :url')
            ->setParameter('url', $url)
            ->getQuery();

        $excludedUrls = $query->getResult();
        return $excludedUrls ? $excludedUrls[0] : null;
    }
}