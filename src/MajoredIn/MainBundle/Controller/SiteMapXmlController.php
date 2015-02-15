<?php

namespace MajoredIn\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MajoredIn\JobSearchBundle\Controller\JobSearchController;

class SiteMapXmlController extends Controller
{   
    public function indexAction()
    {
        $majorManager = $this->get('mi_search.major.manager');
        $majors = $majorManager->findMajors();
        
        $response = $this->render('MajoredInMainBundle:SiteMapXml:index.xml.twig', array(
                'majors' => $majors
        ));

        return $response;
    }
    
    public function locationsAction()
    {
        $locationManager = $this->get('mi_search.location.manager');
        $locations = $locationManager->findLocations();
        
        $router = $this->get('router');
        $canonicalizer = $this->get('mi_search.canonicalizer');
        
        $excludedUrls = $this->getExcludedUrlsLike($router->generate('mi_jobs_results', array('major' => 'undeclared'), true));
        
        foreach ($locations as $location) {
            $locationUrls[] = $this->get('router')->generate('mi_jobs_results', array('major' => 'undeclared', 'location' => $canonicalizer->dash($location->getName())), true);
            $locationUrlsIntern[] = $this->get('router')->generate('mi_jobs_results', array('major' => 'undeclared', 'location' => $canonicalizer->dash($location->getName()), 'jobtype' => 'internship'), true);
        }
        
        $response = $this->render('MajoredInMainBundle:SiteMapXml:locations.xml.twig', array(
                'locationUrls' => $locationUrls,
                'locationUrlsIntern' => $locationUrlsIntern
        ));
        
        return $response;
    }
    
    public function majorsAction($id)
    {
        $majorManager = $this->get('mi_search.major.manager');
        $major = $majorManager->findMajorBy(array('id' => $id));
        
        $business = $majorManager->findMajorByName('Business');
        $maxPopularity = $business->getPopularity();
        if ($maxPopularity > 0) {
            $weight = log($major->getPopularity(), $maxPopularity);
        }
        else {
            $weight = 1;
        }
        $weight = ($weight > 1) ? 1 : $weight;
        
        $locationCount = round(1000 * $weight); //dilute length of sitemap by importance to limit robot crawling.
        
        $locationManager = $this->get('mi_search.location.manager');
        $locations = $locationManager->findLocationsLike('', $locationCount);
        $locationsIntern = array_slice($locations, 0, round($locationCount/10));
        
        $router = $this->get('router');
        $canonicalizer = $this->get('mi_search.canonicalizer');
        $excludedUrlManager = $this->get('mi_search.excluded_url.manager');
        
        $excludedUrls = $this->getExcludedUrlsLike($router->generate('mi_jobs_results', array('major' => $canonicalizer->dash($major->getName())), true));
        
        $majorDashed = $canonicalizer->dash($major->getName());
        $majorUrl[] = $router->generate('mi_jobs_results', array('major' => $majorDashed), true);
        $majorUrlIntern[] = $router->generate('mi_jobs_results', array('major' => $majorDashed, 'jobtype' => 'internship'), true);
        
        foreach ($locations as $location) {
            $locationUrls[] = $this->get('router')->generate('mi_jobs_results', array('major' => $majorDashed, 'location' => $canonicalizer->dash($location->getName())), true);
        }
        
        foreach ($locationsIntern as $location) {
            $locationUrlsIntern[] = $this->get('router')->generate('mi_jobs_results', array('major' => $majorDashed, 'location' => $canonicalizer->dash($location->getName()), 'jobtype' => 'internship'), true);
        }

        $majorUrl = array_diff($majorUrl, $excludedUrls);
        $majorUrlIntern = array_diff($majorUrlIntern, $excludedUrls);
        $locationUrls = array_diff($locationUrls, $excludedUrls);
        $locationUrlsIntern = array_diff($locationUrlsIntern, $excludedUrls);
        
        $response = $this->render('MajoredInMainBundle:SiteMapXml:majors.xml.twig', array(
                'majorUrl' => $majorUrl,
                'majorUrlIntern' => $majorUrlIntern,
                'locationUrls' => $locationUrls,
                'locationUrlsIntern' => $locationUrlsIntern
        ));

        return $response;
    }
    
    private function getExcludedUrlsLike($url) {
        $excludedUrlManager = $this->get('mi_search.excluded_url.manager');
        
        $excludedUrls = $excludedUrlManager->findExcludedUrlsLike($url);
        $mapper = function ($excludedUrl) {
            return $excludedUrl->getUrl();
        };
        
        return array_map($mapper, $excludedUrls);
    }
}
