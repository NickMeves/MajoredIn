<?php

namespace MajoredIn\JobSearchBundle\Tests\Search;

use MajoredIn\JobSearchBundle\Search\JobApiConnector;
use MajoredIn\JobSearchBundle\Exception\NoResultsException;
use MajoredIn\JobSearchBundle\Exception\InvalidParamException;
use MajoredIn\JobSearchBundle\Exception\LocationRedirectException;

class JobApiConnectorTest extends \PHPUnit_Framework_TestCase
{
    protected $jobApiConnector;
    protected $feedReader;
    protected $cache;
    protected $jobQuery;
    protected $ttl;
    
    public function setUp()
    {
        $this->feedReader = $this->getFeedReader();
        $this->cache = $this->getCache();
        $this->jobQuery = $this->getJobQuery();
        $this->ttl = 300;
        
        $defaultParams = array(
            'mi' => 25,
            'pn' => 1,
            'fdb' => '',
            'sb' => 'rd',
            'fjt' => '',
            'fsr' => '',
            'fem' => '',
            'fcz' => '',
            'fcr' => '',
            'c' => '',
            't' => '',
            'clst' => ''
        );
        
        $this->jobApiConnector = new JobApiConnector($this->feedReader, $this->cache, $defaultParams, new \DateTime('2012-11-15'));
    }
    
    public function testJobApiConnectorInstanceOf()
    {
        $this->assertInstanceOf('MajoredIn\JobSearchBundle\Search\JobApiConnectorInterface', $this->jobApiConnector);
    }
    
    public function testAccessApiUrlFail()
    {
        try {
            $this->jobQuery->expects($this->once())
                ->method('getApiUrl')
                ->will($this->returnValue('http://www.testfail.com'));
            $this->jobQuery->expects($this->once())
                ->method('getParams')
                ->will($this->returnValue(array()));
            $this->feedReader->expects($this->once())
                ->method('readUrl')
                ->with('http://www.testfail.com')
                ->will($this->returnValue(false));
            $this->jobApiConnector->accessApi($this->jobQuery);
        }
        catch (\Exception $e) {
            return;
        }
        
        $this->fail('An expected exception has not been raised.');
    }
    
    public function testAccessApiNoResultsError()
    {
        $this->setExpectedException('\MajoredIn\JobSearchBundle\Exception\NoResultsException');
        
        $xml = <<<XML
<?xml version="1.0" encoding="ISO-8859-1" standalone="no" ?>
<!DOCTYPE shrs SYSTEM "http://api.simplyhired.com/c/jobs-api/html/sr2.dtd">

<sherror>
  <error type="noresults" code="1.1">
  <title></title>
  <subtitle></subtitle>
  <text></text>
  <base></base>
 </error>
</sherror>
XML;
        
        $this->jobQuery->expects($this->once())
            ->method('getApiUrl')
            ->will($this->returnValue('http://www.testnoresults.com'));
        $this->jobQuery->expects($this->once())
            ->method('getParams')
            ->will($this->returnValue(array()));
        $this->feedReader->expects($this->once())
            ->method('readUrl')
            ->with('http://www.testnoresults.com')
            ->will($this->returnValue($xml));
        $this->cache->expects($this->once())
            ->method('contains')
            ->with(md5('http://www.testnoresults.com'))
            ->will($this->returnValue(false));
        $this->cache->expects($this->once())
            ->method('save')
            ->with(md5('http://www.testnoresults.com'), $xml, $this->ttl);
        $this->jobApiConnector->accessApi($this->jobQuery);
    }
    
    public function testAccessApiInvalidParamError()
    {
        $this->setExpectedException('\MajoredIn\JobSearchBundle\Exception\InvalidParamException');
        
        $xml = <<<XML
<?xml version="1.0" encoding="ISO-8859-1" standalone="no" ?>
<!DOCTYPE shrs SYSTEM "http://api.simplyhired.com/c/jobs-api/html/sr2.dtd">

 <sherror>
  <error type="invalidparam" code="">
  <title></title>
  <subtitle></subtitle>
  <text></text>
  <base></base>
 </error>
</sherror>
XML;
        
        $this->jobQuery->expects($this->once())
            ->method('getApiUrl')
            ->will($this->returnValue('http://www.testinvalidparam.com'));
        $this->jobQuery->expects($this->once())
            ->method('getParams')
            ->will($this->returnValue(array()));
        $this->feedReader->expects($this->once())
            ->method('readUrl')
            ->with('http://www.testinvalidparam.com')
            ->will($this->returnValue($xml));
        $this->cache->expects($this->once())
            ->method('contains')
            ->with(md5('http://www.testinvalidparam.com'))
            ->will($this->returnValue(false));
        $this->cache->expects($this->once())
            ->method('save')
            ->with(md5('http://www.testinvalidparam.com'), $xml, $this->ttl);
        $this->jobApiConnector->accessApi($this->jobQuery);
    }
    
