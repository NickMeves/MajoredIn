<?php

namespace MajoredIn\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
    
    public function mainAction()
    {
        $response = $this->render('MajoredInMainBundle:SiteMapXml:main.xml.twig');
        return $response;
    }
    
    public function locationsAction()
    {
        $locationManager = $this->get('mi_search.location.manager');
        $locations = $locationManager->findLocations();
        
        $newyork = $locationManager->findLocationByName('New York, NY');
        $maxPopulation = $newyork->getPopulation();
        
        $response = $this->render('MajoredInMainBundle:SiteMapXml:locations.xml.twig', array(
                'locations' => $locations,
                'maxPopulation' => $maxPopulation
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
            $weight = $major->getPopularity() / $maxPopularity;
        }
        else {
            $weight = 1;
        }
        $weight = ($weight > 1) ? 1 : $weight;
        
        $locationCount = round(1000 * $weight) + 50; //dilute length of sitemap by importance to limit robot crawling.
        
        $locationManager = $this->get('mi_search.location.manager');
        $locations = $locationManager->findLocationsLike('', $locationCount);
        
        $newyork = $locationManager->findLocationByName('New York, NY');
        $maxPopulation = $newyork->getPopulation();
        
        $response = $this->render('MajoredInMainBundle:SiteMapXml:majors.xml.twig', array(
                'major' => $major,
                'locations' => $locations,
                'majorWeight' => $weight,
                'maxPopulation' => $maxPopulation
        ));

        return $response;
    }
}
