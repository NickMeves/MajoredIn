<?php

namespace MajoredIn\JobSearchBundle\Tests\Model;

use MajoredIn\JobSearchBundle\Model\LocationManager;
use MajoredIn\JobSearchBundle\Entity\Location;

class LocationManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $locationManager;
    protected $objectManager;
    protected $repository;
    protected $canonicalizer;
    
    public function setUp()
    {
        $this->objectManager = $this->getObjectManager();
        $this->repository = $this->getRepository();
        $this->canonicalizer = $this->getCanonicalizer();
        
        $this->objectManager->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($this->repository));
        
        $this->locationManager = new LocationManager($this->objectManager, $this->canonicalizer);
    }
    
    public function testLocationManagerInstanceOf()
    {
        $this->assertInstanceOf('MajoredIn\JobSearchBundle\Model\LocationManagerInterface', $this->locationManager);
    }
    
    public function testCreateLocation()
    {
        $location = $this->locationManager->createLocation();
        $this->assertInstanceOf('MajoredIn\JobSearchBundle\Model\LocationInterface', $location);
    }
    
    public function testDeleteLocation()
    {
        $location = $this->getLocation();
        $this->objectManager->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($location));
        $this->objectManager->expects($this->once())
            ->method('flush');
    
        $this->locationManager->deleteLocation($location);
    }
    
    public function testFindLocationBy()
    {
        $crit = array("foo" => "bar");
        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo($crit))
            ->will($this->returnValue(array()));
        
        $this->locationManager->findLocationBy($crit);
    }
    
    public function testFindLocationByName()
    {
        $this->canonicalizer->expects($this->once())
            ->method('canonicalize')
            ->with($this->equalTo('LoCaTioN'))
            ->will($this->returnValue('location'));
        $this->repository->expects($this->once())
            ->method('findLocationByNameCanonical')
            ->with($this->equalTo('location'))
            ->will($this->returnValue(array()));
    
        $this->locationManager->findLocationByName('LoCaTioN');
    }
    
    public function testFindLocations()
    {
        $this->repository->expects($this->once())
        ->method('findAll')
        ->will($this->returnValue(array()));
    
        $this->locationManager->findLocations();
    }
    
    public function testFindLocationsLike()
    {
        $this->canonicalizer->expects($this->once())
            ->method('canonicalize')
            ->with($this->equalTo('LoCaTioN'))
            ->will($this->returnValue('location'));
        $this->repository->expects($this->once())
            ->method('findNameCanonicalLike')
            ->with($this->equalTo('location'), $this->equalTo(5))
            ->will($this->returnValue(array()));
    
        $this->locationManager->findLocationsLike('LoCaTioN', 5);
    }
    
    public function testReloadLocation()
    {
        $location = $this->getLocation();
        $this->objectManager->expects($this->once())
            ->method('refresh');
    
        $this->locationManager->reloadLocation($location);
    }
    
    public function testUpdateLocation()
    {
        $location = $this->getLocation();
        $location->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('TeSt'));
        $location->expects($this->once())
            ->method('setNameCanonical')
            ->with($this->equalTo('test'));
        $this->canonicalizer->expects($this->once())
            ->method('canonicalize')
            ->with($this->equalTo('TeSt'))
            ->will($this->returnValue('test'));
        $this->objectManager->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($location));
        $this->objectManager->expects($this->once())
            ->method('flush');
    
        $this->locationManager->updateLocation($location);
    }
    
    public function testUpdateLocationNoFlush()
    {
        $location = $this->getLocation();
        $location->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('TeSt'));
        $location->expects($this->once())
            ->method('setNameCanonical')
            ->with($this->equalTo('test'));
        $this->canonicalizer->expects($this->once())
            ->method('canonicalize')
            ->with($this->equalTo('TeSt'))
            ->will($this->returnValue('test'));
        $this->objectManager->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($location));
        $this->objectManager->expects($this->never())
            ->method('flush');
    
        $this->locationManager->updateLocation($location, false);
    }
    
    protected function getObjectManager()
    {
        return $this->getMock('Doctrine\Common\Persistence\ObjectManager');
    }
    
    protected function getRepository()
    {
        return $this->getMock('MajoredIn\JobSearchBundle\Tests\Model\DummyLocationRepository');
    }
    
    protected function getCanonicalizer()
    {
        return $this->getMock('MajoredIn\JobSearchBundle\Util\Canonicalizer');
    }
    
    protected function getLocation()
    {
        return $this->getMock('MajoredIn\JobSearchBundle\Entity\Location');
    }
}

//protected constructor of LocationRepository's parent class causing getMock issues
class DummyLocationRepository extends \MajoredIn\JobSearchBundle\Entity\LocationRepository
{
    public function __construct()
    {
        return;
    }
}