<?php

namespace MajoredIn\JobSearchBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use MajoredIn\JobSearchBundle\Entity\MajorAlias;
use MajoredIn\JobSearchBundle\Util\CanonicalizerInterface;

class MajorAliasManager implements MajorAliasManagerInterface
{
    protected $objectManager;
    protected $repository;
    protected $canonicalizer;
    
    /**
     * Constructor.
     *
     * @param EntityManager $em
     * @param CanonicalizerInterface $canonicalizer
     */
    public function __construct(ObjectManager $om, CanonicalizerInterface $canonicalizer)
    {
        $this->objectManager = $om;
        $this->repository = $om->getRepository('MajoredInJobSearchBundle:MajorAlias');
        $this->canonicalizer = $canonicalizer;
    }
    
    /**
     * {@inheritDoc}
     */
    public function createMajorAlias()
    {
        $majorAlias = new MajorAlias;
        return $majorAlias;
    }
    
    /**
     * {@inheritDoc}
     */
    public function deleteMajorAlias(MajorAliasInterface $majorAlias)
    {
        $this->objectManager->remove($majorAlias);
        $this->objectManager->flush();
    }
    
    /**
     * {@inheritDoc}
     */
    public function findMajorAliasBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }
    
    /**
     * {@inheritDoc}
     */
    public function findMajorAliasByName($name)
    {
        return $this->repository->findMajorAliasByNameCanonical($this->canonicalizeName($name));
    }
    
    /**
     * {@inheritDoc}
     */
    public function findMajorAliases()
    {
        return $this->repository->findAll();
    }
    
    /**
     * {@inheritDoc}
     */
    public function reloadMajorAlias(MajorAliasInterface $majorAlias)
    {
        $this->objectManager->refresh($majorAlias);
    }
    
    /**
     * {@inheritDoc}
     */
    public function updateMajorAlias(MajorAliasInterface $majorAlias, $andFlush = true)
    {
        $this->updateCanonicalFields($majorAlias);
        
        $this->objectManager->persist($majorAlias);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function updateCanonicalFields(MajorAliasInterface $majorAlias)
    {
        $majorAlias->setNameCanonical($this->canonicalizeName($majorAlias->getName()));
    }
    
    
    /**
     * {@inheritDoc}
     */
    public function canonicalizeName($name)
    {
        return $this->canonicalizer->canonicalize($name);
        
    }
}
