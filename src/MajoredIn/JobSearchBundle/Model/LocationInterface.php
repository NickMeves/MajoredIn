<?php

namespace MajoredIn\JobSearchBundle\Model;

interface LocationInterface
{
    /**
     * Set name
     *
     * @param string $name
     * @return Location
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
     * @return Location
     */
    public function setNameCanonical($nameCanonical);

    /**
     * Get name_canonical
     *
     * @return string
     */
    public function getNameCanonical();

    /**
     * Set population
     *
     * @param integer $population
     * @return Location
     */
    public function setPopulation($population);

    /**
     * Get population
     *
     * @return integer
     */
    public function getPopulation();
}
