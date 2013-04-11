<?php

namespace MajoredIn\JobSearchBundle\Tests\Util;

use MajoredIn\JobSearchBundle\Util\Canonicalizer;

class CanonicalizerTest extends \PHPUnit_Framework_TestCase
{
    public function testCanonizalizerInstanceOf()
    {
        $canon = $this->getCanonicalizer();
        $this->assertInstanceOf('MajoredIn\JobSearchBundle\Util\CanonicalizerInterface', $canon);
    }
    
    public function testCanonicalizeLowercase()
    {
        $canon = $this->getCanonicalizer();
        $this->assertEquals('lowercase', $canon->canonicalize('LoWeRcAsE'));
    }
    
    public function testCanonicalizeConvertSpaces()
    {
        $canon = $this->getCanonicalizer();
        $this->assertEquals('with space', $canon->canonicalize('with+space'));
        $this->assertEquals('with space', $canon->canonicalize('with-space'));
    }
    
    public function testCanonicalizeRemoveDuplicateSpaces()
    {
        $canon = $this->getCanonicalizer();
        $this->assertEquals('with space', $canon->canonicalize('with  space'));
        $this->assertEquals('with space', $canon->canonicalize('with   space'));
        $this->assertEquals('with space', $canon->canonicalize('with    space'));
    }
    
    public function testCanonicalizeLeadingSpaces()
    {
        $canon = $this->getCanonicalizer();
        $this->assertEquals('no leading', $canon->canonicalize(' no leading'));
        $this->assertEquals('no leading', $canon->canonicalize('  no leading'));
    }
    
    public function testCanonicalizeTrailingSpaces() //alters to allow 1 trailing space
    {
        $canon = $this->getCanonicalizer();
        $this->assertEquals('no trailing ', $canon->canonicalize('no trailing '));
        $this->assertEquals('no trailing ', $canon->canonicalize('no trailing  '));
    }
    
    public function testCanonicalizeRemoveInvalidChars()
    {
        $canon = $this->getCanonicalizer();
        $this->assertEquals('nothing fishy', $canon->canonicalize(",nothing fishy'"));
    }
    
    public function testCanonicalizeComprehensive()
    {
        $canon = $this->getCanonicalizer();
        $this->assertEquals('everything is good ', $canon->canonicalize('+-EvEryThing++ is-+-gOOd  '));
    }
    
    protected function getCanonicalizer()
    {
        return new Canonicalizer();
    }
}
