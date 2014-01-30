<?php

namespace MajoredIn\JobSearchBundle\Tests\Util;

use MajoredIn\JobSearchBundle\Util\Canonicalizer;

class CanonicalizerTest extends \PHPUnit_Framework_TestCase
{
    protected $canonicalizer;
    
    public function setUp()
    {
        $this->canonicalizer = $this->getCanonicalizer();
    }
    
    public function testCanonizalizerInstanceOf()
    {
        $this->assertInstanceOf('MajoredIn\JobSearchBundle\Util\CanonicalizerInterface', $this->canonicalizer);
    }
    
    public function testCanonicalizeLowercase()
    {
        $this->assertEquals('lowercase', $this->canonicalizer->canonicalize('LoWeRcAsE'));
    }
    
    public function testCanonicalizeConvertSpaces()
    {
        $this->assertEquals('with space', $this->canonicalizer->canonicalize('with+space'));
        $this->assertEquals('with space', $this->canonicalizer->canonicalize('with-space'));
    }
    
    public function testCanonicalizeRemoveDuplicateSpaces()
    {
        $this->assertEquals('with space', $this->canonicalizer->canonicalize('with  space'));
        $this->assertEquals('with space', $this->canonicalizer->canonicalize('with   space'));
        $this->assertEquals('with space', $this->canonicalizer->canonicalize('with    space'));
    }
    
    public function testCanonicalizeLeadingSpaces()
    {
        $this->assertEquals('no leading', $this->canonicalizer->canonicalize(' no leading'));
        $this->assertEquals('no leading', $this->canonicalizer->canonicalize('  no leading'));
    }
    
    public function testCanonicalizeTrailingSpaces() //alters to allow 1 trailing space
    {
        $this->assertEquals('no trailing ', $this->canonicalizer->canonicalize('no trailing '));
        $this->assertEquals('no trailing ', $this->canonicalizer->canonicalize('no trailing  '));
    }
    
    public function testCanonicalizeRemoveInvalidChars()
    {
        $this->assertEquals('nothing fishy', $this->canonicalizer->canonicalize(",nothing fishy'"));
    }
    
    public function testCanonicalizeComprehensive()
    {
        $this->assertEquals('everything is good ', $this->canonicalizer->canonicalize('+-EvEryThing++ is-+-gOOd  '));
    }
    
    public function testDashSpaces()
    {
        $this->assertEquals('just-one-dash', $this->canonicalizer->dash('just one dash'));
        $this->assertEquals('just-one-dash', $this->canonicalizer->dash('just  one  dash'));
        $this->assertEquals('just-one-dash', $this->canonicalizer->dash('just   one   dash'));
    }
    
    public function testDashDashes()
    {
        $this->assertEquals('under_score', $this->canonicalizer->dash('under-score'));
    }
    
    public function testDashLeadingSpaces()
    {
        $this->assertEquals('just-one-dash', $this->canonicalizer->dash(' just one dash'));
        $this->assertEquals('just-one-dash', $this->canonicalizer->dash('  just one dash'));
        $this->assertEquals('just-one-dash', $this->canonicalizer->dash('   just one dash'));
    }
    
    public function testDashTrailingSpaces()
    {
        $this->assertEquals('just-one-dash', $this->canonicalizer->dash('just one dash '));
        $this->assertEquals('just-one-dash', $this->canonicalizer->dash('just one dash  '));
        $this->assertEquals('just-one-dash', $this->canonicalizer->dash(' just one dash  '));
    }
    
    public function testDashRemoveSlash()
    {
        $this->assertEquals('noslash', $this->canonicalizer->dash('no/slash'));
    }
    
    public function testDashComprehensive()
    {
        $this->assertEquals('everything-is_good', $this->canonicalizer->dash('  every/thing   is-good  '));
    }
    
    public function testUndashDash()
    {
        $this->assertEquals('no space', $this->canonicalizer->undash('no-space'));
    }
    
    public function testUndashUnderscore()
    {
        $this->assertEquals('underscore-dash', $this->canonicalizer->undash('underscore_dash'));
    }
    
    public function testUndashComprehensive()
    {
        $this->assertEquals('everything is a-ok', $this->canonicalizer->undash('everything-is-a_ok'));
    }
    
    public function testFormatLocationNormalInput()
    {
        $this->assertEquals('Irvine, CA', $this->canonicalizer->formatLocation('Irvine, CA'));
    }
    
    public function testFormatLocationNoComma()
    {
        $this->assertEquals('Irvine, CA', $this->canonicalizer->formatLocation('Irvine CA'));
    }
    
    public function testFormatLocationLowercase()
    {
        $this->assertEquals('Irvine, CA', $this->canonicalizer->formatLocation('irvine, ca'));
    }
    
    public function testFormatLocationNoState()
    {
        $this->assertEquals('Los Angeles', $this->canonicalizer->formatLocation('Los Angeles'));
    }
    
    public function testFormatLocationOnlyState()
    {
        $this->assertEquals('CA', $this->canonicalizer->formatLocation('Ca'));
    }
    
    protected function getCanonicalizer()
    {
        return new Canonicalizer();
    }
}
