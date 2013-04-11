<?php

namespace MajoredIn\JobSearchBundle\Tests\Search;

use MajoredIn\JobSearchBundle\Search\JobQuery;

class JobQueryTest extends \PHPUnit_Framework_TestCase
{
    public function testJobQueryInstanceOf()
    {
        $jobQuery = $this->getJobQuery();
        $this->assertInstanceOf('MajoredIn\JobSearchBundle\Search\JobQueryInterface', $jobQuery);
    }
    
    public function testSetQuery()
    {
        $jobQuery = $this->getJobQuery();
        $jobQuery->setQuery('test');
        $this->assertEquals('http://www.jobquerytest.com/q-test', $jobQuery->getApiUrl());
        $params = $jobQuery->getParams();
        $this->assertEquals('test', $params['q']);
        $this->assertNull($params['l']);
    }
    
    public function testSetLocation()
    {
        $jobQuery = $this->getJobQuery();
        $jobQuery->setLocation('test');
        $this->assertEquals('http://www.jobquerytest.com/l-test', $jobQuery->getApiUrl());
        $params = $jobQuery->getParams();
        $this->assertEquals('test', $params['l']);
        $this->assertNull($params['q']);
    }
    
    public function testAddRequiredParams()
    {
        $jobQuery = $this->getJobQuery();
        $jobQuery->addRequiredParam('param1', 'value1');
        $jobQuery->addRequiredParam('param2', 'value2');
        $this->assertEquals('http://www.jobquerytest.com?param1=value1&param2=value2', $jobQuery->getApiUrl());
        $params = $jobQuery->getParams();
        $this->assertEquals('value1', $params['param1']);
        $this->assertEquals('value2', $params['param2']);
    }
    
    public function testAddOptionalParams()
    {
        $jobQuery = $this->getJobQuery();
        $jobQuery->addOptionalParam('param1', 'value1');
        $jobQuery->addOptionalParam('param2', 'value2');
        $this->assertEquals('http://www.jobquerytest.com/param1-value1/param2-value2', $jobQuery->getApiUrl());
        $params = $jobQuery->getParams();
        $this->assertEquals('value1', $params['param1']);
        $this->assertEquals('value2', $params['param2']);
    }
    
    public function testGetApiUrl()
    {
        $jobQuery = $this->getJobQuery();
        $jobQuery->setQuery('query with illegal/slash');
        $this->assertEquals('http://www.jobquerytest.com/q-query+with+illegalslash', $jobQuery->getApiUrl());
        $jobQuery->setLocation('already encoded');
        $this->assertEquals('http://www.jobquerytest.com/q-query+with+illegalslash/l-already+encoded', $jobQuery->getApiUrl());
        $jobQuery->addOptionalParam('a+b+c', '1 2 3');
        $this->assertEquals('http://www.jobquerytest.com/q-query+with+illegalslash/l-already+encoded/a%2Bb%2Bc-1+2+3', $jobQuery->getApiUrl());
        $jobQuery->addRequiredParam('p/a/r/a/m', 'value-with_stuff');
        $this->assertEquals('http://www.jobquerytest.com/q-query+with+illegalslash/l-already+encoded/a%2Bb%2Bc-1+2+3?param=value-with_stuff', $jobQuery->getApiUrl());
    }
    
    public function testGetParams()
    {
        $jobQuery = $this->getJobQuery();
        $jobQuery->addOptionalParam('param1', 'value1');
        $jobQuery->addOptionalParam('param2', 'value2');
        //show optional params have priority in merged results in rare case of overlap
        $jobQuery->addRequiredParam('param1', 'value3');
        $params = $jobQuery->getParams();
        $this->assertEquals('value1', $params['param1']);
        $this->assertEquals('value2', $params['param2']);
    }
    
    public function testQueryLocationNullCases()
    {
        $jobQuery = $this->getJobQuery();
        $jobQuery->setQuery('');
        $jobQuery->setLocation(null);
        $this->assertEquals('http://www.jobquerytest.com', $jobQuery->getApiUrl());
        $params = $jobQuery->getParams();
        $this->assertEquals('', $params['q']);
        $this->assertEquals(null, $params['l']);
    }
    
    protected function getJobQuery()
    {
        return new JobQuery('http://www.jobquerytest.com');
    }
}
