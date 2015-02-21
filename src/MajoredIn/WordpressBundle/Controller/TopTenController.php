<?php

namespace MajoredIn\WordpressBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class TopTenController extends Controller
{
    public function countAction($id, $blogId, $activateCounter)
    {   
        $wordpressApi = $this->get('mi_wordpress.wordpress_api');
        
        $wordpressApi->inScope(function () use ($id, $blogId, $activateCounter) {
            global $wpdb;
            
            $table_name = $wpdb->base_prefix . "top_ten";
            $top_ten_daily = $wpdb->base_prefix . "top_ten_daily";
        
            if ($id > 0) {
                if ((1 == $activateCounter) || (11 == $activateCounter)) {
                    $wpdb->query($wpdb->prepare("INSERT INTO {$table_name} (postnumber, cntaccess, blog_id) VALUES('%d', '1', '%d') ON DUPLICATE KEY UPDATE cntaccess= cntaccess+1 ", $id, $blogId));
                }
                if ((10 == $activateCounter) || (11 == $activateCounter)) {
                    $current_date = gmdate('Y-m-d H', current_time('timestamp', 0));
                    $wpdb->query($wpdb->prepare( "INSERT INTO {$top_ten_daily} (postnumber, cntaccess, dp_date, blog_id) VALUES('%d', '1', '%s', '%d' ) ON DUPLICATE KEY UPDATE cntaccess= cntaccess+1 ", $id, $current_date, $blogId));
                }
            }
            
            return;
        });
        
        $success = new Response();
        $success->setStatusCode('204');
        return $success;
    }
}
