<?php

namespace MajoredIn\JobSearchBundle\Tests\Twig\Extension;

use MajoredIn\JobSearchBundle\Twig\Extension\JobSearchExtension;

class JobSearchExtensionTest extends \PHPUnit_Framework_TestCase
{
    
    protected $canonicalizer;
    protected $jobSearchExtension;
    
    public function setUp()
    {
        $this->canonicalizer = $this->getCanonicalizer();
        $this->jobSearchExtension = new JobSearchExtension($this->canonicalizer);
    }
    
    public function testNormalInput()
    {
        $this->canonicalizer->expects($this->once())
            ->method('canonicalize')
            ->with($this->equalTo('Irvine, CA'))
            ->will($this->returnValue('irvine ca'));
        
        $this->assertEquals('Irvine, CA', $this->jobSearchExtension->formatLoc('Irvine, CA'));
    }
    
    public function testNoComma()
    {
        $this->canonicalizer->expects($this->once())
            ->method('canonicalize')
            ->with($this->equalTo('Irvine CA'))
            ->will($this->returnValue('irvine ca'));
        
        $this->assertEquals('Irvine, CA', $this->jobSearchExtension->formatLoc('Irvine CA'));
    }
    
    public function testLowercase()
    {
        $this->canonicalizer->expects($this->once())
        ->method('canonicalize')
        ->with($this->equalTo('irvine, ca'))
        ->will($this->returnValue('irvine ca'));
    
        $this->assertEquals('Irvine, CA', $this->jobSearchExtension->formatLoc('irvine, ca'));
    }
    
    public function testNoState()
    {
        $this->canonicalizer->expects($this->once())
        ->method('canonicalize')
        ->with($this->equalTo('Los Angeles'))
        ->will($this->returnValue('los angeles'));
    
        $this->assertEquals('Los Angeles', $this->jobSearchExtension->formatLoc('Los Angeles'));
    }
    
    public function testOnlyState()
    {
        $this->canonicalizer->expects($this->once())
        ->method('canonicalize')
        ->with($this->equalTo('Ca'))
        ->will($this->returnValue('ca'));
    
        $this->assertEquals('CA', $this->jobSearchExtension->formatLoc('Ca'));
    }
    
    protected function getCanonicalizer()
    {
        return $this->getMock('MajoredIn\JobSearchBundle\Util\Canonicalizer');
    }
}
