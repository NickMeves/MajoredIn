<?php

function mi_setup() {
    remove_action('wp_head', 'feed_links', 2);
    remove_action('wp_head', 'feed_links_extra', 3);
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
    
	/**
     * Add default posts and comments RSS feed links to head
     */
    add_theme_support('automatic-feed-links');

    /**
     * Enable support for Post Thumbnails on posts and pages
     *
     * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
     */
    add_theme_support('post-thumbnails');
    
    /**
     * Enable excerpts on pages
     */
    add_post_type_support('page', 'excerpt');

    /**
     * Enable support for Post Formats
     */
    //add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link' ) );

	register_nav_menus(array(
	    'header-menu'  => __('Header Menu'),
	    'footer-menu1' => __('Footer Menu Column 1'),
	    'footer-menu2' => __('Footer Menu Column 2'),
	    'footer-menu3' => __('Footer Menu Column 3')
	));
}
add_action('after_setup_theme', 'mi_setup');

function mi_deregister_styles() {
    wp_deregister_style('pdrp_styles');
    // deregister as many stylesheets as you need...
}
add_action('wp_print_styles', 'mi_deregister_styles', 100);

/**
*function mi_deregister_scripts() {
*    wp_deregister_script('jquery');
*}
*add_action('wp_enqueue_scripts', 'mi_deregister_scripts', 100);
*/

function mi_wp_title($title, $sep) {
	global $paged, $page;

	if (is_feed())
		return $title;

	// Add the site name.
	$title .= get_bloginfo('name');

	// Add the site description for the home/front page.
	$site_description = get_bloginfo('description', 'display');
	if ($site_description && (is_home() || is_front_page())) {
		$title = "$title $sep $site_description";
	}

	// Add a page number if necessary.
	if ($paged >= 2 || $page >= 2) {
		$title = "$title $sep " . sprintf('Page %s', max($paged, $page));
	}

	return $title;
}
add_filter('wp_title', 'mi_wp_title', 10, 2);

function mi_excerpt_more($more) {
    return '&hellip; <a class="read-more" href="'. get_permalink(get_the_ID()) . '">Read More</a>';
}
add_filter('excerpt_more', 'mi_excerpt_more', 10, 1);

function mi_remove_widget_title($widget_title) {
    if (substr($widget_title, 0, 1) === '!') {
        return;
    }
    else {
        return $widget_title;
    }
}
add_filter('widget_title', 'mi_remove_widget_title', 10, 1);

function mi_widgets_init() {
	register_sidebar(array(
		'name'          => 'Blog Sidebar',
		'id'            => 'blog-sidebar',
		'description'   => 'Appears on blog in the sidebar.',
		'before_widget' => '<div class="module">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="thin">',
		'after_title'   => '</h4>',
	));
	
	register_sidebar(array(
    	'name'          => 'Jobs Sidebar',
    	'id'            => 'jobs-sidebar',
    	'description'   => 'Appears on jobs in the sidebar.',
    	'before_widget' => '<div class="module">',
    	'after_widget'  => '</div>',
    	'before_title'  => '<h4 class="thin">',
    	'after_title'   => '</h4>',
	));
	
	register_sidebar(array(
    	'name'          => 'Jobs Footer',
    	'id'            => 'jobs-footer',
    	'description'   => 'Widget area in jobs footer for adsense',
    	'before_widget' => '<div>',
    	'after_widget'  => '</div>',
    	'before_title'  => '<!--',
    	'after_title'   => '-->',
	));
	
	register_sidebar(array(
    	'name'          => 'Jobs Popup',
    	'id'            => 'jobs-popup',
    	'description'   => 'Widget for easy editing of jobs spam popup',
    	'before_widget' => '<?xml version="1.0" encoding="UTF-8"?><popup><title><![CDATA[',
    	'after_widget'  => ']]></body></popup>',
    	'before_title'  => '',
    	'after_title'   => ']]></title><body><![CDATA[',
	));
	
	register_sidebar(array(
    	'name'          => 'Homepage Jumbotron',
    	'id'            => 'homepage-jumbotron',
    	'description'   => 'Homepage Jumbotron Area (HTML Only)',
    	'before_widget' => '<div class="jumbotron">',
    	'after_widget'  => '</div>',
    	'before_title'  => '<h1>',
    	'after_title'   => '</h1>',
	));
	
	register_sidebar(array(
    	'name'          => 'Homepage Marketing',
    	'id'            => 'homepage-marketing',
    	'description'   => 'Homepage Marketing (Put 3 span4 Widgets Here)',
    	'before_widget' => '<div class="span4">',
    	'after_widget'  => '</div>',
    	'before_title'  => '<h3>',
    	'after_title'   => '</h3>',
	));
	
	register_sidebar(array(
    	'name'          => 'Homepage Bottom',
    	'id'            => 'homepage-bottom',
    	'description'   => 'Homepage Bottom (Put 2 span6 Widgets Here)',
    	'before_widget' => '<div class="span6">',
    	'after_widget'  => '</div>',
    	'before_title'  => '<h3 class="text-center">',
    	'after_title'   => '</h3>',
	));
}
add_action('widgets_init', 'mi_widgets_init');

function mi_categories($categories, $separator = ', ') {
    $output = '';
    
    if ($categories) {
        foreach ($categories as $category) {
            $output .= '<a href="' . get_category_link( $category->term_id ) . '" title="' . esc_attr(sprintf("View all posts in the %s category", $category->name)) . '">' . $category->cat_name . '</a>' . $separator;
        }
        echo substr($output, 0, 0 - strlen($separator));
    }
}

