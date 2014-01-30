<?php

namespace MajoredIn\JobSearchBundle\Model;

interface MajorInterface
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
     * Set job_query
     *
     * @param string $jobQuery
     * @return Major
     */
    public function setJobQuery($jobQuery);
    
    /**
     * Get job_query
     *
     * @return string
     */
    public function getJobQuery();
    
    /**
     * Set popularity
     *
     * @param integer $popularity
     * @return Major
     */
    public function setPopularity($popularity);
    
    /**
     * Get popularity
     *
     * @return integer
     */
    public function getPopularity();
    
    /**
     * Set related Major description page (Post entity)
     */
    public function setPost($post);
    
    /**
     * Get related Major description page (Post entity)
     */
    public function getPost();

}