    public function testAccessApiInstructionError()
    {
        $xml = <<<XML
<?xml version="1.0" encoding="ISO-8859-1" standalone="no" ?>
<!DOCTYPE shrs SYSTEM "http://api.simplyhired.com/c/jobs-api/html/sr2.dtd">

 <sherror>
  <error type="instruction" code="1.5">
  <title></title>
  <subtitle></subtitle>
  <text></text>
  <base></base>
        <option url="/a/jobs-api/xml_v2/l-Springfield%2C+MO">Springfield, MO</option>
     <option url="/a/jobs-api/xml_v2/l-Springfield%2C+NJ">Springfield, NJ</option>
     <option url="/a/jobs-api/xml_v2/l-Springfield%2C+VA">Springfield, VA</option>
     <option url="/a/jobs-api/xml_v2/l-Springfield%2C+PA">Springfield, PA</option>
     <option url="/a/jobs-api/xml_v2/l-Springfield%2C+MA">Springfield, MA</option>
     <option url="/a/jobs-api/xml_v2/l-Springfield%2C+NE">Springfield, NE</option>
     <option url="/a/jobs-api/xml_v2/l-Springfield%2C+OH">Springfield, OH</option>
     <option url="/a/jobs-api/xml_v2/l-Springfield%2C+WI">Springfield, WI</option>
     <option url="/a/jobs-api/xml_v2/l-Springfield%2C+GA">Springfield, GA</option>
     <option url="/a/jobs-api/xml_v2/l-Springfield%2C+MI">Springfield, MI</option>
     <option url="/a/jobs-api/xml_v2/l-Springfield%2C+TN">Springfield, TN</option>
     <option url="/a/jobs-api/xml_v2/l-Springfield%2C+OR">Springfield, OR</option>
     <option url="/a/jobs-api/xml_v2/l-Springfield%2C+IL">Springfield, IL</option>
     <option url="/a/jobs-api/xml_v2/l-Springfield%2C+NH">Springfield, NH</option>
     <option url="/a/jobs-api/xml_v2/l-Springfield%2C+VT">Springfield, VT</option>
     <option url="/a/jobs-api/xml_v2/l-Springfield%2C+LA">Springfield, LA</option>
     <option url="/a/jobs-api/xml_v2/l-Springfield%2C+WV">Springfield, WV</option>
     <option url="/a/jobs-api/xml_v2/l-Springfield%2C+SC">Springfield, SC</option>
     <option url="/a/jobs-api/xml_v2/l-Springfield%2C+TX">Springfield, TX</option>
     <option url="/a/jobs-api/xml_v2/l-Springfield%2C+KY">Springfield, KY</option>
     <option url="/a/jobs-api/xml_v2/l-Springfield%2C+ID">Springfield, ID</option>
     <option url="/a/jobs-api/xml_v2/l-Springfield%2C+AR">Springfield, AR</option>
     <option url="/a/jobs-api/xml_v2/l-Springfield%2C+MN">Springfield, MN</option>
     <option url="/a/jobs-api/xml_v2/l-Springfield%2C+ME">Springfield, ME</option>
     <option url="/a/jobs-api/xml_v2/l-Springfield%2C+SD">Springfield, SD</option>
     <option url="/a/jobs-api/xml_v2/l-Springfield%2C+CO">Springfield, CO</option>
  </error>
</sherror>
XML;

        try {
            $this->jobQuery->expects($this->once())
                ->method('getApiUrl')
                ->will($this->returnValue('http://www.testinstruction.com'));
            $this->jobQuery->expects($this->once())
                ->method('getParams')
                ->will($this->returnValue(array()));
            $this->feedReader->expects($this->once())
                ->method('readUrl')
                ->with('http://www.testinstruction.com')
                ->will($this->returnValue($xml));
            $this->cache->expects($this->once())
                ->method('contains')
                ->with(md5('http://www.testinstruction.com'))
                ->will($this->returnValue(false));
            $this->cache->expects($this->once())
                ->method('save')
                ->with(md5('http://www.testinstruction.com'), $xml, $this->ttl);
            $this->jobApiConnector->accessApi($this->jobQuery);
        }
        catch (LocationRedirectException $e) {
            $this->assertEquals('Springfield, MO', $e->getLocation());
            return;
        }
        
        $this->fail('An expected LocationRedirectException has not been raised.');
    }
    
    public function testAccessApiUnknownError()
    {   
        $xml = <<<XML
<?xml version="1.0" encoding="ISO-8859-1" standalone="no" ?>
<!DOCTYPE shrs SYSTEM "http://api.simplyhired.com/c/jobs-api/html/sr2.dtd">
        
<sherror>
  <error type="unknown_type" code="1.1">
  <title></title>
  <subtitle></subtitle>
  <text></text>
  <base></base>
 </error>
</sherror>
XML;
        try {
            $this->jobQuery->expects($this->once())
                ->method('getApiUrl')
                ->will($this->returnValue('http://www.testunknowntype.com'));
            $this->jobQuery->expects($this->once())
                ->method('getParams')
                ->will($this->returnValue(array()));
            $this->feedReader->expects($this->once())
                ->method('readUrl')
                ->with('http://www.testunknowntype.com')
                ->will($this->returnValue($xml));
            $this->cache->expects($this->once())
                ->method('contains')
                ->with(md5('http://www.testunknowntype.com'))
                ->will($this->returnValue(false));
            $this->cache->expects($this->once())
                ->method('save')
                ->with(md5('http://www.testunknowntype.com'), $xml, $this->ttl);
            $this->jobApiConnector->accessApi($this->jobQuery);
        }
        catch (\Exception $e) {
            return;
        }
        
        $this->fail('An expected exception has not been raised.');
    }
    
