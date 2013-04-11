<?php

namespace MajoredIn\JobSearchBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use MajoredIn\JobSearchBundle\Entity\Location;
use MajoredIn\JobSearchBundle\Util\CanonicalizerInterface;

class LocationManager implements LocationManagerInterface
{
    protected $objectManager;
    protected $repository;
    protected $canonicalizer;
    
    /**
     * Constructor.
     *
     * @param objectManager $om
     * @param CanonicalizerInterface $canonicalizer
     */
    public function __construct(objectManager $om, CanonicalizerInterface $canonicalizer)
    {
        $this->objectManager = $om;
        $this->repository = $om->getRepository('MajoredInJobSearchBundle:Location');
        $this->canonicalizer = $canonicalizer;
    }
    
    /**
     * {@inheritDoc}
     */
    public function createLocation()
    {
        $location = new Location;
        return $location;
    }
    
    /**
     * {@inheritDoc}
     */
    public function deleteLocation(LocationInterface $location)
    {
        $this->objectManager->remove($location);
        $this->objectManager->flush();
    }
    
    /**
     * {@inheritDoc}
     */
    public function findLocationBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }
    
    /**
     * {@inheritDoc}
     */
    public function findLocationByName($name)
    {
        return $this->repository->findLocationByNameCanonical($this->canonicalizeName($name));
    }
    
    /**
     * {@inheritDoc}
     */
    public function findLocations()
    {
        return $this->repository->findAll();
    }
    
    /**
     * {@inheritDoc}
     */
    public function findLocationsLike($name, $limit = null)
    {
        return $this->repository->findNameCanonicalLike($this->canonicalizeName($name), $limit);
    }
    
    /**
     * {@inheritDoc}
     */
    public function reloadLocation(LocationInterface $location)
    {
        $this->objectManager->refresh($location);
    }
    
    /**
     * {@inheritDoc}
     */
    public function updateLocation(LocationInterface $location, $andFlush = true)
    {
        $this->updateCanonicalFields($location);
        
        $this->objectManager->persist($location);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function updateCanonicalFields(LocationInterface $location)
    {
        $location->setNameCanonical($this->canonicalizeName($location->getName()));
    }
    
    
    /**
     * {@inheritDoc}
     */
    public function canonicalizeName($name)
    {
        return $this->canonicalizer->canonicalize($name);
    }
}
