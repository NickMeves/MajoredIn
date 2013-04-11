<?php

namespace MajoredIn\JobSearchBundle\Model;

interface LocationManagerInterface
{
    /**
     * Creates an empty location instance.
     *
     * @return LocationInterface
     */
    public function createLocation();
    
    /**
     * Deletes a location.
     *
     * @param LocationInterface $location
     */
    public function deleteLocation(LocationInterface $location);
    
    /**
     * Finds one location by the given criteria.
     *
     * @param array $criteria
     *
     * @return LocationInterface
     */
    public function findLocationBy(array $criteria);
    
    /**
     * Find a location by its name.
     *
     * @param string $name
     *
     * @return LocationInterface or null if user does not exist
     */
    public function findLocationByName($name);
    
    /**
     * Returns a collection with all location instances.
     *
     * @return \Traversable
     */
    public function findLocations();
    
    /**
     * Find locations using like functionality (autocomplete)
     * 
     * @param string $name
     * @param int $limit
     * 
     * @return \Traversable
     */
    public function findLocationsLike($name, $limit = null);
    
    /**
     * Reloads a location.
     *
     * @param LocationInterface $location
     */
    public function reloadLocation(LocationInterface $location);
    
    /**
    * Updates a location.
    *
    * @param LocationInterface $location
    * @param Boolean $andFlush Whether to flush the changes (default true)
    */
    public function updateLocation(LocationInterface $location, $andFlush = true);
    
    /**
     * Updates the canonical name field for a location.
     *
     * @param LocationInterface $location
     */
    public function updateCanonicalFields(LocationInterface $location);
    
    /**
     * Canonicalizes a name
     *
     * @param String $name
     *
     * @return String
     */
    public function canonicalizeName($name);
}
