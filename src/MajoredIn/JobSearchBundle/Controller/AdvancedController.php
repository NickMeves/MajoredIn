<?php

namespace MajoredIn\JobSearchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdvancedController extends Controller
{
    public function indexAction()
    {        
        $queryString = array_merge($this->container->getParameter('mi_search.advanced_search.default_params'), $this->get('request')->query->all());
        
        $response = $this->render('MajoredInJobSearchBundle:Advanced:advanced.html.twig', array(
            'queryString' => $queryString,
            'defaults' => $this->container->getParameter('mi_search.advanced_search.default_params')
        ));

        return $response;
    }
    
    public function submitAction()
    {
        $post = $this->get('request')->request->all();        
        $defaults = $this->container->getParameter('mi_search.advanced_search.default_params');
        
        foreach ($post as $key => $value) {
            if($value == $defaults[$key]) {
                unset($post[$key]);
            }
        }
    
        if (!isset($post['major']) || $post['major'] == '') {
            $post['major'] = 'undeclared';
        }
        $post['major'] = JobSearchController::dash($post['major']);
    
    
        if (!isset($post['location']) || $post['location'] == '') {
            $post['location'] = 'everywhere';
        }
        $post['location'] = JobSearchController::dash($post['location']);
    
        $response = $this->redirect($this->generateUrl('mi_jobs_results', $post, true), 301);
        return $response;
    }
}
