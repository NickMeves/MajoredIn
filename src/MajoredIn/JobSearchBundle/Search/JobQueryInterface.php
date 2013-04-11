<?php

namespace MajoredIn\JobSearchBundle\Search;

interface JobQueryInterface
{
    /**
     * Returns the API URL with search parameters.
     * 
     * @return string The URL to access API.
     */
    public function getApiUrl();
    
    /**
     * Returns an array of all set options
     * 
     * @return array
     */
    public function getParams();
}
