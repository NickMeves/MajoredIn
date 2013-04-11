<?php

namespace MajoredIn\jobSearchBundle\Search;

class JobListing
{
    protected $title;
    protected $company;
    protected $url;
    protected $type;
    protected $location;
    protected $age;
    protected $excerpt;
    
    /**
     * Constructor
     * 
     * @param string $title
     * @param string $company
     * @param string $url
     * @param string $type
     * @param string $location
     * @param string $excerpt
     */
    public function __construct($title, $company, $url, $type, $location, $age, $excerpt)
    {
        $this->title = $title;
        $this->company = $company;
        $this->url = $url;
        $this->type = $type;
        $this->location = $location;
        $this->age = $age;
        $this->excerpt = $excerpt;
    }
    
    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * Set company
     *
     * @param string $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }
    
    /**
     * Get company
     *
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }
    
    /**
     * Set url
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }
    
    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
    
    /**
     * Set type
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
    
    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * Set location
     *
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }
    
    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }
    
    /**
     * Set days ago posted
     *
     * @param string $datePosted
     */
    public function setAge($age)
    {
        $this->age = $age;
    }
    
    /**
     * Get days ago posted
     *
     * @return string
     */
    public function getAge()
    {
        return $this->age;
    }
    
    /**
     * Set excerpt
     *
     * @param string $excerpt
     */
    public function setExcerpt($excerpt)
    {
        $this->excerpt = $excerpt;
    }
    
    /**
     * Get excerpt
     *
     * @return string
     */
    public function getExcerpt()
    {
        return $this->excerpt;
    }
    
}
