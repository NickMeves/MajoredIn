<?php

namespace MajoredIn\JobSearchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use MajoredIn\JobSearchBundle\Model\MajorInterface;
use MajoredIn\JobSearchBundle\Model\MajorAliasInterface;

/**
 * MajoredIn\JobSearchBundle\Entity\MajorAlias
 *
 * @ORM\Table(indexes={@ORM\index(name="name_canonical_idx", columns={"name_canonical"})})
 * @ORM\Entity(repositoryClass="MajoredIn\JobSearchBundle\Entity\MajorAliasRepository")
 */
class MajorAlias implements MajorAliasInterface
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string $nameCanonical
     *
     * @ORM\Column(name="name_canonical", type="string", length=255)
     */
    protected $nameCanonical;

    /**
     * @var Major $major
     *
     * @ORM\ManyToOne(targetEntity="Major")
     * @ORM\JoinColumn(name="major_id", referencedColumnName="id")
     */
    protected $major;

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
     * Set major
     *
     * @param Major $major
     * @return MajorAlias
     */
    public function setMajor(MajorInterface $major = null)
    {
        $this->major = $major;
    
        return $this;
    }

    /**
     * Get major
     *
     * @return Major 
     */
    public function getMajor()
    {
        return $this->major;
    }
}