    public function testAccessApiDefaultParams()
    {
        $xml = $this->getLongXml();
        
        $this->jobQuery->expects($this->once())
            ->method('getApiUrl')
            ->will($this->returnValue('http://www.testdefaultparams.com'));
        $this->jobQuery->expects($this->once())
            ->method('getParams')
            ->will($this->returnValue(array()));
        $this->feedReader->expects($this->once())
            ->method('readUrl')
            ->with('http://www.testdefaultparams.com')
            ->will($this->returnValue($xml));
        $this->cache->expects($this->once())
            ->method('contains')
            ->with(md5('http://www.testdefaultparams.com'))
            ->will($this->returnValue(false));
        $this->cache->expects($this->once())
            ->method('save')
            ->with(md5('http://www.testdefaultparams.com'), $xml, $this->ttl);
        $jobResults = $this->jobApiConnector->accessApi($this->jobQuery);
        
        $this->assertEquals(664, $jobResults->getTotalViewable());
        $this->assertEquals(119047, $jobResults->getTotalResults());
        $this->assertEquals(67, $jobResults->getMaxPage());
        $this->assertEquals(25, $jobResults->getDistance());
        $this->assertEquals(1, $jobResults->getCurrentPage());
        $this->assertEquals('', $jobResults->getDatePosted());
        $this->assertEquals('rd', $jobResults->getSortBy());
        $this->assertEquals('', $jobResults->getJobType());
        $this->assertEquals('', $jobResults->getJobBoards());
        $this->assertEquals('', $jobResults->getRecruiters());
        $this->assertEquals('', $jobResults->getCompanySize());
        $this->assertEquals('', $jobResults->getCompanyRevenue());
        $this->assertEquals('', $jobResults->getCompany());
        $this->assertEquals('', $jobResults->getTitle());
        $this->assertEquals('', $jobResults->getHidden());
        
        $locations = $jobResults->getLocations();
        $this->assertEquals('Houston, TX', $locations[0]);
        $this->assertEquals('Oklahoma', $locations[1]);
        $titles = $jobResults->getTitles();
        $this->assertEquals('MORTGAGE LOAN PROCESSOR', $titles[0]);
        $companies = $jobResults->getCompanies();
        $this->assertEquals('Adecco', $companies[0]);
        
        $jobListings = $jobResults->getJobListings();
        if (!is_array($jobListings)) {
            $this->fail('jobListings should be an array');
        }
        $this->assertEquals('Mortgage Quality Control Underwriter Job', $jobListings[0]->getTitle());
        $this->assertEquals('Capital One', $jobListings[0]->getCompany());
        $this->assertEquals('http://api.simplyhired.com/a/job-details/view/cparm-cF9pZD00Mzc0MiZ6b25lPTYmaXA9JmNvdW50PTUwJnN0YW1wPTIwMTItMTEtMDQgMTY6NTE6NTMmcHVibGlzaGVyX2NoYW5uZWxfaWRzPSZhX2lkPTQwMDkyJmNfaWQ9MTUwMDcmY3BjPTAuMzEmcG9zPTEmaGFzaD05ZGFjNmJjNzM3MGE4YmU4NDRiYjEwNjJmNDMxOWU5Yw%3D%3D%3B6aabc0821fbf0dbfd91879abbb3c0601/jobkey-17585.2118857A2/rid-jamgnedemnqelrodlshoeqvoijxzenca/pub_id-43742/cjp-0/hits-119047', $jobListings[0]->getUrl());
        $this->assertEquals('sponsored', $jobListings[0]->getType());
        $this->assertEquals('Wilmington, DE', $jobListings[0]->getLocation());
        $this->assertEquals('19 days', $jobListings[0]->getAge());
        $this->assertEquals('- Run/Review/investigate all information contained in mortgage loan file for accuracy ... - Minimum of 3 years related work experience in Mortgage Quality Control or Mortgage Underwriting -...', $jobListings[0]->getExcerpt());

        $this->assertEquals('MORTGAGE CLERK (DISCLOSURE)', $jobListings[9]->getTitle());
        $this->assertEquals('Adecco', $jobListings[9]->getCompany());
        $this->assertEquals('http://api.simplyhired.com/a/job-details/view/cparm-cF9pZD00Mzc0MiZ6b25lPTYmaXA9JmNvdW50PTUwJnN0YW1wPTIwMTItMTEtMDQgMTY6NTE6NTMmcHVibGlzaGVyX2NoYW5uZWxfaWRzPSZhX2lkPTg0ODMmY19pZD0xNDc4OCZjcGM9MC4yMSZwb3M9MTAmaGFzaD1mYTM5MGNjNzk1NGQ1ZDI3N2IzNjY2ZDk0MjQxNzllZA%3D%3D%3B9cbc6fe70b3da4050eaec8466c955b7f/jobkey-76521d29539f25b3357abfaca219ddc4381e28/rid-jamgnedemnqelrodlshoeqvoijxzenca/pub_id-43742/cjp-9/hits-119047', $jobListings[9]->getUrl());
        $this->assertEquals('sponsored', $jobListings[9]->getType());
        $this->assertEquals('Olean, NY', $jobListings[9]->getLocation());
        $this->assertEquals('error', $jobListings[9]->getAge());
        $this->assertEquals('This candidate will be working in the mortgage dept. Reviews applications and supporting qualifying documents submitted ... clerical errors.Enters initial information into EasyLender Mortgage to create an electronic Loan File for all consumer...', $jobListings[9]->getExcerpt());
    }
    
