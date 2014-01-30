<div class="well">
    <h3 class="author-title thin top"><?php printf( 'About %s', get_the_author() ); ?></h3>
	<div class="row">
		<div class="span1 author-avatar"><?php echo get_avatar( get_the_author_meta( 'user_email' )); ?></div>
		<div class="span6">
		    <p class="author-bio"><?php the_author_meta( 'description' ); ?></p>
			<div>
			    <a class="author-link" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
				    <?php printf('More Posts by %s &raquo;', get_the_author() ); ?>
			    </a>
			</div>
		</div>
	</div>
</div>