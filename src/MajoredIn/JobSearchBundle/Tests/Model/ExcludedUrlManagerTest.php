<?php

namespace MajoredIn\JobSearchBundle\Tests\Model;

use MajoredIn\JobSearchBundle\Model\ExcludedUrlManager;
use MajoredIn\JobSearchBundle\Entity\ExcludedUrl;

class ExcludedUrlManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $excludedUrlManager;
    protected $objectManager;
    protected $repository;
    
    public function setUp()
    {
        $this->objectManager = $this->getObjectManager();
        $this->repository = $this->getRepository();
        
        $this->objectManager->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($this->repository));
        
        $this->excludedUrlManager = new ExcludedUrlManager($this->objectManager);
    }
    
    public function testExcludedUrlManagerInstanceOf()
    {
        $this->assertInstanceOf('MajoredIn\JobSearchBundle\Model\ExcludedUrlManagerInterface', $this->excludedUrlManager);
    }
    
    public function testCreateExcludedUrl()
    {
        $excludedUrl = $this->excludedUrlManager->createExcludedUrl();
        $this->assertInstanceOf('MajoredIn\JobSearchBundle\Model\ExcludedUrlInterface', $excludedUrl);
    }
    
    public function testDeleteExcludedUrl()
    {
        $excludedUrl = $this->getExcludedUrl();
        $this->objectManager->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($excludedUrl));
        $this->objectManager->expects($this->once())
            ->method('flush');
    
        $this->excludedUrlManager->deleteExcludedUrl($excludedUrl);
    }
    
    public function testFindExcludedUrlBy()
    {
        $crit = array("foo" => "bar");
        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo($crit))
            ->will($this->returnValue(array()));
        
        $this->excludedUrlManager->findExcludedUrlBy($crit);
    }
    
    public function testFindExcludedUrlByUrl()
    {
        $this->repository->expects($this->once())
            ->method('findExcludedUrlByUrl')
            ->with($this->equalTo('excludedUrl'))
            ->will($this->returnValue(array()));
    
        $this->excludedUrlManager->findExcludedUrlByUrl('excludedUrl');
    }
    
    public function testFindExcludedUrls()
    {
        $this->repository->expects($this->once())
        ->method('findAll')
        ->will($this->returnValue(array()));
    
        $this->excludedUrlManager->findExcludedUrls();
    }
    
    public function testFindExcludedUrlsLike()
    {
        $this->repository->expects($this->once())
            ->method('findUrlLike')
            ->with($this->equalTo('excludedUrl'), $this->equalTo(5))
            ->will($this->returnValue(array()));
    
        $this->excludedUrlManager->findExcludedUrlsLike('excludedUrl', 5);
    }
    
    public function testReloadExcludedUrl()
    {
        $excludedUrl = $this->getExcludedUrl();
        $this->objectManager->expects($this->once())
            ->method('refresh');
    
        $this->excludedUrlManager->reloadExcludedUrl($excludedUrl);
    }
    
    public function testUpdateExcludedUrl()
    {
        $excludedUrl = $this->getExcludedUrl();
        $this->objectManager->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($excludedUrl));
        $this->objectManager->expects($this->once())
            ->method('flush');
    
        $this->excludedUrlManager->updateExcludedUrl($excludedUrl);
    }
    
    public function testUpdateExcludedUrlNoFlush()
    {
        $excludedUrl = $this->getExcludedUrl();
        $this->objectManager->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($excludedUrl));
        $this->objectManager->expects($this->never())
            ->method('flush');
    
        $this->excludedUrlManager->updateExcludedUrl($excludedUrl, false);
    }
    
    protected function getObjectManager()
    {
        return $this->getMock('Doctrine\Common\Persistence\ObjectManager');
    }
    
    protected function getRepository()
    {
        return $this->getMock('MajoredIn\JobSearchBundle\Tests\Model\DummyExcludedUrlRepository');
    }
    
    protected function getExcludedUrl()
    {
        return $this->getMock('MajoredIn\JobSearchBundle\Entity\ExcludedUrl');
    }
}

//protected constructor of ExcludedUrlRepository's parent class causing getMock issues
class DummyExcludedUrlRepository extends \MajoredIn\JobSearchBundle\Entity\ExcludedUrlRepository
{
    public function __construct()
    {
        return;
    }
}