function mi_tags($tags, $separator = ', ') {
    $output = '';

    if ($tags) {
        foreach ($tags as $tag) {
            $output .= '<a href="' . get_tag_link( $tag->term_id ) . '" title="' . esc_attr(sprintf("View all posts with the %s tag", $tag->name)) . '">' . $tag->name . '</a>' . $separator;
        }
        echo substr($output, 0, 0 - strlen($separator));
    }
}

function mi_numeric_posts_nav() {
    
    if (is_singular()) {
        return;
    }

    global $wp_query;

    /** Stop execution if there's only 1 page */
    if ($wp_query->max_num_pages <= 1) {
        return;
    }

    $paged = get_query_var('paged') ? absint(get_query_var('paged')) : 1;
    $max   = intval($wp_query->max_num_pages);

    if ($paged < 4) {
        $links = range(1, (5 < $max) ? 5 : $max);
    }
    elseif ($paged > $max - 2) {
        $links = range((1 > $max - 4) ? 1 : $max - 4, $max);
    }
    else {
        $links = range($paged - 2, $paged + 2);
    }

    echo '<div class="pagination"><ul>' . "\n";

    /**	Previous Post Link */
    if (get_previous_posts_link()) {
        printf( '<li>%s</li>' . "\n", get_previous_posts_link('&laquo;<span class="hidden-phone"> Prev</span>') );
    }

    /**	Link to current page, plus 2 pages in either direction if necessary */
    foreach ($links as $link) {
        $class = $paged == $link ? ' class="active"' : '';
        printf('<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url(get_pagenum_link($link)), $link);
    }

    /**	Next Post Link */
    if (get_next_posts_link()) {
        printf('<li>%s</li>' . "\n", get_next_posts_link('<span class="hidden-phone">Next </span>&raquo;'));
    }

    echo '</ul></div>' . "\n";
}

function mi_link_pages() {
    $pagination = wp_link_pages(array(
        'before' => '<div class="pagination"><ul>',
        'after'  => '</ul></div>',
        'separator' => '</li><li>',
        'echo' => 0
    ));
    
    $pagination = preg_replace('|<ul></li>|i', '<ul>', $pagination);
    $pagination = preg_replace('|</ul>|i', '</li></ul>', $pagination);
    $pagination = preg_replace('|<li>(\d+)</li>|i', '<li class="active"><a href="#">\1</a></li>', $pagination);
    
    $prevnext = wp_link_pages(array(
        'before' => '',
        'after' => '',
        'next_or_number' => 'next',
        'nextpagelink' => 'NEXT',
		'previouspagelink' => 'PREV',
        'echo' => 0
    ));
    
    $prev = preg_filter('|^.*?<a href="([^"]*?)">PREV</a>.*$|i', '<li><a href="\1">&laquo;<span class="hidden-phone"> Prev</span></a></li>', $prevnext);
    $next = preg_filter('|^.*?<a href="([^"]*?)">NEXT</a>.*$|i', '<li><a href="\1"><span class="hidden-phone">Next </span>&raquo;</a></li>', $prevnext);
    
    $pagination = preg_replace('|<ul>|i', '<ul>' . $prev, $pagination);
    $pagination = preg_replace('|</ul>|i', $next . '</ul>', $pagination);
    
    echo $pagination;
}

function mi_attribution($extended = true) {
    ob_start();
    photodropper_attribution($extended);
    $attribution = ob_get_clean();
    
    $attribution = preg_replace('/<div id="pdrp_tagAttribution">/', '<div class="image-attribution">', $attribution);
    $attribution = preg_replace('/photo/', 'Photo', $attribution);
    $attribution = preg_replace('/by :/', 'by:', $attribution);
    
    echo $attribution;
}

/**
 * CACHE CLEARING HOOKS
 */
function mi_permalink_change($old, $new)
{
    global $kernel;
    $kernel->getContainer()->get('liip_doctrine_cache.ns.majorguide')->deleteAll();
}
add_action('update_option_permalink_structure', 'mi_permalink_change', 10, 2);

function mi_widget_change()
{
    global $kernel;
    $kernel->getContainer()->get('liip_doctrine_cache.ns.layout')->deleteAll();
}
add_filter('sidebar_admin_setup', 'mi_widget_change');

function mi_menu_change($id, $data = null)
{
    global $kernel;
    $kernel->getContainer()->get('liip_doctrine_cache.ns.layout')->deleteAll();
}
add_action('wp_update_nav_menu', 'mi_menu_change', 10, 2);

/**
 * CONNECTOR BETWEEN MAJOR GUIDES AND JOB SEARCH
 */
function mi_guide_meta_update($metaId, $postId, $key, $value)
{
    if ($key != 'major' || get_post_type($postId) != 'page') {
        return;
    }

    $major = $value;
    
    global $kernel;
    $majorManager = $kernel->getContainer()->get('mi_search.major.manager');
    
    if (null === $majorEntity = $majorManager->findMajorByName($major)) {
        return;
    }
    
    if ($majorEntity->getPost() != $postId) {
        $majorEntity->setPost($postId);
        $majorManager->updateMajor($majorEntity);
        $kernel->getContainer()->get('liip_doctrine_cache.ns.majorguide')->deleteAll();
    }
}
add_action('added_post_meta', 'mi_guide_meta_update', 10, 4);
add_action('updated_post_meta', 'mi_guide_meta_update', 10, 4);