<?php

namespace MajoredIn\JobSearchBundle\Model;

interface MajorManagerInterface
{
    /**
     * Creates an empty major instance.
     *
     * @return MajorInterface
     */
    public function createMajor();
    
    /**
     * Deletes a major.
     *
     * @param MajorInterface $major
     */
    public function deleteMajor(MajorInterface $major);
    
    /**
     * Finds one major by the given criteria.
     *
     * @param array $criteria
     *
     * @return MajorInterface
     */
    public function findMajorBy(array $criteria);
    
    /**
     * Find a major by its name.
     *
     * @param string $name
     *
     * @return MajorInterface or null if user does not exist
     */
    public function findMajorByName($name);
    
    /**
     * Returns a collection with all major instances.
     *
     * @return \Traversable
     */
    public function findMajors();
    
    /**
     * Find majors using like functionality (autocomplete)
     * 
     * @param string $name
     * @param int $limit
     * 
     * @return \Traversable
     */
    public function findMajorsLike($name, $limit = null);
    
    /**
     * Reloads a major.
     *
     * @param MajorInterface $major
     */
    public function reloadMajor(MajorInterface $major);
    
    /**
     * Updates a major.
     *
     * @param MajorInterface $major
     * @param Boolean $andFlush Whether to flush the changes (default true)
     */
    public function updateMajor(MajorInterface $major, $andFlush = true);
    
    /**
     * Updates the canonical name field for a major.
     *
     * @param MajorInterface $major
     */
    public function updateCanonicalFields(MajorInterface $major);
    
    /**
     * Canonicalizes a name
     *
     * @param String $name
     *
     * @return String
     */
    public function canonicalizeName($name);
}
