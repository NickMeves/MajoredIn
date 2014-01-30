<?php

namespace MajoredIn\WordpressBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WordpressController extends Controller
{
    public function indexAction()
    {
        $response = new StreamedResponse();
        $wordpressApi = $this->get('mi_wordpress.wordpress_api');
        
        //TODO: PHP 5.4+ can use $this in closure
        $response->setCallback(function () use ($wordpressApi) {
            define('WP_USE_THEMES', true);
            $wordpressApi->inScope(function () {
                $wp_did_header = true;
                wp();
                require_once( ABSPATH . WPINC . '/template-loader.php' );
            });
        });
        return $response;
    }
    
    public function adminAction()
    {
        $response = $this->redirect('/cms/wp-admin/index.php', 301);
        return $response;
    }
}
