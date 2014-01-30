<?php

namespace MajoredIn\JobSearchBundle\Tests\Util;

use MajoredIn\JobSearchBundle\Util\ExcludeQueue;

class ExcludeQueueTest extends \PHPUnit_Framework_TestCase
{
    public function testExcludeQueueInstanceOf()
    {
        $queue = $this->getExcludeQueue();
        $this->assertInstanceOf('MajoredIn\JobSearchBundle\Util\ExcludeQueueInterface', $queue);
    }
    
    public function testAdd()
    {
        $queue = $this->getExcludeQueue();
        
        $this->assertEquals(0, $queue->size());
        $this->assertEquals(true, $queue->isEmpty());
        
        $queue->add($this->getRequest());
        
        $this->assertEquals(1, $queue->size());
        $this->assertEquals(false, $queue->isEmpty());
    }
    
    public function testRemove()
    {
        $queue = $this->getExcludeQueue();
        $request = $this->getRequest();
        
        $request->expects($this->once())
            ->method('getPathInfo')
            ->will($this->returnValue('/test'));
        
        $queue->add($request);
        
        $this->assertEquals(1, $queue->size());
        $this->assertEquals(false, $queue->isEmpty());
        
        $request = $queue->remove();
        
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Request', $request);
        $this->assertEquals('/test', $request->getPathInfo());
        $this->assertEquals(0, $queue->size());
        $this->assertEquals(true, $queue->isEmpty());
        
        $request = $queue->remove();
        
        $this->assertNull($request);
        $this->assertEquals(0, $queue->size());
        $this->assertEquals(true, $queue->isEmpty());
    }
    
    public function testQueue()
    {
        $queue = $this->getExcludeQueue();
        $request1 = $this->getRequest();
        $request2 = $this->getRequest();
    
        $request1->expects($this->once())
            ->method('getPathInfo')
            ->will($this->returnValue('/test1'));
        $request2->expects($this->once())
            ->method('getPathInfo')
            ->will($this->returnValue('/test2'));
    
        $queue->add($request1);
        $this->assertEquals(1, $queue->size());
        $this->assertEquals(false, $queue->isEmpty());
        
        $queue->add($request2);
        $this->assertEquals(2, $queue->size());
        $this->assertEquals(false, $queue->isEmpty());
    
        $request = $queue->remove();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Request', $request);
        $this->assertEquals('/test1', $request1->getPathInfo());
        $this->assertEquals(1, $queue->size());
        $this->assertEquals(false, $queue->isEmpty());
        
        $request = $queue->remove();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Request', $request);
        $this->assertEquals('/test2', $request->getPathInfo());
        $this->assertEquals(0, $queue->size());
        $this->assertEquals(true, $queue->isEmpty());
    }
    
    public function testSize()
    {
        $queue = $this->getExcludeQueue();
        
        $this->assertEquals(0, $queue->size());

        $queue->add($this->getRequest());
        $this->assertEquals(1, $queue->size());
        
        $queue->add($this->getRequest());
        $this->assertEquals(2, $queue->size());
    
        $queue->remove();
        $this->assertEquals(1, $queue->size());
        
        $queue->remove();
        $this->assertEquals(0, $queue->size());
        
        $queue->remove();
        $this->assertEquals(0, $queue->size());
    }
    
    public function testIsEmpty()
    {
        $queue = $this->getExcludeQueue();
        $request = $this->getRequest();
    
        $this->assertEquals(true, $queue->isEmpty());
    
        $queue->add($this->getRequest());
        $this->assertEquals(false, $queue->isEmpty());
        
        $queue->add($this->getRequest());
        $this->assertEquals(false, $queue->isEmpty());
    
        $queue->remove();
        $this->assertEquals(false, $queue->isEmpty());
    
        $queue->remove();
        $this->assertEquals(true, $queue->isEmpty());
        
        $queue->remove();
        $this->assertEquals(true, $queue->isEmpty());
    }
    
    protected function getExcludeQueue()
    {
        return new ExcludeQueue();
    }
    
    protected function getRequest()
    {
        return $this->getMock('Symfony\Component\HttpFoundation\Request');
    }
}
