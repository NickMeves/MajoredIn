<?php

namespace MajoredIn\JobSearchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use MajoredIn\JobSearchBundle\Search\JobQueryInterface;
use MajoredIn\JobSearchBundle\Search\JobQueryFactoryInterface;
use MajoredIn\JobSearchBundle\Search\JobApiConnectorInterface;
use MajoredIn\JobSearchBundle\Search\JobResults;
use MajoredIn\JobSearchBundle\Exception\NoResultsException;
use MajoredIn\JobSearchBundle\Exception\InvalidParamException;
use MajoredIn\JobSearchBundle\Exception\LocationRedirectException;
use MajoredIn\JobSearchBundle\Exception\GatewayTimeoutException;

class JobSearchController extends Controller
{
    public function resultsAction($major, $location)
    {
        $request = $this->get('request');
        $queryString = $request->query->all();
        $canonicalizer = $this->get('mi_search.canonicalizer');
        
        $majorUrl = $major;
        $locationUrl = $location;
        
        $major = $canonicalizer->undash($major);
        $location = $canonicalizer->undash($location);
        
        try {
            $jobQuery = $this->get('mi_search.job_query.factory')->createFromRequest($request, $major, $canonicalizer->canonicalize($location));
        }
        catch (\Exception $e) {
            $this->get('logger')->err('JobSearchController::resultsAction: Exception caught running JobQueryFactory::createFromRequest.  URI: ' . $request->getRequestUri());
            $response = $this->render('TwigBundle:Exception:error.html.twig');
            $response->setStatusCode('503');
            $response->headers->set('Retry-After', '3600');
            return $response;
        }
        
        $jobQueryParams = $jobQuery->getParams();
        
        //urlBase includes previously set filters and search criteria but resets the page to 1 and removes comboxp0 filter.
        $pageUrlBase = array_merge($queryString, array('major' => $majorUrl, 'location' => $locationUrl));
        $urlBase = array_diff_assoc($pageUrlBase, array(
            'page' => isset($jobQueryParams['pn']) ? $jobQueryParams['pn'] : 1,
            'hidden' => isset($jobQueryParams['clst']) ? $jobQueryParams['clst'] : ''
        ));
        
        try {
            $jobResults = $this->get('mi_search.job_api_connector')->accessApi($jobQuery);
        }
        catch (NoResultsException $e) {
            if (isset($jobQueryParams['pn']) && $jobQueryParams['pn'] > 1) {
                try {
                    $jobQuery->addOptionalParam('pn', 1);
                    $jobResults = $this->get('mi_search.job_api_connector')->accessApi($jobQuery);
                }
                catch (NoResultsException $e) {
                    $variables = array(
                        'major' => $major,
                        'location' => $location,
                        'urlBase' => $urlBase,
                        'pageUrlBase' => $pageUrlBase
                    );
                    $response = $this->render('MajoredInJobSearchBundle:JobSearch:noresults.html.twig', $variables);
                    return $response;
                }
                
                $queryString['page'] = $jobResults->getMaxPage();
                $queryString['major'] = $majorUrl;
                $queryString['location'] = $locationUrl;
                
                $response = $this->redirect($this->generateUrl('mi_jobs_results', $queryString, true), 301);
                return $response;
            }
            else {
                //add to exclusion table if applicable
                $this->get('mi_search.exclude_queue')->add($request);
                
                $variables = array(
                    'major' => $major,
                    'location' => $location,
                    'urlBase' => $urlBase,
                    'pageUrlBase' => $pageUrlBase
                );
                $response = $this->render('MajoredInJobSearchBundle:JobSearch:noresults.html.twig', $variables);
                return $response;
            }
        }
        catch (InvalidParamException $e) {
            $this->get('logger')->err('JobSearchController::resultsAction: InvalidParamException caught running JobApiConnector::accessApi.  URI: ' . $request->getRequestUri());
            $response = $this->render('TwigBundle:Exception:error.html.twig');
            $response->setStatusCode('503');
            $response->headers->set('Retry-After', '3600');
            return $response;
        }
        catch (LocationRedirectException $e) {
            $location = $e->getLocation();
            $queryString['major'] = $majorUrl;
            $queryString['location'] = $canonicalizer->dash($location);
            $response = $this->redirect($this->generateUrl('mi_jobs_results', $queryString, true), 301);
            return $response;
        }
        catch (GatewayTimeoutException $e) {
            $this->get('logger')->err('JobSearchController::resultsAction: GatewayTimeoutException caught running JobApiConnector::accessApi.  URI: ' . $request->getRequestUri());
            $response = $this->render('MajoredInJobSearchBundle:JobSearch:timeout.html.twig');
            $response->setStatusCode('503');
            $response->headers->set('Retry-After', '3600');
            return $response;
        }
        catch (\Exception $e) {
            $this->get('logger')->err('JobSearchController::resultsAction: Exception caught running JobApiConnector::accessApi.  URI: ' . $request->getRequestUri());
            $response = $this->render('TwigBundle:Exception:error.html.twig');
            $response->setStatusCode('503');
            $response->headers->set('Retry-After', '3600');
            return $response;
        }
        
        $variables = array(
            'major' => $major,
            'location' => $location,
            'urlBase' => $urlBase,
            'pageUrlBase' => $pageUrlBase,
            'jobResults' => $jobResults,
            'queryString' => array_merge($this->container->getParameter('mi_search.advanced_search.default_params'), $queryString),
            'defaults' => $this->container->getParameter('mi_search.advanced_search.default_params')
        );
        $response = $this->render('MajoredInJobSearchBundle:JobSearch:results.html.twig', $variables);
        return $response;
    }
    
