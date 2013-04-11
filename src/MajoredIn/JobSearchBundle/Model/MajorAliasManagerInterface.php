<?php

namespace MajoredIn\JobSearchBundle\Model;

interface MajorAliasManagerInterface
{
    /**
     * Creates an empty major instance.
     *
     * @return MajorInterface
     */
    public function createMajorAlias();
    
    /**
     * Deletes a major.
     *
     * @param MajorInterface $major
     */
    public function deleteMajorAlias(MajorAliasInterface $majorAlias);
    
    /**
     * Finds one majorAlias by the given criteria.
     *
     * @param array $criteria
     *
     * @return MajorAliasInterface
     */
    public function findMajorAliasBy(array $criteria);
    
    /**
     * Find a majorAlias by its name.
     *
     * @param string $name
     *
     * @return MajorAliasInterface or null if user does not exist
     */
    public function findMajorAliasByName($name);
    
    /**
     * Returns a collection with all majorAlias instances.
     *
     * @return \Traversable
     */
    public function findMajorAliases();
    
    /**
     * Reloads a majorAlias.
     *
     * @param MajorAliasInterface $majorAlias
     */
    public function reloadMajorAlias(MajorAliasInterface $majorAlias);
    
    /**
     * Updates a majorAlias.
     *
     * @param MajorAliasInterface $majorAlias
     * @param Boolean $andFlush Whether to flush the changes (default true)
     */
    public function updateMajorAlias(MajorAliasInterface $majorAlias, $andFlush = true);
    
    /**
     * Updates the canonical name field for a majorAlias.
     *
     * @param MajorAliasInterface $majorAlias
     */
    public function updateCanonicalFields(MajorAliasInterface $majorAlias);
    
    /**
     * Canonicalizes a name
     *
     * @param String $name
     *
     * @return String
     */
    public function canonicalizeName($name);
}
