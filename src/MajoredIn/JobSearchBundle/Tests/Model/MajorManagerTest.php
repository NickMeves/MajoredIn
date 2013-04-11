<?php

namespace MajoredIn\JobSearchBundle\Tests\Model;

use MajoredIn\JobSearchBundle\Model\MajorManager;
use MajoredIn\JobSearchBundle\Entity\Major;

class MajorManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $majorManager;
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
        
        $this->majorManager = new MajorManager($this->objectManager, $this->canonicalizer);
    }
    
    public function testMajorManagerInstanceOf()
    {
        $this->assertInstanceOf('MajoredIn\JobSearchBundle\Model\MajorManagerInterface', $this->majorManager);
    }
    
    public function testCreateMajor()
    {
        $major = $this->majorManager->createMajor();
        $this->assertInstanceOf('MajoredIn\JobSearchBundle\Model\MajorInterface', $major);
    }
    
    public function testDeleteMajor()
    {
        $major = $this->getMajor();
        $this->objectManager->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($major));
        $this->objectManager->expects($this->once())
            ->method('flush');
    
        $this->majorManager->deleteMajor($major);
    }
    
    public function testFindMajorBy()
    {
        $crit = array("foo" => "bar");
        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo($crit))
            ->will($this->returnValue(array()));
        
        $this->majorManager->findMajorBy($crit);
    }
    
    public function testFindMajorByName()
    {
        $this->canonicalizer->expects($this->once())
            ->method('canonicalize')
            ->with($this->equalTo('MaJoR'))
            ->will($this->returnValue('major'));
        $this->repository->expects($this->once())
            ->method('findMajorByNameCanonical')
            ->with($this->equalTo('major'))
            ->will($this->returnValue(array()));
    
        $this->majorManager->findMajorByName('MaJoR');
    }
    
    public function testFindMajors()
    {
        $this->repository->expects($this->once())
        ->method('findAll')
        ->will($this->returnValue(array()));
    
        $this->majorManager->findMajors();
    }
    
    public function testFindMajorsLike()
    {
        $this->canonicalizer->expects($this->once())
            ->method('canonicalize')
            ->with($this->equalTo('MaJoR'))
            ->will($this->returnValue('major'));
        $this->repository->expects($this->once())
            ->method('findNameCanonicalLike')
            ->with($this->equalTo('major'), $this->equalTo(5))
            ->will($this->returnValue(array()));
    
        $this->majorManager->findMajorsLike('MaJoR', 5);
    }
    
    public function testReloadMajor()
    {
        $major = $this->getMajor();
        $this->objectManager->expects($this->once())
            ->method('refresh');
    
        $this->majorManager->reloadMajor($major);
    }
    
    public function testUpdateMajor()
    {
        $major = $this->getMajor();
        $major->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('TeSt'));
        $major->expects($this->once())
            ->method('setNameCanonical')
            ->with($this->equalTo('test'));
        $this->canonicalizer->expects($this->once())
            ->method('canonicalize')
            ->with($this->equalTo('TeSt'))
            ->will($this->returnValue('test'));
        $this->objectManager->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($major));
        $this->objectManager->expects($this->once())
            ->method('flush');
    
        $this->majorManager->updateMajor($major);
    }
    
    public function testUpdateMajorNoFlush()
    {
        $major = $this->getMajor();
        $major->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('TeSt'));
        $major->expects($this->once())
            ->method('setNameCanonical')
            ->with($this->equalTo('test'));
        $this->canonicalizer->expects($this->once())
            ->method('canonicalize')
            ->with($this->equalTo('TeSt'))
            ->will($this->returnValue('test'));
        $this->objectManager->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($major));
        $this->objectManager->expects($this->never())
            ->method('flush');
    
        $this->majorManager->updateMajor($major, false);
    }
    
    protected function getObjectManager()
    {
        return $this->getMock('Doctrine\Common\Persistence\ObjectManager');
    }
    
    protected function getRepository()
    {
        return $this->getMock('MajoredIn\JobSearchBundle\Tests\Model\DummyMajorRepository');
    }
    
    protected function getCanonicalizer()
    {
        return $this->getMock('MajoredIn\JobSearchBundle\Util\Canonicalizer');
    }
    
    protected function getMajor()
    {
        return $this->getMock('MajoredIn\JobSearchBundle\Entity\Major');
    }
}

//protected constructor of MajorRepository's parent class causing getMock issues
class DummyMajorRepository extends \MajoredIn\JobSearchBundle\Entity\MajorRepository
{
    public function __construct()
    {
        return;
    }
}