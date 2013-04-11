<?php

namespace MajoredIn\JobSearchBundle\Tests\Model;

use MajoredIn\JobSearchBundle\Model\MajorAliasManager;
use MajoredIn\JobSearchBundle\Entity\MajorAlias;

class MajorAliasManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $majorAliasManager;
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
        
        $this->majorAliasManager = new MajorAliasManager($this->objectManager, $this->canonicalizer);
    }
    
    public function testMajorAliasManagerInstanceOf()
    {
        $this->assertInstanceOf('MajoredIn\JobSearchBundle\Model\MajorAliasManagerInterface', $this->majorAliasManager);
    }
    
    public function testCreateMajorAlias()
    {
        $majorAlias = $this->majorAliasManager->createMajorAlias();
        $this->assertInstanceOf('MajoredIn\JobSearchBundle\Model\MajorAliasInterface', $majorAlias);
    }
    
    public function testDeleteMajorAlias()
    {
        $majorAlias = $this->getMajorAlias();
        $this->objectManager->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($majorAlias));
        $this->objectManager->expects($this->once())
            ->method('flush');
    
        $this->majorAliasManager->deleteMajorAlias($majorAlias);
    }
    
    public function testFindMajorAliasBy()
    {
        $crit = array("foo" => "bar");
        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo($crit))
            ->will($this->returnValue(array()));
        
        $this->majorAliasManager->findMajorAliasBy($crit);
    }
    
    public function testFindMajorAliasByName()
    {
        $this->canonicalizer->expects($this->once())
            ->method('canonicalize')
            ->with($this->equalTo('MaJoRaLiAs'))
            ->will($this->returnValue('majoralias'));
        $this->repository->expects($this->once())
            ->method('findMajorAliasByNameCanonical')
            ->with($this->equalTo('majoralias'))
            ->will($this->returnValue(array()));
    
        $this->majorAliasManager->findMajorAliasByName('MaJoRaLiAs');
    }
    
    public function testFindMajorAliases()
    {
        $this->repository->expects($this->once())
        ->method('findAll')
        ->will($this->returnValue(array()));
    
        $this->majorAliasManager->findMajorAliases();
    }
    
    public function testReloadMajorAlias()
    {
        $majorAlias = $this->getMajorAlias();
        $this->objectManager->expects($this->once())
            ->method('refresh');
    
        $this->majorAliasManager->reloadMajorAlias($majorAlias);
    }
    
    public function testUpdateMajorAlias()
    {
        $majorAlias = $this->getMajorAlias();
        $majorAlias->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('TeSt'));
        $majorAlias->expects($this->once())
            ->method('setNameCanonical')
            ->with($this->equalTo('test'));
        $this->canonicalizer->expects($this->once())
            ->method('canonicalize')
            ->with($this->equalTo('TeSt'))
            ->will($this->returnValue('test'));
        $this->objectManager->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($majorAlias));
        $this->objectManager->expects($this->once())
            ->method('flush');
    
        $this->majorAliasManager->updateMajorAlias($majorAlias);
    }
    
    public function testUpdateMajorAliasNoFlush()
    {
        $majorAlias = $this->getMajorAlias();
        $majorAlias->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('TeSt'));
        $majorAlias->expects($this->once())
            ->method('setNameCanonical')
            ->with($this->equalTo('test'));
        $this->canonicalizer->expects($this->once())
            ->method('canonicalize')
            ->with($this->equalTo('TeSt'))
            ->will($this->returnValue('test'));
        $this->objectManager->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($majorAlias));
        $this->objectManager->expects($this->never())
            ->method('flush');
    
        $this->majorAliasManager->updateMajorAlias($majorAlias, false);
    }
    
    protected function getObjectManager()
    {
        return $this->getMock('Doctrine\Common\Persistence\ObjectManager');
    }
    
    protected function getRepository()
    {
        return $this->getMock('MajoredIn\JobSearchBundle\Tests\Model\DummyMajorAliasRepository');
    }
    
    protected function getCanonicalizer()
    {
        return $this->getMock('MajoredIn\JobSearchBundle\Util\Canonicalizer');
    }
    
    protected function getMajorAlias()
    {
        return $this->getMock('MajoredIn\JobSearchBundle\Entity\MajorAlias');
    }
}

//protected constructor of MajorRepository's parent class causing getMock issues
class DummyMajorAliasRepository extends \MajoredIn\JobSearchBundle\Entity\MajorAliasRepository
{
    public function __construct()
    {
        return;
    }
}