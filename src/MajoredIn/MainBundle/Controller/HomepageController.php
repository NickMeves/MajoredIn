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

        $response->setSharedMaxAge(3600);
        return $response;
    }
}
