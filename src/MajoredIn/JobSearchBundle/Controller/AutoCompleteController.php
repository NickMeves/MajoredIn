<?php

namespace MajoredIn\JobSearchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class AutoCompleteController extends Controller
{
    public function majorAction()
    {
        $queryString = $this->get('request')->query->all();
        $majorNames = array();
        $cache = false;
        if (isset($queryString['term'])) {
            $term = $queryString['term'];
            $limit = isset($queryString['limit']) ? $queryString['limit'] : $this->container->getParameter('mi_search.autocomplete.limit');
            
            $majorManager = $this->get('mi_search.major.manager');
            
            $majors = $majorManager->findMajorsLike($term, $limit);
            foreach ($majors as $major) {
                $cache = true;
                $majorNames[] = $major->getName();
            }
        }
        
        $response = new JsonResponse();
        $response->setData(array('term' => isset($queryString['term']) ? $queryString['term'] : '', 'data' => $majorNames));
        
        if (isset($queryString['callback'])) {
            try {
                $response->setCallback($queryString['callback']);
            }
            catch (\Exception $e) {
                $this->get('logger')->err('AutoCompleteController::majorAction: Exception caught due to invalid callback.  URI: ' . $request->getRequestUri());
            }
        }
        
        if ($cache) {
            $this->get('liip_doctrine_cache.ns.autocomplete')->save($this->get('request')->getRequestUri(), $response->getContent());
        }
        
        return $response;
    }
    
    public function locationAction()
    {
        $queryString = $this->get('request')->query->all();
        $locationNames = array();
        $cache = false;
        if (isset($queryString['term'])) {
            $term = $queryString['term'];
            $limit = isset($queryString['limit']) ? $queryString['limit'] : $this->container->getParameter('mi_search.autocomplete.limit');
            
            $locationManager = $this->get('mi_search.location.manager');
            
            $locations = $locationManager->findLocationsLike($term, $limit);
            foreach ($locations as $location) {
                $cache = true;
                $locationNames[] = $location->getName();
            }
        }
        
        $response = new JsonResponse();
        $response->setData(array('term' => isset($queryString['term']) ? $queryString['term'] : '', 'data' => $locationNames));
        
        if (isset($queryString['callback'])) {
            try {
                $response->setCallback($queryString['callback']);
            }
            catch (\Exception $e) {
                $this->get('logger')->err('AutoCompleteController::locationAction: Exception caught due to invalid callback.  URI: ' . $request->getRequestUri());
            }
        }
        
        if ($cache) {
            $this->get('liip_doctrine_cache.ns.autocomplete')->save($this->get('request')->getRequestUri(), $response->getContent());
        }
        
        return $response;
    }
}
