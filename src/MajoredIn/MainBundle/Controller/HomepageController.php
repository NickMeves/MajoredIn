<?php

namespace MajoredIn\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomepageController extends Controller
{
    public function indexAction()
    {
        if (in_array($this->get('kernel')->getEnvironment(), array('mobile', 'mobile_dev'))) {
            $response = $this->render('MajoredInMainBundle:Homepage:homepage.mobile.twig');
        }
        else {
            $response = $this->render('MajoredInMainBundle:Homepage:homepage.html.twig');
        }
        $response->setPublic();
        $response->setMaxAge(86400);
        $response->setSharedMaxAge(86400);
        return $response;
    }
}
