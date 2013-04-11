<?php

namespace MajoredIn\JobSearchBundle\Tests\Search;

use MajoredIn\JobSearchBundle\Search\JobQueryFactory;

class JobQueryFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $jobQueryFactory;
    protected $majorManager;
    protected $majorEntity;
    protected $majorAliasManager;
    protected $majorAliasEntity;
    protected $request;
    protected $parameterBag;
    
    public function setUp()
    {
        $this->majorManager = $this->getMajorManager();
        $this->majorEntity = $this->getMajorEntity();
        $this->majorAliasManager = $this->getMajorAliasManager();
        $this->majorAliasEntity = $this->getMajorAliasEntity();
        $this->request = $this->getRequest();
        $this->parameterBag = $this->getParameterBag();
        
        $this->request->query = $this->parameterBag;
        
        $allowableParams = array(
            'dist' => array(
                'translation' => 'mi',
                'requirements' => '/^([1-9]\d*|exact)$/'
            ),
            'page' => array(
                'translation' => 'pn',
                'requirements' => '/^[1-9]\d*$/'
            ),
            'date' => array(
                'translation' => 'fdb',
                'requirements' => '/^[1-9]\d*$/'
            ),
            'sort' => array(
                'translation' => 'sb',
                'requirements' => '/^(rd|ra|dd|da|td|ta|cd|ca|ld|la)$/'
            ),
            'jobtype' => array(
                'translation' => 'fjt',
                'requirements' => '/^(full-time|part-time|internship)$/'
            ),
            'boards' => array(
                'translation' => 'fsr',
                'requirements' => '/^(primary|job_board)$/'
            ),
            'recruiters' => array(
                'translation' => 'fem',
                'requirements' => '/^(employer|recruiter)$/'
            ),
            'size' => array(
                'translation' => 'fcz',
                'requirements' => '/^(1|2|3|4|5|6)$/'
            ),
            'revenue' => array(
                'translation' => 'fcr',
                'requirements' => '/^(1|2|3|4|5|6)$/'
            ),
            'all' => array(
                'translation' => 'qa',
                'requirements' => '/^.+$/'
            ),
            'exact' => array(
                'translation' => 'qe',
                'requirements' => '/^.+$/'
            ),
            'atleast' => array(
                'translation' => 'qo',
                'requirements' => '/^.+$/'
            ),
            'without' => array(
                'translation' => 'qw',
                'requirements' => '/^.+$/'
            ),
            'title' => array(
                'translation' => 't',
                'requirements' => '/^.+$/'
            ),
            'company' => array(
                'translation' => 'c',
                'requirements' => '/^.+$/'
            ),
            'filter' => array(
                'translation' => 'clst',
                'requirements' => '/^comboxp0$/'
            )
        );
        
        $this->jobQueryFactory = new JobQueryFactory(
            $this->majorManager,
            $this->majorAliasManager,
            'http://api.simplyhired.com/a/jobs-api/xml-v2',
            $allowableParams,
            '43742',
            'dailycal.jobamatic.com',
            '2',
            'r'
        );
    }
    
    public function testCreateFromRequest()
    {
        $this->majorManager->expects($this->once())
            ->method('findMajorByName')
            ->with('software test')
            ->will($this->returnValue($this->majorEntity));
        
        $this->majorEntity->expects($this->once())
            ->method('getJobQuery')
            ->will($this->returnValue('(software engineer) OR (software developer)'));
        
        $this->request->expects($this->once())
            ->method('getClientIp')
            ->will($this->returnValue('127.0.0.1'));
        
        $this->parameterBag->expects($this->once())
            ->method('all')
            ->will($this->returnValue(array(
                'dist' => 25,
                'page' => 2,
                'date' => '14',
                'sort' => 'dd',
                'jobtype' => 'full-time',
                'boards' => 'job_board',
                'recruiters' => 'employer',
                'size' => 3,
                'revenue' => 6,
                'filter' => 'comboxp0'
            ))
        );
        
        $jobQuery = $this->jobQueryFactory->createFromRequest($this->request, 'software test', 'Orange, CA');
        $params = $jobQuery->getParams();
        $this->assertEquals('(software engineer) OR (software developer)', $params['q']);
        $this->assertEquals('Orange, CA', $params['l']);
        $this->assertEquals('newgrad', $params['frl']);
        $this->assertEquals('25', $params['mi']);
        $this->assertEquals(2, $params['pn']);
        $this->assertEquals(14, $params['fdb']);
        $this->assertEquals('dd', $params['sb']);
        $this->assertEquals('full-time', $params['fjt']);
        $this->assertEquals('job_board', $params['fsr']);
        $this->assertEquals('employer', $params['fem']);
        $this->assertEquals(3, $params['fcz']);
        $this->assertEquals(6, $params['fcr']);
        $this->assertEquals('comboxp0', $params['clst']);
        $this->assertEquals(43742, $params['pshid']);
        $this->assertEquals('dailycal.jobamatic.com', $params['jbd']);
        $this->assertEquals(2, $params['ssty']);
        $this->assertEquals('r', $params['cflg']);
        $this->assertEquals('127.0.0.1', $params['clip']);
    }
    
    public function testCreateFromRequestAlias()
    {
        //simulate failed lookup in database
        $this->majorManager->expects($this->once())
            ->method('findMajorByName')
            ->with('software test')
            ->will($this->returnValue(null));
        
        $this->majorAliasManager->expects($this->once())
            ->method('findMajorAliasByName')
            ->with('software test')
            ->will($this->returnValue($this->majorAliasEntity));
        
        $this->majorAliasEntity->expects($this->once())
            ->method('getMajor')
            ->will($this->returnValue($this->majorEntity));
    
        $this->majorEntity->expects($this->once())
            ->method('getJobQuery')
            ->will($this->returnValue('(software engineer) OR (software developer)'));
    
        $this->request->expects($this->once())
            ->method('getClientIp')
            ->will($this->returnValue('127.0.0.1'));
    
        $this->parameterBag->expects($this->once())
            ->method('all')
            ->will($this->returnValue(array())
        );
    
        $jobQuery = $this->jobQueryFactory->createFromRequest($this->request, 'software test', 'Orange, CA');
        $params = $jobQuery->getParams();
        $this->assertEquals('(software engineer) OR (software developer)', $params['q']);
        $this->assertEquals('Orange, CA', $params['l']);
        $this->assertEquals('newgrad', $params['frl']);
        $this->assertEquals(43742, $params['pshid']);
        $this->assertEquals('dailycal.jobamatic.com', $params['jbd']);
        $this->assertEquals(2, $params['ssty']);
        $this->assertEquals('r', $params['cflg']);
        $this->assertEquals('127.0.0.1', $params['clip']);
    }
    
    public function testCreateFromRequestAdvanced()
    {
        $this->majorManager->expects($this->once())
            ->method('findMajorByName')
            ->with('software test')
            ->will($this->returnValue($this->majorEntity));
    
        $this->majorEntity->expects($this->once())
            ->method('getJobQuery')
            ->will($this->returnValue('(software engineer) OR (software developer)'));
    
        $this->request->expects($this->once())
            ->method('getClientIp')
            ->will($this->returnValue('127.0.0.1'));
    
        $this->parameterBag->expects($this->once())
            ->method('all')
            ->will($this->returnValue(array(
                'all' => 'all these terms',
                'exact' => '  testing  extra  spaces  ',
                'atleast' => 'one of these terms',
                'without' => 'none used',
                'title' => 'developer engineer',
                'company' => 'northrop grumman'
            ))
        );
    
        $jobQuery = $this->jobQueryFactory->createFromRequest($this->request, 'software test', 'Orange, CA');
        $params = $jobQuery->getParams();
        $this->assertEquals('(software engineer) OR (software developer)', $params['q']);
        $this->assertEquals('Orange, CA', $params['l']);
        $this->assertEquals('newgrad', $params['frl']);
        $this->assertEquals('all these terms', $params['qa']);
        $this->assertEquals('  testing  extra  spaces  ', $params['qe']);
        $this->assertEquals('one of these terms', $params['qo']);
        $this->assertEquals('none used', $params['qw']);
        $this->assertEquals('developer engineer', $params['t']);
        $this->assertEquals('northrop grumman', $params['c']);
        $this->assertEquals(43742, $params['pshid']);
        $this->assertEquals('dailycal.jobamatic.com', $params['jbd']);
        $this->assertEquals(2, $params['ssty']);
        $this->assertEquals('r', $params['cflg']);
        $this->assertEquals('127.0.0.1', $params['clip']);
    }
    
    public function testCreateFromRequestInvalidInput()
    {
        //simulate failed lookup in database
        $this->majorManager->expects($this->once())
            ->method('findMajorByName')
            ->with('software test')
            ->will($this->returnValue(null));
        
        $this->majorAliasManager->expects($this->once())
            ->method('findMajorAliasByName')
            ->with('software test')
            ->will($this->returnValue(null));
    
        $this->request->expects($this->once())
            ->method('getClientIp')
            ->will($this->returnValue('127.0.0.1'));
    
        $this->parameterBag->expects($this->once())
            ->method('all')
            ->will($this->returnValue(array(
                'dist' => 'exact',
                'page' => '0',
                'date' => 'today',
                'sort' => 'dd',
                'jobtype' => 'part-time',
                'boards' => 'primary',
                'recruiters' => 'recruiterssss',
                'invalid' => 'invalid'
            ))
        );
    
        $jobQuery = $this->jobQueryFactory->createFromRequest($this->request, 'software test', 'everywhere');
        $params = $jobQuery->getParams();
        $this->assertEquals('software test', $params['q']);
        $this->assertEquals('', $params['l']);
        $this->assertEquals('newgrad', $params['frl']);
        $this->assertEquals('exact', $params['mi']);
        $this->assertFalse(array_key_exists('pn', $params));
        $this->assertFalse(array_key_exists('fdb', $params));
        $this->assertEquals('dd', $params['sb']);
        $this->assertEquals('part-time', $params['fjt']);
        $this->assertEquals('primary', $params['fsr']);
        $this->assertFalse(array_key_exists('fem', $params));
        $this->assertFalse(array_key_exists('invalid', $params));
        $this->assertEquals(43742, $params['pshid']);
        $this->assertEquals('dailycal.jobamatic.com', $params['jbd']);
        $this->assertEquals(2, $params['ssty']);
        $this->assertEquals('r', $params['cflg']);
        $this->assertEquals('127.0.0.1', $params['clip']);
    }
    
    public function testCreateFromRequestNoMajor()
    {
        $this->majorManager->expects($this->once())
            ->method('findMajorByName')
            ->with('undeclared')
            ->will($this->returnValue(null));
        
        $this->majorAliasManager->expects($this->once())
            ->method('findMajorAliasByName')
            ->with('undeclared')
            ->will($this->returnValue(null));
    
        $this->request->expects($this->once())
            ->method('getClientIp')
            ->will($this->returnValue('127.0.0.1'));
    
        $this->parameterBag->expects($this->once())
            ->method('all')
            ->will($this->returnValue(array()));
    
        $jobQuery = $this->jobQueryFactory->createFromRequest($this->request, 'undeclared', 'Orange, CA');
        $params = $jobQuery->getParams();
        $this->assertEquals('http://api.simplyhired.com/a/jobs-api/xml-v2/l-Orange%2C+CA/frl-newgrad?cflg=r&clip=127.0.0.1&jbd=dailycal.jobamatic.com&pshid=43742&ssty=2', $jobQuery->getApiUrl());
        $this->assertEquals('', $params['q']);
        $this->assertEquals('Orange, CA', $params['l']);
    }
    
    public function testCreateFromRequestNoLocation()
    {
        //simulate failed lookup in database
        $this->majorManager->expects($this->once())
            ->method('findMajorByName')
            ->with('software test')
            ->will($this->returnValue(null));
        
        $this->majorAliasManager->expects($this->once())
            ->method('findMajorAliasByName')
            ->with('software test')
            ->will($this->returnValue(null));
    
        $this->request->expects($this->once())
            ->method('getClientIp')
            ->will($this->returnValue('127.0.0.1'));
    
        $this->parameterBag->expects($this->once())
            ->method('all')
            ->will($this->returnValue(array()));
    
        $jobQuery = $this->jobQueryFactory->createFromRequest($this->request, 'software test', 'everywhere');
        $params = $jobQuery->getParams();
        $this->assertEquals('http://api.simplyhired.com/a/jobs-api/xml-v2/q-software+test/frl-newgrad?cflg=r&clip=127.0.0.1&jbd=dailycal.jobamatic.com&pshid=43742&ssty=2', $jobQuery->getApiUrl());
        $this->assertEquals('software test', $params['q']);
        $this->assertEquals('', $params['l']);
    }
    
    protected function getMajorManager()
    {
        return $this->getMock('MajoredIn\JobSearchBundle\Model\MajorManagerInterface');
    }
    
    protected function getMajorEntity()
    {
        return $this->getMock('MajoredIn\JobSearchBundle\Model\MajorInterface');
    }
    
    protected function getMajorAliasManager()
    {
        return $this->getMock('MajoredIn\JobSearchBundle\Model\MajorAliasManagerInterface');
    }
    
    protected function getMajorAliasEntity()
    {
        return $this->getMock('MajoredIn\JobSearchBundle\Model\MajorAliasInterface');
    }
    
    protected function getRequest()
    {
        return $this->getMock('Symfony\Component\HttpFoundation\Request');
    }
    
    protected function getParameterBag()
    {
        return $this->getMock('Symfony\Component\HttpFoundation\ParameterBag');
    }
}