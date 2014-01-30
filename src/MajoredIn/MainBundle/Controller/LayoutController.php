<?php

namespace MajoredIn\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LayoutController extends Controller
{
    public function headerAction($major = 'undeclared', $location = 'everywhere')
    {
        $layoutCache = $this->get('liip_doctrine_cache.ns.layout');
        if (!($menu = $layoutCache->fetch('main-header'))) {
            ob_start();
            $this->get('mi_wordpress.wordpress_api')->wp_nav_menu(array(
                    'container' => 'div',
                    'container_class' => 'nav-collapse collapse',
                    'theme_location' => 'header-menu',
                    'menu_class' => 'nav',
                    'items_wrap' => '<ul class="%2$s">%3$s</ul>'
            ));
            $menu = ob_get_clean();
            $layoutCache->save('main-header', $menu);
        }
        
        $response = $this->render('MajoredInMainBundle:Layout:header.html.twig', array('menu' => $menu, 'major' => $major, 'location' => $location));
        return $response;
    }
    
    public function footerAction()
    {
        $layoutCache = $this->get('liip_doctrine_cache.ns.layout');
        $wordpressApi = $this->get('mi_wordpress.wordpress_api');
        
        for ($i = 1; $i <= 3; ++$i) {
            if (!($menu = $layoutCache->fetch('main-footer'.$i))) {
                ob_start();
                $wordpressApi->wp_nav_menu(array(
                        'container' => 'div',
                        'container_class' => 'span3',
                        'theme_location' => 'footer-menu'.$i,
                        'menu_class' => 'unstyled',
                        'items_wrap' => '<ul class="%2$s">%3$s</ul>'
                ));
                $menu = ob_get_clean();
                $layoutCache->save('main-footer'.$i, $menu);
            }
            $menus[] = $menu;
        }
    
        $response = $this->render('MajoredInMainBundle:Layout:footer.html.twig', array('menus' => $menus));
        return $response;
    }
}
