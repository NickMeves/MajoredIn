<?php get_header(); ?>
<div id="content">
    <div class="container">
        <div class="row">
            <div id="main" class="span8 article">
            <?php while (have_posts()) : the_post(); ?>
                <?php global $mi_current_ID; $mi_current_ID = get_the_ID(); ?>
                <div class="article-header">
                    <div class="breadcrumbs">
                        <a href="<?php echo bloginfo('url'); ?>">MajoredIn</a>
                        <span> / </span>
                        <?php if (get_option('show_on_front') == 'page') { echo '<a href="' . get_permalink(get_option('page_for_posts')) . '">News &amp; Articles</a><span> / </span>'; } ?>
                        <?php if ($categories = get_the_category()) { echo '<a href="' . get_category_link( $categories[0]->term_id ) . '">' . $categories[0]->cat_name . '</a><span> / </span>'; } ?>
                        <span><?php the_title(); ?></span>
                    </div>
                    <h1 class="article-title thin"><?php the_title(); ?></h1>
                    <div>
                        <span>By </span>
                        <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
				            <?php echo get_the_author(); ?>
			            </a>
                        <span> | <?php the_date(); ?></span>
                    </div>
                    <div class="social-wrapper-article">
                    <?php 
                        global $kernel;
                        echo $kernel->getContainer()->get('templating')->render('MajoredInMainBundle:Social:socialshare.html.twig', array('shareUrl' => get_permalink()));
                    ?>
                    </div>
                </div>
                <div>
                    <?php if (has_post_thumbnail()) { the_post_thumbnail('medium', array('class' => 'featured-image')); } ?>
                    <?php the_content(); ?>
                </div>
                <?php mi_link_pages(); ?>
                <?php if (is_single() && get_the_author_meta('description')) : ?>
            		<?php get_template_part('author-bio'); ?>
            	<?php endif; ?>
            <?php endwhile; ?>
            <?php mi_attribution(false); ?>
            </div>
            <?php get_sidebar(); ?>
        </div>
    </div>
</div>
<?php get_footer(); ?>