    public function resultsApiAction($major, $location)
    {
        $request = $this->get('request');
        $queryString = $request->query->all();
        $canonicalizer = $this->get('mi_search.canonicalizer');
    
        $majorUrl = $major;
        $locationUrl = $location;
    
        $major = $canonicalizer->undash($major);
        $location = $canonicalizer->undash($location);
        
        $response = new JsonResponse();
        if (isset($queryString['callback'])) {
            try {
                $response->setCallback($queryString['callback']);
            }
            catch (\Exception $e) {
                $this->get('logger')->err('JobSearchController::resultsApiAction: Exception caught due to invalid callback.  URI: ' . $request->getRequestUri());
            }
        }
    
        try {
            $jobQuery = $this->get('mi_search.job_query.factory')->createFromRequest($request, $major, $canonicalizer->canonicalize($location));
        }
        catch (\Exception $e) {
            $this->get('logger')->err('JobSearchController::resultsApiAction: Exception caught running JobQueryFactory::createFromRequest.  URI: ' . $request->getRequestUri());
            $response->setData(array());
            return $response;
        }
    
        try {
            $jobResults = $this->get('mi_search.job_api_connector')->accessApi($jobQuery);
        }
        catch (NoResultsException $e) {
            $jobQueryParams = $jobQuery->getParams();
            if (isset($jobQueryParams['pn']) && $jobQueryParams['pn'] > 1) {
                try {
                    $jobQuery->addOptionalParam('pn', 1);
                    $jobResults = $this->get('mi_search.job_api_connector')->accessApi($jobQuery);
                }
                catch (NoResultsException $e) {
                    $response->setData(array());
                    return $response;
                }
    
                $queryString['page'] = $jobResults->getMaxPage();
                $queryString['major'] = $majorUrl;
                $queryString['location'] = $locationUrl;
    
                $response = $this->redirect($this->generateUrl('mi_jobs_api_results', $queryString, true), 301);
                return $response;
            }
            else {
                $response->setData(array());
                return $response;
            }
        }
        catch (InvalidParamException $e) {
            $this->get('logger')->err('JobSearchController::resultsApiAction: InvalidParamException caught running JobApiConnector::accessApi.  URI: ' . $request->getRequestUri());
            $response->setData(array());
            return $response;
        }
        catch (LocationRedirectException $e) {
            $location = $e->getLocation();
            $queryString['major'] = $majorUrl;
            $queryString['location'] = $canonicalizer->dash($location);
            $response = $this->redirect($this->generateUrl('mi_jobs_api_results', $queryString, true), 301);
            return $response;
        }
        catch (GatewayTimeoutException $e) {
            $this->get('logger')->err('JobSearchController::resultsApiAction: GatewayTimeoutException caught running JobApiConnector::accessApi.  URI: ' . $request->getRequestUri());
            $response->setData(array());
            return $response;
        }
        catch (\Exception $e) {
            $this->get('logger')->err('JobSearchController::resultsApiAction: Exception caught running JobApiConnector::accessApi.  URI: ' . $request->getRequestUri());
            $response->setData(array());
            return $response;
        }
    
        foreach ($jobResults->getJobListings() as $jobListing) {
            $apiResults[] = array(
                'title'    => htmlspecialchars($jobListing->getTitle(), ENT_QUOTES),
                'company'  => htmlspecialchars($jobListing->getCompany(), ENT_QUOTES),
                'url'      => htmlspecialchars($jobListing->getUrl(), ENT_QUOTES),
                'type'     => htmlspecialchars($jobListing->getType(), ENT_QUOTES),
                'location' => htmlspecialchars($jobListing->getLocation(), ENT_QUOTES),
                'age'      => htmlspecialchars($jobListing->getAge(), ENT_QUOTES),
                'excerpt'  => htmlspecialchars(substr($jobListing->getExcerpt(), 0, -1), ENT_QUOTES) //fix ...> bug in simplyhired results
            );
        }

        $response->setData($apiResults);
        return $response;
    }
    
    public function preCacheAction($major, $location)
    {
        $request = $this->get('request');
        $queryString = $request->query->all();
        $canonicalizer = $this->get('mi_search.canonicalizer');
    
        $major = $canonicalizer->undash($major);
        $location = $canonicalizer->undash($location);
        
        $success = new Response();
        $success->setStatusCode('201');
        $fail = new Response();
        $fail->setStatusCode('204');
    
        try {
            $jobQuery = $this->get('mi_search.job_query.factory')->createFromRequest($request, $major, $canonicalizer->canonicalize($location));
        }
        catch (\Exception $e) {
            $this->get('logger')->err('JobSearchController::resultsAction: Exception caught running JobQueryFactory::createFromRequest.  URI: ' . $request->getRequestUri());
            return $fail;
        }
        
        $isCached = $this->get('mi_search.job_api_connector')->preCache($jobQuery);
        if ($isCached) {
            return $success;
        }
        else {
            return $fail;
        }
    }
    
    public function redirectAction()
    {
        $queryString = $this->get('request')->query->all();
        $canonicalizer = $this->get('mi_search.canonicalizer');
        
        if (!isset($queryString['major']) || $queryString['major'] == '') {
            $queryString['major'] = 'undeclared';
        }
        
        $queryString['major'] = $canonicalizer->dash($queryString['major']);
        
        
        if (!isset($queryString['location']) || $queryString['location'] == '') {
            $queryString['location'] = 'everywhere';
        }
        
        $queryString['location'] = $canonicalizer->dash($queryString['location']);
        
        $response = $this->redirect($this->generateUrl('mi_jobs_results', $queryString, true), 301);
        return $response;
    }
}
