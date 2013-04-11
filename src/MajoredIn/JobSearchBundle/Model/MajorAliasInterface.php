<?php

namespace MajoredIn\JobSearchBundle\Model;

interface MajorAliasInterface
{
    /**
     * Set name
     *
     * @param string $name
     * @return Major
     */
    public function setName($name);
    
    /**
     * Get name
     *
     * @return string
     */
    public function getName();
    
    /**
     * Set name_canonical
     *
     * @param string $nameCanonical
     * @return Major
     */
    public function setNameCanonical($nameCanonical);
    
    /**
     * Get name_canonical
     *
     * @return string
     */
    public function getNameCanonical();
    
    /**
     * Set major
     *
     * @param MajorInterface $major
     * @return MajorAlias
     */
    public function setMajor(MajorInterface $major = null);
    
    /**
     * Get major
     *
     * @return MajorInterface
     */
    public function getMajor();
}
