<?php

namespace MajoredIn\jobSearchBundle\Search;

class JobResults
{
    //scalar results and filters
    protected $distance;
    protected $totalViewable;
    protected $totalResults;
    protected $currentPage;
    protected $maxPage;
    protected $datePosted;
    protected $sortBy;
    protected $jobType;
    protected $jobBoards;
    protected $recruiters;
    protected $companySize;
    protected $companyRevenue;
    protected $company;
    protected $title;
    protected $hidden;
    
    //Job Listings array
    protected $jobListings;
    
    //array results
    protected $locations;
    protected $titles;
    protected $companies;
    
    //working set arrays
    private $mLocations;
    private $mTitles;
    private $mCompanies;
    
    //cached flag
    protected $cached;
    
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->jobListings = array();
        
        $this->locations = null;
        $this->titles = null;
        $this->companies = null;
        
        $this->mLocations = array();
        $this->mTitles = array();
        $this->mCompanies = array();
        
        $this->cached = false;
    }
    
    /**
     * Set the radius from location
     * 
     * @param integer $distance
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;
    }
    
    /**
     * Get the radius from location
     * 
     * @return integer
     */
    public function getDistance()
    {
        return $this->distance;
    }
    
    /**
     * Set the total viewable results for search
     * 
     * @param integer $totalViewable
     */
    public function setTotalViewable($totalViewable)
    {
        $this->totalViewable = $totalViewable;
    }
    
    /**
     * Get the total viewable number of results
     * 
     * @return integer
     */
    public function getTotalViewable()
    {
        return $this->totalViewable;
    }
    
    /**
     * Set the total results for search
     *
     * @param integer $totalResults
     */
    public function setTotalResults($totalResults)
    {
        $this->totalResults = $totalResults;
    }
    
    /**
     * Get the total number of results
     *
     * @return integer
     */
    public function getTotalResults()
    {
        return $this->totalResults;
    }
    
    /**
     * Set the current page of results
     * 
     * @param integer $currentPage
     */
    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;
    }
    
    /**
     * Get the current page
     * 
     * @return integer
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }
    
    /**
     * Set the max page
     * 
     * @param integer $maxPage
     */
    public function setMaxPage($maxPage)
    {
        $this->maxPage = $maxPage;
    }
    
    /**
     * Get the max page
     * 
     * @return integer
     */
    public function getMaxPage()
    {
        return $this->maxPage;
    }
    
    /**
     * Set the Date Posted filter
     * 
     * @param integer $datePosted
     */
    public function setDatePosted($datePosted)
    {
        $this->datePosted = $datePosted;
    }
    
    /**
     * Get the Date Posted filter
     * 
     * @return integer
     */
    public function getDatePosted()
    {
        return $this->datePosted;
    }
    
    /**
     * Set the how results sorted (default relevance, or by date)
     *
     * @param integer $datePosted
     */
    public function setSortBy($sortBy)
    {
        $this->sortBy = $sortBy;
    }
    
    /**
     * Get the sort by filter
     *
     * @return integer
     */
    public function getSortBy()
    {
        return $this->sortBy;
    }
    
    /**
     * Set the jobType filter
     * 
     * @param string $jobType
     */
    public function setJobType($jobType)
    {
        $this->jobType = $jobType;
    }
    
    /**
     * Get the jobType filter
     * 
     * @return string
     */
    public function getJobType()
    {
        return $this->jobType;
    }
    
    /**
     * Set the JobBoards filter
     * 
     * @param string $jobBoards
     */
    public function setJobBoards($jobBoards)
    {
        $this->jobBoards = $jobBoards;
    }
    
    /**
     * Get the JobBoards filter
     * 
     * @return string
     */
    public function getJobBoards()
    {
        return $this->jobBoards;
    }
    
    /**
     * Set the recruiters filter
     * 
     * @param string $recruiters
     */
    public function setRecruiters($recruiters)
    {
        $this->recruiters = $recruiters;
    }
    
    /**
     * Get the recruiters filter
     * 
     * @return string
     */
    public function getRecruiters()
    {
        return $this->recruiters;
    }
    
    /**
     * Set the company size filter
     *
     * @param string $companySize
     */
    public function setCompanySize($companySize)
    {
        $this->companySize = $companySize;
    }
    
    /**
     * Get the company size filter
     *
     * @return string
     */
    public function getCompanySize()
    {
        return $this->companySize;
    }
    
    /**
     * Set the company revenue filter
     *
     * @param string $companyRevenue
     */
    public function setCompanyRevenue($companyRevenue)
    {
        $this->companyRevenue = $companyRevenue;
    }
    
    /**
     * Get the company revenue filter
     *
     * @return string
     */
    public function getCompanyRevenue()
    {
        return $this->companyRevenue;
    }
    
    /**
     * Set the company filter
     *
     * @param string $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }
    
    /**
     * Get the company filter
     *
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }
    
    /**
     * Set the title filter
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    /**
     * Get the title filter
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * Set the duplicate results filter
     *
     * @param string $hidden
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
    }
    
    /**
     * Get the duplicate results filter
     *
     * @return string
     */
    public function getHidden()
    {
        return $this->hidden;
    }
    
    /**
     * Add a JobListing to the jobListings array
     * 
     * @param JobListing $jobListing
     */
    public function addJobListing(JobListing $jobListing)
    {
        $this->jobListings[] = $jobListing;
        
        $location = $jobListing->getLocation();
        if ($location != '') {
            $this->mLocations[$location] = isset($this->mLocations[$location]) ? $this->mLocations[$location] + 1 : 1;
        }
        
        $title = $jobListing->getTitle();
        if ($title != '') {
            $this->mTitles[$title] = isset($this->mTitles[$title]) ? $this->mTitles[$title] + 1 : 1;
        }
        
        $company = $jobListing->getCompany();
        if ($company != '') {
            $this->mCompanies[$company] = isset($this->mCompanies[$company]) ? $this->mCompanies[$company] + 1 : 1;
        }
    }
    
    /**
     * Get the jobListings array
     * 
     * @return array
     */
    public function getJobListings()
    {
        return $this->jobListings;
    }
    
    /**
     * Get the ranked locations array
     * 
     * @return array
     */
    public function getLocations()
    {
        if ($this->locations) {
            return $this->locations;
        }
        else {
            arsort($this->mLocations);
            $this->locations = array_keys($this->mLocations);
            return $this->locations;
        }
    }
    
    /**
     * Get the ranked titles array
     *
     * @return array
     */
    public function getTitles()
    {
        if ($this->titles) {
            return $this->titles;
        }
        else {
            arsort($this->mTitles);
            $this->titles = array_keys($this->mTitles);
            return $this->titles;
        }
    }
    
    /**
     * Get the ranked companies array
     *
     * @return array
     */
    public function getCompanies()
    {
        if ($this->companies) {
            return $this->companies;
        }
        else {
            arsort($this->mCompanies);
            $this->companies = array_keys($this->mCompanies);
            return $this->companies;
        }
    }
    
    /**
     * Set the cached flag
     *
     * @param bool $cached
     */
    public function setCached($cached)
    {
        $this->cached = $cached;
    }
    
    /**
     * Get the cached flag
     *
     * @return bool
     */
    public function getCached()
    {
        return $this->cached;
    }
}
