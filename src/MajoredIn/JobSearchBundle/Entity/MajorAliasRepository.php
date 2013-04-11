<?php

namespace MajoredIn\JobSearchBundle\Entity;

use Doctrine\ORM\EntityRepository;

class MajorAliasRepository extends EntityRepository
{
    public function findMajorAliasByNameCanonical($nameCanonical)
    {
        $query = $this->createQueryBuilder('m')
            ->where('m.nameCanonical = :name')
            ->setParameter('name', $nameCanonical)
            ->getQuery();
        
        $query->useResultCache(true);
        $majorAliases = $query->getResult();
        return $majorAliases ? $majorAliases[0] : null;
    }
}