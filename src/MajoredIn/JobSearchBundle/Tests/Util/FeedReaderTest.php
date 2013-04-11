<?php

namespace MajoredIn\JobSearchBundle\Tests\Util;

use MajoredIn\JobSearchBundle\Util\FeedReader;

class FeedReaderTest extends \PHPUnit_Framework_TestCase
{
    public function testFeedReaderInstanceOf()
    {
        $feedReader = $this->getFeedReader();
        $this->assertInstanceOf('MajoredIn\JobSearchBundle\Util\FeedReaderInterface', $feedReader);
    }
    
    public function testReadUrl()
    {
        $feedReader = $this->getFeedReader();
        $url = 'http://api.simplyhired.com/a/jobs-api/xml-v2';
        $this->assertInternalType('string', $feedReader->readUrl($url));
    }
    
    public function testReadUrlFail()
    {
        $feedReader = $this->getFeedReader();
        $url = 'http://www.website.fail:1337';
        $this->assertEquals(false, $feedReader->readUrl($url));
    }
    
    protected function getFeedReader()
    {
        $options = array(
            'CURLOPT_HEADER' => false,
            'CURLOPT_RETURNTRANSFER' => true,
            'CURLOPT_FOLLOWLOCATION' => true,
            'CURLOPT_MAXREDIRS' => 3,
            'CURLOPT_CONNECTTIMEOUT' => 2,
            'CURLOPT_TIMEOUT' => 5
        );
        return new FeedReader($options);
    }
}
