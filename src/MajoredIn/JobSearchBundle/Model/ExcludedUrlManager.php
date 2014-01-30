<?php

namespace MajoredIn\JobSearchBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use MajoredIn\JobSearchBundle\Entity\ExcludedUrl;

class ExcludedUrlManager implements ExcludedUrlManagerInterface
{
    protected $objectManager;
    protected $repository;
    
    /**
     * Constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(ObjectManager $om)
    {
        $this->objectManager = $om;
        $this->repository = $om->getRepository('MajoredInJobSearchBundle:ExcludedUrl');
    }
    
    /**
     * {@inheritDoc}
     */
    public function createExcludedUrl()
    {
        $excludedUrl = new ExcludedUrl();
        return $excludedUrl;
    }
    
    /**
     * {@inheritDoc}
     */
    public function deleteExcludedUrl(ExcludedUrlInterface $excludedUrl)
    {
        $this->objectManager->remove($excludedUrl);
        $this->objectManager->flush();
    }
    
    /**
     * {@inheritDoc}
     */
    public function findExcludedUrlBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }
    
    /**
     * {@inheritDoc}
     */
    public function findExcludedUrlByUrl($url)
    {
        return $this->repository->findExcludedUrlByUrl($url);
    }
    
    /**
     * {@inheritDoc}
     */
    public function findExcludedUrls()
    {
        return $this->repository->findAll();
    }
    
    /**
     * {@inheritDoc}
     */
    public function findExcludedUrlsLike($url, $limit = null)
    {
        return $this->repository->findUrlLike($url, $limit);
    }
    
    /**
     * {@inheritDoc}
     */
    public function reloadExcludedUrl(ExcludedUrlInterface $excludedUrl)
    {
        $this->objectManager->refresh($excludedUrl);
    }
    
    /**
     * {@inheritDoc}
     */
    public function updateExcludedUrl(ExcludedUrlInterface $excludedUrl, $andFlush = true)
    {
        $this->objectManager->persist($excludedUrl);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }
}
