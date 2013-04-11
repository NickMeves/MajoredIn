<?php

namespace MajoredIn\JobSearchBundle\Search;

use MajoredIn\JobSearchBundle\Model\MajorManagerInterface;
use MajoredIn\JobSearchBundle\Model\MajorAliasManagerInterface;
use MajoredIn\JobSearchBundle\Entity\Major;
use Symfony\Component\HttpFoundation\Request;

class JobQueryFactory implements JobQueryFactoryInterface
{
    protected $majorManager;
    protected $majorAliasManager;
    protected $baseUrl;
    protected $allowableParams;
    protected $publisherId;
    protected $jobamaticDomain;
    protected $searchStyle;
    protected $configFlag;
    
    /**
     * Constructor
     * 
     * @param MajorManagerInterface $majorManager The manager of the major repository
     * @param string $baseUrl The base URL of the Simply Hired API
     * @param array $allowableParams The GET parameters and rules allowed
     * @param string $publisherId Simply Hired publisher ID
     * @param string $jobamaticDomain Simply Hired jobamatic domain
     * @param string $searchStyle Simply Hired required API value (unknown use)
     * @param string $configFlag Simply Hired required API value (unknown use)
     */
    public function __construct(MajorManagerInterface $majorManager, MajorAliasManagerInterface $majorAliasManager, $baseUrl, array $allowableParams, $publisherId, $jobamaticDomain, $searchStyle, $configFlag)
    {
        $this->majorManager = $majorManager;
        $this->majorAliasManager = $majorAliasManager;
        $this->baseUrl = $baseUrl;
        $this->allowableParams = $allowableParams;
        $this->publisherId = $publisherId;
        $this->jobamaticDomain = $jobamaticDomain;
        $this->searchStyle = $searchStyle;
        $this->configFlag = $configFlag;
    }
    
    /**
     * {@inheritDoc}
     */
    public function createFromRequest(Request $request, $major, $location)
    {
        $jobQuery = new JobQuery($this->baseUrl);
        
        $queryString = $request->query->all();
        
        //Find a majorEntity directly or via a majorAlias
        try {
            $majorEntity = $this->majorManager->findMajorByName($major);
            if (! $majorEntity) {
                $majorAliasEntity = $this->majorAliasManager->findMajorAliasByName($major);
                if ($majorAliasEntity) {
                    $majorEntity = $majorAliasEntity->getMajor();
                }
            }
        }
        catch (\Exception $e) {
            $majorEntity = null;
        }
        
        if ($majorEntity) {
            $jobQuery->setQuery($majorEntity->getJobQuery());
        }
        else {
            if ($major === 'undeclared') {
                $jobQuery->setQuery('');
            }
            else {
                $jobQuery->setQuery($major);
            }
        }
        
        if ($location === 'everywhere') {
            $jobQuery->setLocation('');
        }
        else {
            $jobQuery->setLocation($location);
        }
        
        $jobQuery->addOptionalParam('frl', 'newgrad');
        
        $optionalParams = array_intersect_key($queryString, $this->allowableParams);
        foreach ($optionalParams as $param => $value) {
            if (isset($this->allowableParams[$param]['requirements']) && preg_match($this->allowableParams[$param]['requirements'], $value)) {
                $jobQuery->addOptionalParam($this->allowableParams[$param]['translation'], $value);
            }
        }
        
        $jobQuery->addRequiredParam('pshid', $this->publisherId);
        $jobQuery->addRequiredParam('jbd', $this->jobamaticDomain);
        $jobQuery->addRequiredParam('ssty', $this->searchStyle);
        $jobQuery->addRequiredParam('cflg', $this->configFlag);
        $jobQuery->addRequiredParam('clip', $request->getClientIp());

        return $jobQuery;
    }
    
    //POSSIBLY DEPRECATED
    protected static function trim($str)
    {
        $str = preg_replace('/\s+/', ' ', $str);
        $str = preg_replace('/^\s/', '', $str);
        $str = preg_replace('/\s$/', '', $str);
        
        return $str;
    }
}
