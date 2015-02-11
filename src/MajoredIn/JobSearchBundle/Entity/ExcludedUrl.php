<?php

namespace MajoredIn\JobSearchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use MajoredIn\JobSearchBundle\Model\ExcludedUrlInterface;

/**
 * MajoredIn\JobSearchBundle\Entity\ExcludedUrl
 *
 * @ORM\Table(indexes={@ORM\Index(name="url_idx", columns={"url"})})
 * @ORM\Entity(repositoryClass="MajoredIn\JobSearchBundle\Entity\ExcludedUrlRepository")
 */
class ExcludedUrl implements ExcludedUrlInterface
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
     * @var string $url
     *
     * @ORM\Column(name="url", type="string", length=2048)
     */
    protected $url;

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
    public function setUrl($url)
    {
        $this->url = $url;
    
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getUrl()
    {
        return $this->url;
    }
}