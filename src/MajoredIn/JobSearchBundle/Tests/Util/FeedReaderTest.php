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
        $this->assertInternalType('string', $feedReader->get($url)->__toString());
    }
    
    /**
     * @expectedException MajoredIn\JobSearchBundle\Exception\GatewayTimeoutException
     */
    public function testReadUrlFailTimeout()
    {
        $url = 'http://www.website.fail:1337';
        $this->getFeedReader()->get($url);
    }
    
    /**
     * @expectedException MajoredIn\JobSearchBundle\Exception\PageNotFoundException
     */
    public function testReadUrlFail404()
    {
        $url = 'http://api.simplyhired.com/a/jobs-api/xml-v404Me';
        $this->getFeedReader()->get($url);
    }
    
    protected function getFeedReader()
    {
        $options = array(
            'timeout' => 10,
            'connect_timeout' => 1.5
        );
        return new FeedReader($options);
    }
}
