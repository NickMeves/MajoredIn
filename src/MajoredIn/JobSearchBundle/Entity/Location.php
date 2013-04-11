<?php

namespace MajoredIn\JobSearchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use MajoredIn\JobSearchBundle\Model\LocationInterface;

/**
 * MajoredIn\JobSearchBundle\Entity\Location
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="MajoredIn\JobSearchBundle\Entity\LocationRepository")
 */
class Location implements LocationInterface
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string $name_canonical
     *
     * @ORM\Column(name="name_canonical", type="string", length=255)
     */
    private $nameCanonical;

    /**
     * @var integer $population
     *
     * @ORM\Column(name="population", type="integer")
     */
    private $population;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function setNameCanonical($nameCanonical)
    {
        $this->nameCanonical = $nameCanonical;
    
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getNameCanonical()
    {
        return $this->nameCanonical;
    }

    /**
     * {@inheritDoc}
     */
    public function setPopulation($population)
    {
        $this->population = $population;
    
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getPopulation()
    {
        return $this->population;
    }
}