    public function testAccessApiJobQueryParams()
    {
        $queryParams = array(
                'mi' => 50,
                'pn' => 3,
                'fdb' => 30,
                'sb' => 'dd',
                'fjt' => 'internship',
                'fsr' => 'primary',
                'fem' => 'employer',
                'fcz' => 2,
                'fcr' => 3,
                'c' => 'northrop grumman',
                't' => 'developer engineer',
                'clst' => 'comboxp0'
        );
        
        $xml = $this->getLongXml();
        
        $this->jobQuery->expects($this->once())
            ->method('getApiUrl')
            ->will($this->returnValue('http://www.testqueryparams.com'));
        $this->jobQuery->expects($this->once())
            ->method('getParams')
            ->will($this->returnValue($queryParams));
        $this->feedReader->expects($this->once())
            ->method('readUrl')
            ->with('http://www.testqueryparams.com')
            ->will($this->returnValue($xml));
        $this->cache->expects($this->once())
            ->method('contains')
            ->with(md5('http://www.testqueryparams.com'))
            ->will($this->returnValue(false));
        $this->cache->expects($this->once())
            ->method('save')
            ->with(md5('http://www.testqueryparams.com'), $xml, $this->ttl);
        $jobResults = $this->jobApiConnector->accessApi($this->jobQuery);
        
        $this->assertEquals(664, $jobResults->getTotalViewable());
        $this->assertEquals(119047, $jobResults->getTotalResults());
        $this->assertEquals(67, $jobResults->getMaxPage());
        $this->assertEquals(50, $jobResults->getDistance());
        $this->assertEquals(3, $jobResults->getCurrentPage());
        $this->assertEquals('30', $jobResults->getDatePosted());
        $this->assertEquals('dd', $jobResults->getSortBy());
        $this->assertEquals('internship', $jobResults->getJobType());
        $this->assertEquals('primary', $jobResults->getJobBoards());
        $this->assertEquals('employer', $jobResults->getRecruiters());
        $this->assertEquals(2, $jobResults->getCompanySize());
        $this->assertEquals(3, $jobResults->getCompanyRevenue());
        $this->assertEquals('northrop grumman', $jobResults->getCompany());
        $this->assertEquals('developer engineer', $jobResults->getTitle());
        $this->assertEquals('comboxp0', $jobResults->getHidden());
        
        $locations = $jobResults->getLocations();
        $this->assertEquals('Houston, TX', $locations[0]);
        $this->assertEquals('Oklahoma', $locations[1]);
        $titles = $jobResults->getTitles();
        $this->assertEquals('MORTGAGE LOAN PROCESSOR', $titles[0]);
        $companies = $jobResults->getCompanies();
        $this->assertEquals('Adecco', $companies[0]);
        
        $jobListings = $jobResults->getJobListings();
        if (!is_array($jobListings)) {
            $this->fail('jobListings should be an array');
        }
        $this->assertEquals('Mortgage Quality Control Underwriter Job', $jobListings[0]->getTitle());
        $this->assertEquals('Capital One', $jobListings[0]->getCompany());
        $this->assertEquals('http://api.simplyhired.com/a/job-details/view/cparm-cF9pZD00Mzc0MiZ6b25lPTYmaXA9JmNvdW50PTUwJnN0YW1wPTIwMTItMTEtMDQgMTY6NTE6NTMmcHVibGlzaGVyX2NoYW5uZWxfaWRzPSZhX2lkPTQwMDkyJmNfaWQ9MTUwMDcmY3BjPTAuMzEmcG9zPTEmaGFzaD05ZGFjNmJjNzM3MGE4YmU4NDRiYjEwNjJmNDMxOWU5Yw%3D%3D%3B6aabc0821fbf0dbfd91879abbb3c0601/jobkey-17585.2118857A2/rid-jamgnedemnqelrodlshoeqvoijxzenca/pub_id-43742/cjp-0/hits-119047', $jobListings[0]->getUrl());
        $this->assertEquals('sponsored', $jobListings[0]->getType());
        $this->assertEquals('Wilmington, DE', $jobListings[0]->getLocation());
        $this->assertEquals('19 days', $jobListings[0]->getAge());
        $this->assertEquals('- Run/Review/investigate all information contained in mortgage loan file for accuracy ... - Minimum of 3 years related work experience in Mortgage Quality Control or Mortgage Underwriting -...', $jobListings[0]->getExcerpt());
        
        $this->assertEquals('MORTGAGE CLERK (DISCLOSURE)', $jobListings[9]->getTitle());
        $this->assertEquals('Adecco', $jobListings[9]->getCompany());
        $this->assertEquals('http://api.simplyhired.com/a/job-details/view/cparm-cF9pZD00Mzc0MiZ6b25lPTYmaXA9JmNvdW50PTUwJnN0YW1wPTIwMTItMTEtMDQgMTY6NTE6NTMmcHVibGlzaGVyX2NoYW5uZWxfaWRzPSZhX2lkPTg0ODMmY19pZD0xNDc4OCZjcGM9MC4yMSZwb3M9MTAmaGFzaD1mYTM5MGNjNzk1NGQ1ZDI3N2IzNjY2ZDk0MjQxNzllZA%3D%3D%3B9cbc6fe70b3da4050eaec8466c955b7f/jobkey-76521d29539f25b3357abfaca219ddc4381e28/rid-jamgnedemnqelrodlshoeqvoijxzenca/pub_id-43742/cjp-9/hits-119047', $jobListings[9]->getUrl());
        $this->assertEquals('sponsored', $jobListings[9]->getType());
        $this->assertEquals('Olean, NY', $jobListings[9]->getLocation());
        $this->assertEquals('error', $jobListings[9]->getAge());
        $this->assertEquals('This candidate will be working in the mortgage dept. Reviews applications and supporting qualifying documents submitted ... clerical errors.Enters initial information into EasyLender Mortgage to create an electronic Loan File for all consumer...', $jobListings[9]->getExcerpt());
    }
    
