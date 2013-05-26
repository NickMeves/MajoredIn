<?php

namespace MajoredIn\JobSearchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
        
        $majorUrl = $major;
        $locationUrl = $location;
        
        $major = static::undash($major);
        $location = static::undash($location);
        
        try {
            $jobQuery = $this->get('mi_search.job_query.factory')->createFromRequest($request, $major, $this->get('mi_search.canonicalizer')->canonicalize($location));
        }
        catch (\Exception $e) {
            $this->get('logger')->err('JobSearchController::resultsAction: Exception caught running JobQueryFactory::createFromRequest.  URI: ' . $request->getRequestUri());
            $response = $this->render('TwigBundle:Exception:error.html.twig');
            $response->setStatusCode('500');
            return $response;
        }
        
        $jobQueryParams = $jobQuery->getParams();
        
        //urlBase includes previously set filters and search criteria but resets the page to 1 and removes comboxp0 filter.
        $pageUrlBase = array_merge($queryString, array('major' => $majorUrl, 'location' => $locationUrl));
        $urlBase = array_diff_assoc($pageUrlBase, array(
            'page' => isset($jobQueryParams['pn']) ? $jobQueryParams['pn'] : 1,
            'hidden' => isset($jobQueryParams['clst']) ? $jobQueryParams['clst'] : ''
        ));
        
        $advancedBase = $urlBase;
        $advancedBase['major'] = $major;
        $advancedBase['location'] = $location;
        $advancedBase = array_diff_assoc($advancedBase, array(
            'major' => 'undeclared',
            'location' => 'everywhere'
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
                        'pageUrlBase' => $pageUrlBase,
                        'advancedBase' => $advancedBase
                    );
                    $response = $this->render('MajoredInJobSearchBundle:JobSearch:noresults.html.twig', $variables);
                }
                
                $queryString['page'] = $jobResults->getMaxPage();
                $queryString['major'] = $majorUrl;
                $queryString['location'] = $locationUrl;
                
                $response = $this->redirect($this->generateUrl('mi_jobs_results', $queryString, true), 301);
            }
            else {
                $variables = array(
                    'major' => $major,
                    'location' => $location,
                    'urlBase' => $urlBase,
                    'pageUrlBase' => $pageUrlBase,
                    'advancedBase' => $advancedBase
                );
                $response = $this->render('MajoredInJobSearchBundle:JobSearch:noresults.html.twig', $variables);
            }
            
            return $response;
        }
        catch (InvalidParamException $e) {
            $this->get('logger')->err('JobSearchController::resultsAction: InvalidParamException caught running JobApiConnector::accessApi.  URI: ' . $request->getRequestUri());
            $response = $this->render('TwigBundle:Exception:error.html.twig');
            $response->setStatusCode('500');
            return $response;
        }
        catch (LocationRedirectException $e) {
            $location = $e->getLocation();
            $queryString['major'] = $majorUrl;
            $queryString['location'] = static::dash($location);
            $response = $this->redirect($this->generateUrl('mi_jobs_results', $queryString, true), 301);
            return $response;
        }
        catch (GatewayTimeoutException $e) {
            $this->get('logger')->err('JobSearchController::resultsAction: GatewayTimeoutException caught running JobApiConnector::accessApi.  URI: ' . $request->getRequestUri());
            $response = $this->render('MajoredInJobSearchBundle:JobSearch:timeout.html.twig');
            $response->setStatusCode('504');
            return $response;
        }
        catch (\Exception $e) {
            $this->get('logger')->err('JobSearchController::resultsAction: Exception caught running JobApiConnector::accessApi.  URI: ' . $request->getRequestUri());
            $response = $this->render('TwigBundle:Exception:error.html.twig');
            $response->setStatusCode('500');
            return $response;
        }
        
        $variables = array(
            'major' => $major,
            'location' => $location,
            'urlBase' => $urlBase,
            'pageUrlBase' => $pageUrlBase,
            'advancedBase' => $advancedBase,
            'jobResults' => $jobResults
        );
        $response = $this->render('MajoredInJobSearchBundle:JobSearch:results.html.twig', $variables);
        return $response;
    }
    
    public function preCacheAction($major, $location)
    {
        $request = $this->get('request');
        $queryString = $request->query->all();
    
        $major = static::undash($major);
        $location = static::undash($location);
        
        $success = new Response();
        $success->setStatusCode('204');
        $fail = new Response();
        $fail->setStatusCode('500');
    
        try {
            $jobQuery = $this->get('mi_search.job_query.factory')->createFromRequest($request, $major, $this->get('mi_search.canonicalizer')->canonicalize($location));
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
        
        if (!isset($queryString['major']) || $queryString['major'] == '') {
            $queryString['major'] = 'undeclared';
        }
        
        $queryString['major'] = static::dash($queryString['major']);
        
        
        if (!isset($queryString['location']) || $queryString['location'] == '') {
            $queryString['location'] = 'everywhere';
        }
        
        $queryString['location'] = static::dash($queryString['location']);
        
        $response = $this->redirect($this->generateUrl('mi_jobs_results', $queryString, true), 301);
        return $response;
    }
    
    public static function dash($str)
    {
        $str = preg_replace('/-/', '_', $str); //allows - in queries
        
        $str = preg_replace('/\s+/', ' ', $str);
        $str = preg_replace('/^\s/', '', $str);
        $str = preg_replace('/\s$/', '', $str);
        $str = preg_replace('/\s+/', '-', $str);
        
        $str = preg_replace('/\//', '', $str); //fixes / and route issues.
        
        return $str;
    }
    
    public static function undash($str)
    {
        $str = preg_replace('/-/', ' ', $str);
        $str = preg_replace('/_/', '-', $str);
        return $str;
    }
}
