<?php get_header(); ?>
<div id="content">
    <div class="container">
        <div class="row">
            <div id="main" class="span8">
                <div class="archive-top-header">
                    <div class="breadcrumbs">
                        <a href="<?php echo bloginfo('url'); ?>">MajoredIn</a>
                        <span> / </span>
                        <h1>News &amp; Articles</h1>
                    </div>
                </div>
            <?php if (have_posts()) : ?>
                <?php /* Start the Loop */ ?>
                <?php while (have_posts()) : the_post(); ?>
                <div class="archive-entry">
                    <div class="archive-header">
                        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium', array('class' => 'archive-image visible-phone')); ?></a>
                        <h2 class="thin"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <div>
                            <span>By </span><a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author"><?php echo get_the_author(); ?></a><span> | <?php the_date(); ?></span>
                        </div>
                    </div>
                    <div class="row hidden-phone">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="span2"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium', array('class' => 'archive-image')); ?></a></div>
                        <div class="span6"><?php the_excerpt(); ?></div>
                    <?php else : ?>
                        <div class="span8"><?php the_excerpt(); ?></div>
                    <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; ?>
                <?php mi_numeric_posts_nav(); ?>
            <?php else : ?>
                <?php //get_template_part( 'no-results', 'archive' ); ?>
            <?php endif; ?>
            </div>
            <?php get_sidebar(); ?>
        </div>
    </div>
</div>
<?php get_footer(); ?>