    public function testAccessApiCached()
    {
        $queryParams = array(
                'mi' => 50,
                'pn' => 3,
                'fdb' => 30,
                'sb' => 'dd',
                'fjt' => 'internship',
                'fsr' => 'primary',
                'fem' => 'employer',
                'fcz' => 2,
                'fcr' => 3,
                'c' => 'northrop grumman',
                't' => 'developer engineer',
                'clst' => 'comboxp0'
        );
    
        $xml = $this->getLongXml();
    
        $this->jobQuery->expects($this->once())
            ->method('getApiUrl')
            ->will($this->returnValue('http://www.testqueryparams.com'));
        $this->jobQuery->expects($this->once())
            ->method('getParams')
            ->will($this->returnValue($queryParams));
        $this->cache->expects($this->once())
            ->method('contains')
            ->with(md5('http://www.testqueryparams.com'))
            ->will($this->returnValue(true));
        $this->cache->expects($this->once())
            ->method('fetch')
            ->with(md5('http://www.testqueryparams.com'))
            ->will($this->returnValue($xml));
        $jobResults = $this->jobApiConnector->accessApi($this->jobQuery);
    
        $this->assertEquals(664, $jobResults->getTotalViewable());
        $this->assertEquals(119047, $jobResults->getTotalResults());
        $this->assertEquals(67, $jobResults->getMaxPage());
        $this->assertEquals(50, $jobResults->getDistance());
        $this->assertEquals(3, $jobResults->getCurrentPage());
        $this->assertEquals('30', $jobResults->getDatePosted());
        $this->assertEquals('dd', $jobResults->getSortBy());
        $this->assertEquals('internship', $jobResults->getJobType());
        $this->assertEquals('primary', $jobResults->getJobBoards());
        $this->assertEquals('employer', $jobResults->getRecruiters());
        $this->assertEquals(2, $jobResults->getCompanySize());
        $this->assertEquals(3, $jobResults->getCompanyRevenue());
        $this->assertEquals('northrop grumman', $jobResults->getCompany());
        $this->assertEquals('developer engineer', $jobResults->getTitle());
        $this->assertEquals('comboxp0', $jobResults->getHidden());
    
        $locations = $jobResults->getLocations();
        $this->assertEquals('Houston, TX', $locations[0]);
        $this->assertEquals('Oklahoma', $locations[1]);
        $titles = $jobResults->getTitles();
        $this->assertEquals('MORTGAGE LOAN PROCESSOR', $titles[0]);
        $companies = $jobResults->getCompanies();
        $this->assertEquals('Adecco', $companies[0]);
    
        $jobListings = $jobResults->getJobListings();
        if (!is_array($jobListings)) {
            $this->fail('jobListings should be an array');
        }
        $this->assertEquals('Mortgage Quality Control Underwriter Job', $jobListings[0]->getTitle());
        $this->assertEquals('Capital One', $jobListings[0]->getCompany());
        $this->assertEquals('http://api.simplyhired.com/a/job-details/view/cparm-cF9pZD00Mzc0MiZ6b25lPTYmaXA9JmNvdW50PTUwJnN0YW1wPTIwMTItMTEtMDQgMTY6NTE6NTMmcHVibGlzaGVyX2NoYW5uZWxfaWRzPSZhX2lkPTQwMDkyJmNfaWQ9MTUwMDcmY3BjPTAuMzEmcG9zPTEmaGFzaD05ZGFjNmJjNzM3MGE4YmU4NDRiYjEwNjJmNDMxOWU5Yw%3D%3D%3B6aabc0821fbf0dbfd91879abbb3c0601/jobkey-17585.2118857A2/rid-jamgnedemnqelrodlshoeqvoijxzenca/pub_id-43742/cjp-0/hits-119047', $jobListings[0]->getUrl());
        $this->assertEquals('sponsored', $jobListings[0]->getType());
        $this->assertEquals('Wilmington, DE', $jobListings[0]->getLocation());
        $this->assertEquals('19 days', $jobListings[0]->getAge());
        $this->assertEquals('- Run/Review/investigate all information contained in mortgage loan file for accuracy ... - Minimum of 3 years related work experience in Mortgage Quality Control or Mortgage Underwriting -...', $jobListings[0]->getExcerpt());
    
        $this->assertEquals('MORTGAGE CLERK (DISCLOSURE)', $jobListings[9]->getTitle());
        $this->assertEquals('Adecco', $jobListings[9]->getCompany());
        $this->assertEquals('http://api.simplyhired.com/a/job-details/view/cparm-cF9pZD00Mzc0MiZ6b25lPTYmaXA9JmNvdW50PTUwJnN0YW1wPTIwMTItMTEtMDQgMTY6NTE6NTMmcHVibGlzaGVyX2NoYW5uZWxfaWRzPSZhX2lkPTg0ODMmY19pZD0xNDc4OCZjcGM9MC4yMSZwb3M9MTAmaGFzaD1mYTM5MGNjNzk1NGQ1ZDI3N2IzNjY2ZDk0MjQxNzllZA%3D%3D%3B9cbc6fe70b3da4050eaec8466c955b7f/jobkey-76521d29539f25b3357abfaca219ddc4381e28/rid-jamgnedemnqelrodlshoeqvoijxzenca/pub_id-43742/cjp-9/hits-119047', $jobListings[9]->getUrl());
        $this->assertEquals('sponsored', $jobListings[9]->getType());
        $this->assertEquals('Olean, NY', $jobListings[9]->getLocation());
        $this->assertEquals('error', $jobListings[9]->getAge());
        $this->assertEquals('This candidate will be working in the mortgage dept. Reviews applications and supporting qualifying documents submitted ... clerical errors.Enters initial information into EasyLender Mortgage to create an electronic Loan File for all consumer...', $jobListings[9]->getExcerpt());
    }

    public function testAccessApiNoResultSetTag()
    {
        $this->setExpectedException('\MajoredIn\JobSearchBundle\Exception\NoResultsException');
        
        $xml = <<<XML
<?xml version="1.0" encoding="ISO-8859-1" standalone="no" ?>
<!DOCTYPE shrs SYSTEM "http://api.simplyhired.com/c/jobs-api/html/sr2.dtd">
        
<shrs>
<rq url="http://api.simplyhired.com/a/jobs-api/xml_v2/q-mortgage">
  <t>Mortgage Jobs</t>
  <dt>2012-11-05T00:51:53Z</dt>
  <si>0</si>
  <rpd>10</rpd>
  <tr>0</tr>
  <tv>0</tv>
  <em url=""/>
<h>
  <kw pos="1"/>
</h>
</rq>
<pjl>
</pjl>
</shrs>    
XML;
        
        $this->jobQuery->expects($this->once())
            ->method('getApiUrl')
            ->will($this->returnValue('http://www.testnoresults.com'));
        $this->jobQuery->expects($this->once())
            ->method('getParams')
            ->will($this->returnValue(array()));
        $this->feedReader->expects($this->once())
            ->method('readUrl')
            ->with('http://www.testnoresults.com')
            ->will($this->returnValue($xml));
        $this->cache->expects($this->once())
            ->method('contains')
            ->with(md5('http://www.testnoresults.com'))
            ->will($this->returnValue(false));
        $this->cache->expects($this->once())
            ->method('save')
            ->with(md5('http://www.testnoresults.com'), $xml, $this->ttl);
        $this->jobApiConnector->accessApi($this->jobQuery);
    }
    
