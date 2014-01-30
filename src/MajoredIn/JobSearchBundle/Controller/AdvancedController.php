<?php

namespace MajoredIn\JobSearchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdvancedController extends Controller
{   
    public function submitAction()
    {
        $post = $this->get('request')->request->all();        
        $defaults = $this->container->getParameter('mi_search.advanced_search.default_params');
        $canonicalizer = $this->get('mi_search.canonicalizer');
        
        foreach ($post as $key => $value) {
            if($value == $defaults[$key]) {
                unset($post[$key]);
            }
        }
    
        if (!isset($post['major']) || $post['major'] == '') {
            $post['major'] = 'undeclared';
        }
        $post['major'] = $canonicalizer->dash($post['major']);
    
    
        if (!isset($post['location']) || $post['location'] == '') {
            $post['location'] = 'everywhere';
        }
        $post['location'] = $canonicalizer->dash($post['location']);
    
        $response = $this->redirect($this->generateUrl('mi_jobs_results', $post, true), 301);
        return $response;
    }
}
