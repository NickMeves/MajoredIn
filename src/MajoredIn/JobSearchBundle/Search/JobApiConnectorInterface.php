<?php

namespace MajoredIn\JobSearchBundle\Search;

interface JobApiConnectorInterface
{
    /**
     * Creates a JobResultsInterface based on XML API results for a JobQueryInterface
     * 
     * @param JobQueryInterface The JobQuery with the API URL.
     * 
     * @throws Exception if XML from API is malformed.
     * 
     * @return JobResultsListInterface 
     */
    public function accessApi(JobQueryInterface $jobQuery);
    
    /**
     * PreCaches the results from the API
     *
     * @param JobQueryInterface The JobQuery with the API URL.
     *
     * @return Boolean true if cached, false if not
     */
    public function preCache(JobQueryInterface $jobQuery);
}