    public function testAccessApiEmptyResultSetTag()
    {
        $this->setExpectedException('\MajoredIn\JobSearchBundle\Exception\NoResultsException');
        
        $xml = <<<XML
<?xml version="1.0" encoding="ISO-8859-1" standalone="no" ?>
<!DOCTYPE shrs SYSTEM "http://api.simplyhired.com/c/jobs-api/html/sr2.dtd">
        
<shrs>
<rq url="http://api.simplyhired.com/a/jobs-api/xml_v2/q-mortgage">
  <t>Mortgage Jobs</t>
  <dt>2012-11-05T00:51:53Z</dt>
  <si>0</si>
  <rpd>10</rpd>
  <tr>0</tr>
  <tv>0</tv>
  <em url=""/>
<h>
  <kw pos="1"/>
</h>
</rq>
<rs>
</rs>
<pjl>
</pjl>
</shrs>
XML;
        
        $this->jobQuery->expects($this->once())
            ->method('getApiUrl')
            ->will($this->returnValue('http://www.testnoresults.com'));
        $this->jobQuery->expects($this->once())
            ->method('getParams')
            ->will($this->returnValue(array()));
        $this->feedReader->expects($this->once())
            ->method('readUrl')
            ->with('http://www.testnoresults.com')
            ->will($this->returnValue($xml));
        $this->cache->expects($this->once())
            ->method('contains')
            ->with(md5('http://www.testnoresults.com'))
            ->will($this->returnValue(false));
        $this->cache->expects($this->once())
            ->method('save')
            ->with(md5('http://www.testnoresults.com'), $xml, $this->ttl);
        $this->jobApiConnector->accessApi($this->jobQuery);
    }
    
    protected function getFeedReader()
    {
        return $this->getMock('MajoredIn\JobSearchBundle\Util\FeedReaderInterface');
    }
    
    protected function getCache()
    {
        return $this->getMock('MajoredIn\JobSearchBundle\Util\CacheInterface');
    }
    
    protected function getJobQuery()
    {
        return $this->getMock('MajoredIn\JobSearchBundle\Search\JobQueryInterface');
    }
    
