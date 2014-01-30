<?php

namespace MajoredIn\JobSearchBundle\Model;

interface ExcludedUrlManagerInterface
{
    /**
     * Creates an empty excludedUrl instance.
     *
     * @return ExcludedUrlInterface
     */
    public function createExcludedUrl();
    
    /**
     * Deletes a excludedUrl.
     *
     * @param ExcludedUrlInterface $excludedUrl
     */
    public function deleteExcludedUrl(ExcludedUrlInterface $excludedUrl);
    
    /**
     * Finds one excludedUrl by the given criteria.
     *
     * @param array $criteria
     *
     * @return ExcludedUrlInterface
     */
    public function findExcludedUrlBy(array $criteria);
    
    /**
     * Find a excludedUrl by its name.
     *
     * @param string $name
     *
     * @return ExcludedUrlInterface or null if user does not exist
     */
    public function findExcludedUrlByUrl($url);
    
    /**
     * Returns a collection with all excludedUrl instances.
     *
     * @return \Traversable
     */
    public function findExcludedUrls();
    
    /**
     * Find excludedUrls using like functionality (autocomplete)
     * 
     * @param string $url
     * @param int $limit
     * 
     * @return \Traversable
     */
    public function findExcludedUrlsLike($url, $limit = null);
    
    /**
     * Reloads a excludedUrl.
     *
     * @param ExcludedUrlInterface $excludedUrl
     */
    public function reloadExcludedUrl(ExcludedUrlInterface $excludedUrl);
    
    /**
     * Updates a excludedUrl.
     *
     * @param ExcludedUrlInterface $excludedUrl
     * @param Boolean $andFlush Whether to flush the changes (default true)
     */
    public function updateExcludedUrl(ExcludedUrlInterface $excludedUrl, $andFlush = true);
}
