<?php

namespace MajoredIn\JobSearchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use MajoredIn\JobSearchBundle\Model\MajorInterface;

/**
 * MajoredIn\JobSearchBundle\Entity\Major
 *
 * @ORM\Table(indexes={@ORM\Index(name="name_canonical_idx", columns={"name_canonical"}), @ORM\Index(name="popularity_idx", columns={"popularity"})})
 * @ORM\Entity(repositoryClass="MajoredIn\JobSearchBundle\Entity\MajorRepository")
 */
class Major implements MajorInterface
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
     * @var string $jobQuery
     *
     * @ORM\Column(name="job_query", type="text")
     */
    protected $jobQuery;

    /**
     * @var integer $popularity
     *
     * @ORM\Column(name="popularity", type="integer")
     */
    protected $popularity;
    
    /**
     * @var Post $post
     *
     * @ORM\Column(name="post_id", type="bigint", nullable=true, options={"unsigned"=true})
     */
    protected $post;


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
    public function setJobQuery($jobQuery)
    {
        $this->jobQuery = $jobQuery;
    
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getJobQuery()
    {
        return $this->jobQuery;
    }

    /**
     * {@inheritDoc}
     */
    public function setPopularity($popularity)
    {
        $this->popularity = $popularity;
    
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getPopularity()
    {
        return $this->popularity;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setPost($post)
    {
        $this->post = $post;
    
        return $this;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getPost()
    {
        return $this->post;
    }
}
