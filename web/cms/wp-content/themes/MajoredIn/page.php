<?php get_header(); ?>
<div id="content">
    <div class="container">
        <div class="row">
            <div id="main" class="span8 article">
            <?php while (have_posts()) : the_post(); ?>
                <?php global $mi_current_ID; $mi_current_ID = get_the_ID(); ?>
                <div class="article-header">
                    <h1 class="article-title thin top"><?php the_title(); ?></h1>
                </div>
                <div>
                    <?php if (has_post_thumbnail()) { the_post_thumbnail('medium', array('class' => 'featured-image')); } ?>
                    <?php the_content(); ?>
                </div>
            <?php endwhile; ?>
            <?php mi_attribution(false); ?>
            </div>
            <?php get_sidebar(); ?>
        </div>
    </div>
</div>
<?php get_footer(); ?>