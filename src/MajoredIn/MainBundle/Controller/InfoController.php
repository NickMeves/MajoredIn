<?php

namespace MajoredIn\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class InfoController extends Controller
{
    public function aboutAction()
    {
        $response = $this->render('MajoredInMainBundle:Info:about.html.twig');
        $response->setPublic();
        $response->setMaxAge(86400);
        $response->setSharedMaxAge(86400);
        return $response;
    }
    
    public function faqAction()
    {
        $response = $this->render('MajoredInMainBundle:Info:faq.html.twig');
        $response->setPublic();
        $response->setMaxAge(86400);
        $response->setSharedMaxAge(86400);
        return $response;
    }
    
    public function privacyAction()
    {
        $response = $this->render('MajoredInMainBundle:Info:privacy.html.twig');
        $response->setPublic();
        $response->setMaxAge(86400);
        $response->setSharedMaxAge(86400);
        return $response;
    }
    
    public function termsAction()
    {
        $response = $this->render('MajoredInMainBundle:Info:terms.html.twig');
        $response->setPublic();
        $response->setMaxAge(86400);
        $response->setSharedMaxAge(86400);
        return $response;
    }
    
    public function sitemapAction()
    {
        $majorManager = $this->get('mi_search.major.manager');
        $majors = $majorManager->findMajors();
        
        $majorNames = array();
        foreach ($majors as $major) {
            $majorNames[] = $major->getName();
        }
        sort($majorNames);
        
        $response = $this->render('MajoredInMainBundle:Info:sitemap.html.twig', array(
            'majors' => $majorNames
        ));
        $response->setPublic();
        $response->setMaxAge(86400);
        $response->setSharedMaxAge(86400);
        return $response;
    }
    
    public function attributionAction()
    {
        $response = $this->render('MajoredInMainBundle:Info:attribution.html.twig');
        $response->setPublic();
        $response->setMaxAge(86400);
        $response->setSharedMaxAge(86400);
        return $response;
    }
}
