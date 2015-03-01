<?php

namespace MajoredIn\JobSearchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class MajorGuideController extends Controller
{
    public function indexAction($major, $urlBase)
    {
        $majorGuideCache = $this->get('liip_doctrine_cache.ns.majorguide');
        
        unset($urlBase['location']);
        ksort($urlBase);
        $cacheKey = md5(serialize($urlBase));
        
        if ($guide = $majorGuideCache->fetch($cacheKey)) {
            return new Response($guide);
        }
        
        try {
            $majorEntity = $this->get('mi_search.major.manager')->findMajorByName($major);
            if (!$majorEntity) {
                $majorAliasEntity = $this->get('mi_search.major_alias.manager')->findMajorAliasByName($major);
                if ($majorAliasEntity) {
                    $majorEntity = $majorAliasEntity->getMajor();
                }
                else {
                    return new Response();
                }
            }
        }
        catch (\Exception $e) {
            return new Response();
        }
        
        $postId = $majorEntity->getPost();
        if (!$postId) {
            $majorGuideCache->save($cacheKey, '');
            return new Response();
        }

        $wordpressApi = $this->get('mi_wordpress.wordpress_api');
        
        $title = $majorEntity->getName();
        
        $wordpressApi->scopeOn();
            if ('publish' === get_post_status($postId)) {
                $post = get_post($postId);
                $excerpt = ($post->post_excerpt) ? $post->post_excerpt : wp_trim_words($post->post_content, 55, '');
                
                $permalink = get_permalink($postId);
                
                $locations = array();
                for ($i = 1; $i < 5; ++$i) {
                    $location = get_post_meta($postId, 'location'.$i, true);
                    if (trim($location) != '') {
                        $locations[] = $location;
                    }
                }
                $showGuide = true;
            }
            else {
                $showGuide = false;
            }
        $wordpressApi->scopeOff();
        
        if ($showGuide) {
            $variables = array(
                'major' => $title,
                'excerpt' => $excerpt,
                'permalink' => $permalink,
                'locations' => $locations,
                'urlBase' => $urlBase
            );
            
            $response = $this->render('MajoredInJobSearchBundle:JobSearch:majorguide.html.twig', $variables);
            $majorGuideCache->save($cacheKey, $response->getContent());
            return $response;
        }
        else {
            return new Response();
        }
    }
}
