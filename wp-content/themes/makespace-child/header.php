<!DOCTYPE html>
<html lang="en-US" class="no-js">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<?php wp_head(); ?>
		<!--[if lt IE 9]>
			<script src="//oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
			<script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body <?php body_class(); ?>>
		<?php if( 'ocn' == get_field( 'menu_type', 'option' ) ): ?>
			<div id="ocn">
				<div id="ocn-inner">
					<div id="ocn-top">
						<a href="<?php echo home_url(); ?>" title="<?php bloginfo( 'name' ); ?>" id="ocn-brand">
							<img src="<?php the_field( 'site_logo', 'option' ); ?>" alt="<?php bloginfo( 'name' ); ?>">
						</a>
						<button class="nav-toggle" type="button" id="ocn-close">
							<span></span>
						</button>
					</div>
					<?php wp_nav_menu( array(
						'container' => 'nav',
						'container_id' => 'ocn-nav-primary',
						'theme_location' => 'primary',
						'before' => '<span class="ocn-link-wrap">',
						'after' => '<span class="ocn-sub-menu-button"></span></span>'
					) ); ?>
				</div>
			</div>
		<?php endif; ?>
		<header class="site-header">
			<div class="inner container">
				<div class="left">
					<a href="<?php echo home_url(); ?>" title="<?php bloginfo( 'name' ); ?>" class="brand">
						<img src="<?php the_field( 'site_logo', 'option' ); ?>" alt="<?php bloginfo( 'name' ); ?>">
					</a>
				</div>
				<div class="right">
					<?php
						wp_nav_menu( array(
							'container' => 'nav',
							'container_id' => 'large-nav-primary',
							'theme_location' => 'primary'
						) );
					?>
					<button class="nav-toggle" type="button" id="nav-toggle">
						<span>menu</span>
					</button>
				</div>
			</div>
			<?php
				if( 'dropdown' == get_field( 'menu_type', 'option' ) ){
					wp_nav_menu( array(
						'container' => 'nav',
						'container_id' => 'dropdown-nav-primary',
						'theme_location' => 'primary'
					) );
				}
			?>
		</header>
		<?php
			if( function_exists( 'yoast_breadcrumb' ) ){
				yoast_breadcrumb( '<div id="breadcrumbs">', '</div>' );
			}
		?>