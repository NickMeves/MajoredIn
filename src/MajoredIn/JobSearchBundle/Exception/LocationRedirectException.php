<?php

namespace MajoredIn\JobSearchBundle\Exception;

class LocationRedirectException extends \Exception
{
    protected $location;
    
    /**
     * Constructor
     * 
     * @param string $location location to redirect to
     * @param string $message
     * @param integer $code
     * @param Exception $previous
     */
    public function __construct($location, $message = null, $code = 0, Exception $previous = null)
    {
        $this->location = $location;
        parent::__construct($message, $code, $previous);
    }
    
    /**
     * Set the redirection location
     * 
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }    
    
    /**
     * Get the redirection location
     * 
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }
}
