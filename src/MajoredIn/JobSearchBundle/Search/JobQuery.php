<?php

namespace MajoredIn\JobSearchBundle\Search;

class JobQuery implements JobQueryInterface
{
    protected $baseUrl;
    protected $query;
    protected $location;
    protected $requiredParams;
    protected $optionalParams;
    protected $embeddedParams;
    
    
    /**
     * Constructor
     * 
     * @param string $baseUrl
     */
    public function __construct($baseUrl)
    {
        $this->baseUrl = $baseUrl;
        $this->requiredParams = array();
        $this->optionalParams = array();
        $this->embeddedParams = array();
    }
    
    /**
     * Set the query
     * 
     * @param string $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }
    
    /**
     * Set the location
     * 
     * @param string $location
     */
    public function setLocation($location)
    {
           $this->location = $location;
    }

    /**
     * Add a required parameter
     * 
     * @param string $param
     * @param string $value
     */
    public function addRequiredParam($param, $value)
    {
        $this->requiredParams[$param] = $value;
    }
    
    /**
     * Add an optional parameter
     * 
     * @param string $param
     * @param string $value
     */
    public function addOptionalParam($param, $value)
    {
        $this->optionalParams[$param] = $value;
    }
    
    /**
     * Add an embedded parameter (initial parameter converted to part of query) THIS IS POSSIBLY DEPRECATED
     *
     * @param string $param
     * @param string $value
     */
    public function addEmbeddedParam($param, $value)
    {
        $this->embeddedParams[$param] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function getApiUrl()
    {
        $path = $this->baseUrl;
        
        if ($this->query) {
            $path = $path . "/q-" . static::clean($this->query);
        }
        
        if ($this->location) {
            $path = $path . "/l-" . static::clean($this->location);
        }
        
        ksort($this->optionalParams); //standardize order for caching
        foreach ($this->optionalParams as $param => $value) {
            $path = $path . "/" . static::clean($param) . "-" . static::clean($value);
        }
        
        $path = $path . "?";
        
        ksort($this->requiredParams); //standardize order for caching
        foreach ($this->requiredParams as $param => $value) {
            $path = $path . static::clean($param) . "=" . static::clean($value) . "&";
        }
        
        $path = substr($path, 0, -1);
        
        return $path;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getParams()
    {
        $merged = array_merge($this->requiredParams, $this->embeddedParams, $this->optionalParams);
        $merged['q'] = $this->query;
        $merged['l'] = $this->location;
        
        return $merged;
    }
    
    protected static function clean($urlstr)
    {
        return urlencode(preg_replace('/\//', '', $urlstr));
    }
}
