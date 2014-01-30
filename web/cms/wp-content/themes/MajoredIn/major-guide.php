<?php 
/*
 Template Name: Major Guide Template
*/
get_header(); ?>
<div id="content">
    <div class="container">
        <div class="row">
            <div id="main" class="span8 article">
            <?php while (have_posts()) : the_post(); ?>
                <div class="article-header">
                    <div class="breadcrumbs">
                        <a href="<?php echo bloginfo('url'); ?>">MajoredIn</a>
                        <span> / </span>
                        <?php $post = get_post(); if ($post->post_parent) { echo '<a href="' . get_permalink($post->post_parent) . '">' . get_the_title($post->post_parent) . '</a><span> / </span>'; } ?>
                        <span><?php the_title(); ?></span>
                    </div>
                    <h1 class="article-title thin"><?php the_title(); ?></h1>
                    <div class="social-wrapper-article">
                    <?php 
                        global $kernel;
                        echo $kernel->getContainer()->get('templating')->render('MajoredInMainBundle:Social:socialshare.html.twig', array('shareUrl' => get_permalink()));
                    ?>
                    </div>
                </div>
                <div>
                    <?php the_post_thumbnail('medium', array('class' => 'featured-image')); ?>
                    <?php the_content(); ?>
                </div>
            <?php endwhile; ?>
            <?php if ( function_exists('photodropper_attribution') ) { photodropper_attribution(false); } ?>
            </div>
            <?php get_sidebar(); ?>
        </div>
    </div>
</div>
<?php get_footer(); ?>