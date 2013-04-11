<?php

namespace MajoredIn\JobSearchBundle\Search;

use MajoredIn\JobSearchBundle\Util\FeedReaderInterface;
use MajoredIn\JobSearchBundle\Util\CacheInterface;
use MajoredIn\JobSearchBundle\Exception\NoResultsException;
use MajoredIn\JobSearchBundle\Exception\InvalidParamException;
use MajoredIn\JobSearchBundle\Exception\LocationRedirectException;

class JobApiConnector implements JobApiConnectorInterface
{
    protected $feedReader;
    protected $cache;
    protected $defaultParams;
    protected $currentTime;
    
    /**
     * Constructor
     * 
     * @param FeedReaderInterface $feedReader Retreives page from URL (curl, fopen, etc).
     * @param array $defaultParams Default values for filter array
     * @param DateTime $currentTime The time used for calculating age of job postings. (default current time).
     */
    public function __construct(FeedReaderInterface $feedReader, CacheInterface $cache, $defaultParams, \DateTime $currentTime = null)
    {
        $this->feedReader = $feedReader;
        $this->cache = $cache;
        
        $origDefaultParams = array(
                'mi' => '',
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
        $newDefaultParams = array_merge($origDefaultParams, $defaultParams);
        
        $this->defaultParams = $newDefaultParams;
        
        if ($currentTime === null) {
            $this->currentTime = new \DateTime();
        }
        else {
            $this->currentTime = $currentTime;
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function accessApi(JobQueryInterface $jobQuery)
    {
        $url = $jobQuery->getApiUrl();
        $queryParams = $jobQuery->getParams();
        $queryParams = array_merge($this->defaultParams, $queryParams);
        $jobResults = new JobResults();
        
        //Hash the URL to avoid keys >250 or invalid characters.
        $urlMd5 = md5($url);
        if ($this->cache->contains($urlMd5)) {
            $xmlstr = $this->cache->fetch($urlMd5);
            $jobResults->setCached(true);
        }
        else {
            $xmlstr = $this->feedReader->readUrl($url);
            if ($xmlstr) {
                $this->cache->save($urlMd5, $xmlstr, 300);
            }
        }
        
        if (! $xmlstr) {
            throw new \Exception;
        }
        
        $xml = new \SimpleXMLElement($xmlstr);
        
        //Check if returned XML is <sherror> and handle
        if ($error_array = $xml->xpath('/sherror/error')) {
            while (list( , $error) = each($error_array)) {
                if ($error['type'] == 'noresults') {
                    throw new NoResultsException();
                }
                elseif ($error['type'] == 'invalidparam') {
                    throw new InvalidParamException();
                }
                elseif ($error['type'] == 'instruction') {
                    if (isset($error->option[0])) {
                        throw new LocationRedirectException($error->option[0]);
                    }
                    else {
                        throw new \Exception;
                    }
                }
                else {
                    throw new \Exception;
                }
            }
        }
        
        if (!isset($xml->rq)) {
            throw new \Exception;
        }
        
        $rpd = (int) $xml->rq->rpd;
        $tr = (int) $xml->rq->tr;
        $tv = (int) $xml->rq->tv;
        $maxPage = ($rpd == 0) ? 1 : ceil($tv / $rpd);
        
        $jobResults->setTotalViewable($tv);
        $jobResults->setTotalResults($tr);
        $jobResults->setMaxPage($maxPage);
        
        $jobResults->setDistance($queryParams['mi']);
        $jobResults->setCurrentPage($queryParams['pn']);
        $jobResults->setDatePosted($queryParams['fdb']);
        $jobResults->setSortBy($queryParams['sb']);
        $jobResults->setJobType($queryParams['fjt']);
        $jobResults->setJobBoards($queryParams['fsr']);
        $jobResults->setRecruiters($queryParams['fem']);
        $jobResults->setCompanySize($queryParams['fcz']);
        $jobResults->setCompanyRevenue($queryParams['fcr']);
        $jobResults->setCompany($queryParams['c']);
        $jobResults->setTitle($queryParams['t']);
        $jobResults->setHidden($queryParams['clst']);
        
        if (!isset($xml->rs)) {
            throw new NoResultsException();
        }
        
        foreach ($xml->rs->children() as $result) {
            try {
                $datePosted = new \DateTime($result->dp);
                $interval = $this->currentTime->diff($datePosted);
                $age = $interval->format('%a');
                if ($age < 0) {
                    $age = 'error';
                }
                elseif ($age == 0) {
                    $age = $interval->format('%h');
                    if ($age == 1) {
                        $age = $age . ' hour';
                    }
                    else {
                        $age = $age . ' hours';
                    }
                }
                else {
                    if ($age == 1) {
                        $age = $age . ' day';
                    }
                    elseif ($age > 30) {
                        $age = '30+ days';
                    }
                    else {
                        $age = $age. ' days';
                    }
                }
            }
            catch (\Exception $e) {
                $age = 'error';
            }
            
            $result_src_attr = $result->src->attributes();
            $jobResults->addJobListing(new JobListing(
                htmlspecialchars_decode($result->jt, ENT_QUOTES),
                htmlspecialchars_decode($result->cn, ENT_QUOTES),
                htmlspecialchars_decode($result_src_attr['url'], ENT_QUOTES),
                htmlspecialchars_decode($result->ty, ENT_QUOTES),
                htmlspecialchars_decode($result->loc, ENT_QUOTES),
                $age,
                htmlspecialchars_decode($result->e, ENT_QUOTES)
            ));
        }
        
        $jobListings = $jobResults->getJobListings();
        if (empty($jobListings)) {
            throw new NoResultsException();
        }
        
        return $jobResults;
    }
    
    /**
     * {@inheritDoc}
     */
    public function preCache(JobQueryInterface $jobQuery)
    {
        $url = $jobQuery->getApiUrl();
        
        //Hash the URL to avoid keys >250 or invalid characters.
        $urlMd5 = md5($url);
        if ($this->cache->contains($urlMd5)) {
            return true;
        }
        
        $xmlstr = $this->feedReader->readUrl($url);
        if ($xmlstr) {
            $this->cache->save($urlMd5, $xmlstr, 300);
            return true;
        }
        else {
            return false;
        }
    }
}
