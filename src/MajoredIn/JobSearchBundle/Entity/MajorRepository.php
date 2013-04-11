<?php

namespace MajoredIn\JobSearchBundle\Entity;

use Doctrine\ORM\EntityRepository;

class MajorRepository extends EntityRepository
{
    public function findNameCanonicalLike($string, $limit = null)
    {
        $query = $this->createQueryBuilder('m')
            ->where('m.nameCanonical LIKE :like')
            ->setParameter('like', $string . '%')
            ->orderBy('m.popularity', 'DESC')
            ->getQuery();
        
        if ($limit) {
            $query->setMaxResults($limit);
        }
        
        //$query->useResultCache(true); //No results cache, use case cache response body instead
        return $query->getResult();
    }
    
    public function findMajorByNameCanonical($nameCanonical)
    {
        $query = $this->createQueryBuilder('m')
            ->where('m.nameCanonical = :name')
            ->setParameter('name', $nameCanonical)
            ->getQuery();

        $query->useResultCache(true);
        $majors = $query->getResult();
        return $majors ? $majors[0] : null;
    }
}