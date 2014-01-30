<?php

namespace MajoredIn\JobSearchBundle\EventListener;

use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use MajoredIn\JobSearchBundle\Util\ExcludeQueueInterface;
use Symfony\Component\Routing\RouterInterface;
use MajoredIn\JobSearchBundle\Util\CanonicalizerInterface;
use MajoredIn\JobSearchBundle\Model\MajorManagerInterface;
use MajoredIn\JobSearchBundle\Model\LocationManagerInterface;
use MajoredIn\JobSearchBundle\Model\ExcludedUrlManagerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

class ExcludeUrlListener implements EventSubscriberInterface
{
    protected $excludeQueue;
    protected $router;
    protected $canonicalizer;
    protected $majorManager;
    protected $locationManager;
    protected $excludedUrlManager;
    protected $objectManager;
    protected $logger;

    public function __construct(ExcludeQueueInterface $excludeQueue, RouterInterface $router, CanonicalizerInterface $canonicalizer, MajorManagerInterface $majorManager, LocationManagerInterface $locationManager, ExcludedUrlManagerInterface $excludedUrlManager, ObjectManager $om, LoggerInterface $logger)
    {
        $this->excludeQueue = $excludeQueue;
        $this->router = $router;
        $this->canonicalizer = $canonicalizer;
        $this->majorManager = $majorManager;
        $this->locationManager = $locationManager;
        $this->excludedUrlManager = $excludedUrlManager;
        $this->objectManager = $om;
        $this->logger = $logger;
    }
 
    public function onKernelTerminate(PostResponseEvent $event)
    {
        while (!$this->excludeQueue->isEmpty()) {
            $request = $this->excludeQueue->remove();
            $parameters = $this->router->match($request->getPathInfo());
            
            $major = isset($parameters['major']) ? $this->canonicalizer->undash($parameters['major']) : null;
            $location = isset($parameters['location']) ? $this->canonicalizer->undash($parameters['location']) : null;
            
            if (null === $major || null === $location) {
                continue;
            }
            
            $queryString = $request->query->all();
            
            if (count(array_diff_assoc($queryString, array('jobtype' => 'internship'))) > 0) {
                return;
            }
            if (null === $this->majorManager->findMajorByName($major) && $major !== 'undeclared') {
                return;
            }
            if (null === $this->locationManager->findLocationByName($location) && $location !== 'everywhere') {
                return;
            }
            
            $major = isset($majorEntity) ? $majorEntity->getName() : $major;
            $location = isset($locationEntity) ? $locationEntity->getName() : $location;
            
            $params = array_merge($queryString, array('major' => $this->canonicalizer->dash($major), 'location' => $this->canonicalizer->dash($location)));
            $url = $this->router->generate('mi_jobs_results', $params, true);
            
            if (null === $this->excludedUrlManager->findExcludedUrlByUrl($url)) {
                $excludedUrl = $this->excludedUrlManager->createExcludedUrl();
                $excludedUrl->setUrl($url);
                $this->excludedUrlManager->updateExcludedUrl($excludedUrl, false);
            }
        }
        
        try {
            $this->objectManager->flush();
        }
        catch (\PDOException $e) {
            $this->logger->err('ExcludeUrlListener::onKernelTerminate: ' . $e->getMessage());
        }
    }
 
    static public function getSubscribedEvents()
    {
        return array(KernelEvents::TERMINATE => 'onKernelTerminate');
    }
}