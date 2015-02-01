<?php

namespace MajoredIn\JobSearchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use \DOMDocument;

class LayoutController extends Controller
{
    public function sidebarAction()
    {
        $layoutCache = $this->get('liip_doctrine_cache.ns.layout');
        if (!($sidebar = $layoutCache->fetch('jobs-sidebar'))) {
            ob_start();
            $this->get('mi_wordpress.wordpress_api')->inScope(function () {
                if ( is_active_sidebar('Jobs Sidebar')) {
                    dynamic_sidebar('Jobs Sidebar');
                }
            });
            $sidebar = ob_get_clean();
            $layoutCache->save('jobs-sidebar', $sidebar, 3600); //give age so popular posts can update every now and then
        }
    
        $response = new Response($sidebar);
        return $response;
    }
    
    public function popupAction()
    {
        $layoutCache = $this->get('liip_doctrine_cache.ns.layout');
        if (!($popup = $layoutCache->fetch('jobs-popup'))) {
            ob_start();
            $this->get('mi_wordpress.wordpress_api')->inScope(function () {
                if ( is_active_sidebar('Jobs Popup')) {
                    dynamic_sidebar('Jobs Popup');
                }
            });
            $popup = ob_get_clean();
            $layoutCache->save('jobs-popup', $popup);
        }
        
        $dom = new DOMDocument();
        $dom->loadXML($popup);
        $dom->preserveWhiteSpace = false;
        $titles = $dom->getElementsByTagName('title');
        foreach ($titles as $title) {
            $title_elem = $title->nodeValue;
        }
        $bodies = $dom->getElementsByTagName('body');
        foreach ($bodies as $body) {
            $body_elem = $body->nodeValue;
        }
        
        $response = $this->render('MajoredInJobSearchBundle:Modal:share.html.twig', array('title' => $title_elem, 'body' => $body_elem));
        return $response;
    }
    
    public function footerAction()
    {
        $layoutCache = $this->get('liip_doctrine_cache.ns.layout');
        if (!($footer = $layoutCache->fetch('jobs-footer'))) {
            ob_start();
            $this->get('mi_wordpress.wordpress_api')->inScope(function () {
                if ( is_active_sidebar('Jobs Footer')) {
                    dynamic_sidebar('Jobs Footer');
                }
            });
            $footer = ob_get_clean();
            $layoutCache->save('jobs-footer', $footer);
        }
    
        $response = new Response($footer);
        return $response;
    }
}
