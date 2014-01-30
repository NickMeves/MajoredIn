<?php

namespace MajoredIn\JobSearchBundle\Model;

interface ExcludedUrlInterface
{
    /**
     * Set url
     *
     * @param string $url
     * @return ExcludedUrlInterface
     */
    public function setUrl($url);
    
    /**
     * Get name
     *
     * @return string
     */
    public function getUrl();
}
