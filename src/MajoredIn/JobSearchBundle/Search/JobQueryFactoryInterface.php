<?php

namespace MajoredIn\JobSearchBundle\Search;

use Symfony\Component\HttpFoundation\Request;

interface JobQueryFactoryInterface
{
    /**
     * Creates a JobQueryInterface based on an HTTP Request
     * 
     * @param Request $request The HTTP Request
     * @param string $major Optional: the college major to search in the database
     * @param string $location Optional: The location to center the search radius
     * 
     * @return JobQueryInterface 
     */
    public function createFromRequest(Request $request, $major, $location);
}