    protected function getLongXml()
    {
        $xml = <<<XML
<?xml version="1.0" encoding="ISO-8859-1" standalone="no" ?>
<!DOCTYPE shrs SYSTEM "http://api.simplyhired.com/c/jobs-api/html/sr2.dtd">
        
<shrs>
<rq url="http://api.simplyhired.com/a/jobs-api/xml_v2/q-mortgage">
  <t>Mortgage Jobs</t>
  <dt>2012-11-05T00:51:53Z</dt>
  <si>0</si>
  <rpd>10</rpd>
  <tr>119047</tr>
  <tv>664</tv>
  <em url=""/>
<h>
  <kw pos="1"/>
</h>
</rq>
<rs>
<r>
  <jt>Mortgage Quality Control Underwriter Job</jt>
  <cn url="">Capital One</cn>
  <src url="http://api.simplyhired.com/a/job-details/view/cparm-cF9pZD00Mzc0MiZ6b25lPTYmaXA9JmNvdW50PTUwJnN0YW1wPTIwMTItMTEtMDQgMTY6NTE6NTMmcHVibGlzaGVyX2NoYW5uZWxfaWRzPSZhX2lkPTQwMDkyJmNfaWQ9MTUwMDcmY3BjPTAuMzEmcG9zPTEmaGFzaD05ZGFjNmJjNzM3MGE4YmU4NDRiYjEwNjJmNDMxOWU5Yw%3D%3D%3B6aabc0821fbf0dbfd91879abbb3c0601/jobkey-17585.2118857A2/rid-jamgnedemnqelrodlshoeqvoijxzenca/pub_id-43742/cjp-0/hits-119047">Capital One</src>
  <ty>sponsored</ty>
  <loc cty="Wilmington" st="DE" postal="19801" county="" region="" country="US">Wilmington, DE</loc>
  <ls>2012-10-31T05:16:00Z</ls>
  <dp>2012-10-27T05:36:48Z</dp>
  <e>- Run/Review/investigate all information contained in mortgage loan file for accuracy ... - Minimum of 3 years related work experience in Mortgage Quality Control or Mortgage Underwriting -...</e>
</r>
<r>
  <jt>Mld Mortgage Rep</jt>
  <cn url="">Union Bank</cn>
  <src url="http://api.simplyhired.com/a/job-details/view/cparm-cF9pZD00Mzc0MiZ6b25lPTYmaXA9JmNvdW50PTUwJnN0YW1wPTIwMTItMTEtMDQgMTY6NTE6NTMmcHVibGlzaGVyX2NoYW5uZWxfaWRzPSZhX2lkPTExNzA3JmNfaWQ9NjcxNSZjcGM9MC4zJnBvcz0yJmhhc2g9YjdmOGMxMGY5MWM3OWY3MGI0MWVmYTgxNzkxZDhjMTk%3D%3Ba0ec3e4117d8d2b498e27e773189c754/jobkey-5aba943a7c5d3d35a45c583ce826bdb48cd51860/rid-jamgnedemnqelrodlshoeqvoijxzenca/pub_id-43742/cjp-1/hits-119047">Union Bank</src>
  <ty>sponsored</ty>
  <loc cty="Everett" st="WA" postal="98207" county="" region="" country="US">Everett, WA</loc>
  <ls>2012-10-30T17:42:45Z</ls>
  <dp>2012-10-30T17:42:45Z</dp>
  <e>- Consultative inbound and outbound phone selling of mortgage loan products including determining needs and ... for employment who will be engaged in residential loan mortgage originations (as defined by the SAFE Act) must...</e>
</r>
<r>
  <jt>MORTGAGE LOAN PROCESSOR</jt>
  <cn url="">Adecco</cn>
  <src url="http://api.simplyhired.com/a/job-details/view/cparm-cF9pZD00Mzc0MiZ6b25lPTYmaXA9JmNvdW50PTUwJnN0YW1wPTIwMTItMTEtMDQgMTY6NTE6NTMmcHVibGlzaGVyX2NoYW5uZWxfaWRzPSZhX2lkPTg0ODMmY19pZD0xNDc4OCZjcGM9MC4yMSZwb3M9MyZoYXNoPWZhMzkwY2M3OTU0ZDVkMjc3YjM2NjZkOTQyNDE3OWVk%3B4a97e5e3a8ec66ea7b3954e141884908/jobkey-748dc31bfc4af7658df22d21f17d19bfe5a39e14/rid-jamgnedemnqelrodlshoeqvoijxzenca/pub_id-43742/cjp-2/hits-119047">Adecco</src>
  <ty>sponsored</ty>
  <loc cty="Houston" st="TX" postal="77009" county="" region="" country="US">Houston, TX</loc>
  <ls>2012-10-25T18:33:53Z</ls>
  <dp>2012-10-25T18:33:53Z</dp>
  <e>with locations from coast to coast, is searching for Mortgage Loan Processors. The Loan Processor is a contract ... below Apply Now!You must have 3 - 5 years of experience of mortgage experience (processors, origination, files,...</e>
</r>
<r>
  <jt>Mortgage Banker (Lawton)</jt>
  <cn url="">Bank of Oklahoma</cn>
  <src url="http://api.simplyhired.com/a/job-details/view/cparm-cF9pZD00Mzc0MiZ6b25lPTYmaXA9JmNvdW50PTUwJnN0YW1wPTIwMTItMTEtMDQgMTY6NTE6NTMmcHVibGlzaGVyX2NoYW5uZWxfaWRzPSZhX2lkPTIyMjk2JmNfaWQ9MTAxNjkmY3BjPTAuMTEmcG9zPTQmaGFzaD0xNmVlZDNlN2QxZDZiMGIyZWU5YmNlODVlYTYyYjE3MA%3D%3D%3B179e0e51e754be1ce47eddee0bd5b2c8/jobkey-15fdaa4c209a482d765af3e553e3acdb29e4a350/rid-jamgnedemnqelrodlshoeqvoijxzenca/pub_id-43742/cjp-3/hits-119047">Bank of Oklahoma</src>
  <ty>sponsored</ty>
  <loc cty="" st="OK" postal="" county="" region="" country="US">Oklahoma</loc>
  <ls>2012-11-03T14:26:32Z</ls>
  <dp>2012-11-03T14:26:32Z</dp>
  <e>and consumer banking, investment and trust services, mortgage origination and servicing, and an electronic funds ... will be responsible for representing a full range of mortgage financing, as well as generating mortgage and...</e>
</r>
<r>
  <jt>MORTGAGE LOAN PROCESSOR</jt>
  <cn url="">Bank of Texas</cn>
  <src url="http://api.simplyhired.com/a/job-details/view/cparm-cF9pZD00Mzc0MiZ6b25lPTYmaXA9JmNvdW50PTUwJnN0YW1wPTIwMTItMTEtMDQgMTY6NTE6NTMmcHVibGlzaGVyX2NoYW5uZWxfaWRzPSZhX2lkPTIyMjk2JmNfaWQ9MTAxNzAmY3BjPTAuMTEmcG9zPTUmaGFzaD05N2RhMTMzM2FiMjA2MDQ2MDcwMTExYWQ4NmZkZDRmZg%3D%3D%3B27f4dd6e7fda182f0b8c16c3597511b9/jobkey-ccd25e29baa475b0584c582ebd569acf39cc6634/rid-jamgnedemnqelrodlshoeqvoijxzenca/pub_id-43742/cjp-4/hits-119047">Bank of Texas</src>
  <ty>sponsored</ty>
  <loc cty="Houston" st="TX" postal="77009" county="" region="" country="US">Houston, TX</loc>
  <ls>2012-11-04T12:02:19Z</ls>
  <dp>2012-11-04T12:02:19Z</dp>
  <e>our team as a Mortgage Loan Processor in Houston, TX!
        
The Mortgage Loan Processor is primarily responsible for ... FHLMC, FNMA and private investor)
In-depth knowledge of mortgage loan underwriting criteria
Ability to perform...</e>
</r>
<r>
  <jt>Mortgage Loan Officer Job</jt>
  <cn url="">Atterro</cn>
  <src url="http://api.simplyhired.com/a/job-details/view/cparm-cF9pZD00Mzc0MiZ6b25lPTYmaXA9JmNvdW50PTUwJnN0YW1wPTIwMTItMTEtMDQgMTY6NTE6NTMmcHVibGlzaGVyX2NoYW5uZWxfaWRzPSZhX2lkPTE4ODQ3JmNfaWQ9MTM5OTAmY3BjPTAuMSZwb3M9NiZoYXNoPThlN2Y1YTc5NTA3MTUwOWYzZDYwM2E3YjZkZjM3ODhj%3B2b33f49d1003e6bebecd2c3ef78f3b02/jobkey-15313.2189956A1/rid-jamgnedemnqelrodlshoeqvoijxzenca/pub_id-43742/cjp-5/hits-119047">Atterro</src>
  <ty>sponsored</ty>
  <loc cty="Houston" st="TX" postal="77001" county="" region="" country="US">Houston, TX</loc>
  <ls>2012-11-01T19:36:48Z</ls>
  <dp>2012-11-01T19:36:48Z</dp>
  <e>Well known Mortgage Home Loan Company located in the Galleria area is looking for qualified Loan Officers. Typical Duties/Responsibilities include: Taking leads from bank branches and call center Generate/self source new lead from book of...</e>
</r>
<r>
  <jt>MORTGAGE SUPPORT ASSISTANT II</jt>
  <cn url="">Adecco</cn>
  <src url="http://api.simplyhired.com/a/job-details/view/cparm-cF9pZD00Mzc0MiZ6b25lPTYmaXA9JmNvdW50PTUwJnN0YW1wPTIwMTItMTEtMDQgMTY6NTE6NTMmcHVibGlzaGVyX2NoYW5uZWxfaWRzPSZhX2lkPTg0ODMmY19pZD0xNDc4OCZjcGM9MC4yMSZwb3M9NyZoYXNoPWZhMzkwY2M3OTU0ZDVkMjc3YjM2NjZkOTQyNDE3OWVk%3B5de848f1f50926d1eabac0ffe9ad56af/jobkey-cad2fb10fae7df612daed80dc2628cc4780c926/rid-jamgnedemnqelrodlshoeqvoijxzenca/pub_id-43742/cjp-6/hits-119047">Adecco</src>
  <ty>sponsored</ty>
  <loc cty="Plano" st="TX" postal="75026" county="" region="" country="US">Plano, TX</loc>
  <ls>2012-10-25T21:41:59Z</ls>
  <dp>2012-10-25T21:41:59Z</dp>
  <e>Center of Excellence has an immediate opening for a Mortgage Support Assistant on a 12 month opportunity with a ... Assistant II Job ID: 266942Pay: $17/hrJob SummaryThe Mortgage Support Assistant is responsible for all aspects of...</e>
</r>
<r>
  <jt>Mortgage Loan Underwriter (Tulsa)</jt>
  <cn url="">Bank of Oklahoma</cn>
  <src url="http://api.simplyhired.com/a/job-details/view/cparm-cF9pZD00Mzc0MiZ6b25lPTYmaXA9JmNvdW50PTUwJnN0YW1wPTIwMTItMTEtMDQgMTY6NTE6NTMmcHVibGlzaGVyX2NoYW5uZWxfaWRzPSZhX2lkPTIyMjk2JmNfaWQ9MTAxNjkmY3BjPTAuMTEmcG9zPTgmaGFzaD0xNmVlZDNlN2QxZDZiMGIyZWU5YmNlODVlYTYyYjE3MA%3D%3D%3B48bf8a28697160108de688255f9edce6/jobkey-32a238d8abdb5787f06cd7cda3cb580101bc0/rid-jamgnedemnqelrodlshoeqvoijxzenca/pub_id-43742/cjp-7/hits-119047">Bank of Oklahoma</src>
  <ty>sponsored</ty>
  <loc cty="" st="OK" postal="" county="" region="" country="US">Oklahoma</loc>
  <ls>2012-11-02T13:24:41Z</ls>
  <dp>2012-11-02T13:24:41Z</dp>
  <e>its markets.
        
Join us as a Mortgage Loan Underwriter
        
The Mortgage Loan Underwriter is primarily responsible for the ... knowledge of creditor financial analysis techniques and mortgage credit evaluation
Thorough knowledge of criteria,...</e>
</r>
<r>
  <jt>Mortgage Loan Underwriter II Plano</jt>
  <cn url="">Bank of Texas</cn>
  <src url="http://api.simplyhired.com/a/job-details/view/cparm-cF9pZD00Mzc0MiZ6b25lPTYmaXA9JmNvdW50PTUwJnN0YW1wPTIwMTItMTEtMDQgMTY6NTE6NTMmcHVibGlzaGVyX2NoYW5uZWxfaWRzPSZhX2lkPTIyMjk2JmNfaWQ9MTAxNzAmY3BjPTAuMTEmcG9zPTkmaGFzaD05N2RhMTMzM2FiMjA2MDQ2MDcwMTExYWQ4NmZkZDRmZg%3D%3D%3Bd37bb56fda7e03bcdbdde31a8572a7b8/jobkey-7e61f21a28664e9e7716d1da8dcdf24bb07ab341/rid-jamgnedemnqelrodlshoeqvoijxzenca/pub_id-43742/cjp-8/hits-119047">Bank of Texas</src>
  <ty>sponsored</ty>
  <loc cty="Dallas" st="TX" postal="75205" county="" region="" country="US">Dallas, TX</loc>
  <ls>2012-11-02T13:25:00Z</ls>
  <dp>2012-11-02T13:25:00Z</dp>
  <e>Mortgage Loan Underwriter II Plano TX
        
The Mortgage Underwriter II is primarily responsible for the mortgage credit risk ... complex conventional and government residential mortgage loans. This position ensures the highest level of...</e>
</r>
<r>
  <jt>MORTGAGE CLERK (DISCLOSURE)</jt>
  <cn url="">Adecco</cn>
  <src url="http://api.simplyhired.com/a/job-details/view/cparm-cF9pZD00Mzc0MiZ6b25lPTYmaXA9JmNvdW50PTUwJnN0YW1wPTIwMTItMTEtMDQgMTY6NTE6NTMmcHVibGlzaGVyX2NoYW5uZWxfaWRzPSZhX2lkPTg0ODMmY19pZD0xNDc4OCZjcGM9MC4yMSZwb3M9MTAmaGFzaD1mYTM5MGNjNzk1NGQ1ZDI3N2IzNjY2ZDk0MjQxNzllZA%3D%3D%3B9cbc6fe70b3da4050eaec8466c955b7f/jobkey-76521d29539f25b3357abfaca219ddc4381e28/rid-jamgnedemnqelrodlshoeqvoijxzenca/pub_id-43742/cjp-9/hits-119047">Adecco</src>
  <ty>sponsored</ty>
  <loc cty="Olean" st="NY" postal="14760" county="" region="" country="US">Olean, NY</loc>
  <ls>2012-10-24T22:25:07Z</ls>
  <dp>2012-10-24T22:25:07ERROR</dp>
  <e>This candidate will be working in the mortgage dept. Reviews applications and supporting qualifying documents submitted ... clerical errors.Enters initial information into EasyLender Mortgage to create an electronic Loan File for all consumer...</e>
</r>
</rs>
<pjl>
</pjl>
</shrs>
XML;
        
        return $xml;
    }

}
