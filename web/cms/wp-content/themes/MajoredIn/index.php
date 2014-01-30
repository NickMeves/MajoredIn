<?php get_header(); ?>
<div id="content">
    <div class="container">
        <div class="row">
            <div id="main" class="span8">
            <?php if ( have_posts() ) : ?>
                <?php /* Start the Loop */ ?>
                <?php while ( have_posts() ) : the_post(); ?>
            
                <h1 class="thin"><?php the_title(); ?></h1>
                <div class="article">
                    <?php the_post_thumbnail('thumbnail', array('class' => 'featured-image')); ?>
                    <?php the_excerpt(); ?>
                </div>
            
                <?php endwhile; ?>
            <?php else : ?>
                <?php //get_template_part( 'no-results', 'archive' ); ?>
            <?php endif; ?>
            
            </div>
            <?php get_sidebar(); ?>
        </div>
    </div>
</div>
<?php get_footer(); ?>