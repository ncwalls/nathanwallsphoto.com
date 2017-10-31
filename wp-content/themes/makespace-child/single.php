<?php get_header(); ?>

	<div class="container">
		<?php while( have_posts() ): the_post(); ?>
			<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
				<h1><?php the_title(); ?></h1>
				<ul class="post-meta">
					<li><?php the_time( 'F j, Y' ); ?></li>
					<li><?php echo MakespaceFramework::read_time(); ?></li>
				</ul>
				<?php the_content(); ?>
				<footer>
					<ul>
						<li class="item prev">
							<?php if( get_previous_post() ): $prev = get_previous_post(); ?>
								<a href="<?php echo get_permalink( $prev->ID ); ?>"><?php echo $prev->post_title; ?></a>
							<?php endif; ?>
						</li>
						<li class="item next">
							<?php if( get_next_post() ): $next = get_next_post(); ?>
							<a href="<?php echo get_permalink( $next->ID ); ?>"><?php echo $next->post_title; ?></a>
							<?php endif; ?>
						</li>
						<li>
							<a href="<?php echo get_permalink(get_option('page_for_posts')); ?>">Back to All</a>
						</li>
					</ul>
				</footer>
			</article>
		<?php endwhile; ?>
	</div>

<?php get_footer();