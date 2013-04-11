<?php

namespace MajoredIn\JobSearchBundle\Entity;

use Doctrine\ORM\EntityRepository;

class LocationRepository extends EntityRepository
{
    public function findNameCanonicalLike($string, $limit = null)
    {
        $query = $this->createQueryBuilder('l')
            ->where('l.nameCanonical LIKE :like')
            ->setParameter('like', $string . '%')
            ->orderBy('l.population', 'DESC')
            ->getQuery();
        
        if ($limit) {
            $query->setMaxResults($limit);
        }
        
        //$query->useResultCache(true); //No results cache, use case cache response body instead
        return $query->getResult();
    }
    
    public function findLocationByNameCanonical($nameCanonical)
    {
        $query = $this->createQueryBuilder('l')
            ->where('l.nameCanonical = :name')
            ->setParameter('name', $nameCanonical)
            ->getQuery();
    
        $query->useResultCache(true);
        $locations = $query->getResult();
        return $locations ? $locations[0] : null;
    }
}