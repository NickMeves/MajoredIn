<?php

namespace MajoredIn\JobSearchBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use MajoredIn\JobSearchBundle\Entity\Major;
use MajoredIn\JobSearchBundle\Util\CanonicalizerInterface;

class MajorManager implements MajorManagerInterface
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
        $this->repository = $om->getRepository('MajoredInJobSearchBundle:Major');
        $this->canonicalizer = $canonicalizer;
    }
    
    /**
     * {@inheritDoc}
     */
    public function createMajor()
    {
        $major = new Major;
        return $major;
    }
    
    /**
     * {@inheritDoc}
     */
    public function deleteMajor(MajorInterface $major)
    {
        $this->objectManager->remove($major);
        $this->objectManager->flush();
    }
    
    /**
     * {@inheritDoc}
     */
    public function findMajorBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }
    
    /**
     * {@inheritDoc}
     */
    public function findMajorByName($name)
    {
        return $this->repository->findMajorByNameCanonical($this->canonicalizeName($name));
    }
    
    /**
     * {@inheritDoc}
     */
    public function findMajors()
    {
        return $this->repository->findAll();
    }
    
    /**
     * {@inheritDoc}
     */
    public function findMajorsLike($name, $limit = null)
    {
        return $this->repository->findNameCanonicalLike($this->canonicalizeName($name), $limit);
    }
    
    /**
     * {@inheritDoc}
     */
    public function reloadMajor(MajorInterface $major)
    {
        $this->objectManager->refresh($major);
    }
    
    /**
     * {@inheritDoc}
     */
    public function updateMajor(MajorInterface $major, $andFlush = true)
    {
        $this->updateCanonicalFields($major);
        
        $this->objectManager->persist($major);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function updateCanonicalFields(MajorInterface $major)
    {
        $major->setNameCanonical($this->canonicalizeName($major->getName()));
    }
    
    
    /**
     * {@inheritDoc}
     */
    public function canonicalizeName($name)
    {
        return $this->canonicalizer->canonicalize($name);
        
    }
}
