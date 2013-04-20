<?php

namespace MajoredIn\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomepageController extends Controller
{
    public function indexAction()
    {
        //redirect away from weird facebook link querystring for homepage (can tie analytics logic in later)
        $queryString = $this->get('request')->query->all();
        if (count($queryString) > 0) {
            $response = $this->redirect($this->generateUrl('mi_main_homepage', array(), true), 301);
            return $response;
        }
        
        if (in_array($this->get('kernel')->getEnvironment(), array('mobile', 'mobile_dev'))) {
            $response = $this->render('MajoredInMainBundle:Homepage:homepage.mobile.twig');
        }
        else {
            $response = $this->render('MajoredInMainBundle:Homepage:homepage.html.twig');
        }

        $response->setSharedMaxAge(3600);
        return $response;
    }
}
