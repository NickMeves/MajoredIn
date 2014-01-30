<?php get_header(); ?>
<div id="content">
    <div class="container">
        <div class="row">
            <div id="main" class="span8 article">
            <?php while (have_posts()) : the_post(); ?>
                <div class="article-header">
                    <h1 class="article-title thin top"><?php the_title(); ?></h1>
                </div>
                <div>
                    <?php the_post_thumbnail('large', array('class' => 'featured-image')); ?>
                    <?php the_content(); ?>
                </div>
            <?php endwhile; ?>
            </div>
            <?php get_sidebar(); ?>
        </div>
    </div>
</div>
<?php get_footer(); ?>