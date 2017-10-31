<?php
/*----------------------------------------------------------------------------------------------------
  Standard error logging function for debugging WordPress
----------------------------------------------------------------------------------------------------*/
if( !function_exists( 'write_log' ) ){
	function write_log( $log ){
		if( true === WP_DEBUG ){
			if( is_array( $log ) || is_object( $log ) ){
				error_log( print_r( $log, true ) );
			} else {
				error_log( $log );
			}
		}
	}
}

class MakespaceFramework {
	function __construct(){
		include_once( get_template_directory() . '/functions-pluggable.php' );
		include_once( get_template_directory() . '/includes/acf/acf-makespace.php' );
		include_once( get_template_directory() . '/includes/tgmpa/class-tgm-plugin-activation.php' );

		add_action( 'acf/init', array( $this, 'acf_map_key' ) );
		add_action( 'acf/save_post', array( $this, 'flush_rewrite_after_register_post_types' ), 20 );
		//add_action( 'admin_bar_menu', array( $this, 'admin_bar_menu' ), 100 );
		add_action( 'admin_head', array( $this, 'do_favicon' ) );
		add_action( 'admin_init', array( $this, 'disable_yoast_notifications' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );
		add_action( 'after_switch_theme', array( $this, 'after_switch_theme' ) );
		add_action( 'login_enqueue_scripts', array( $this, 'login_enqueue_scripts' ) );
		add_action( 'login_head', array( $this, 'do_favicon' ) );
		add_action( 'phpmailer_init', array( $this, 'phpmailer_init' ) );
		add_action( 'init', array( $this, 'check_required_plugins' ) );
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'save_post', array( $this, 'live_reload' ) );
		add_action( 'tgmpa_register', array( $this, 'tgmpa_register' ) );
		add_action( 'woocommerce_after_main_content', array( $this, 'woocommerce_after_main_content' ), 10 );
		add_action( 'woocommerce_before_main_content', array( $this, 'woocommerce_before_main_content' ), 10 );
		add_action( 'woocommerce_before_main_content', array( $this, 'woocommerce_yoast_breadcrumbs' ), 20 );
		add_action( 'wp_dashboard_setup', array( $this, 'wp_dashboard_setup' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 9 );
		add_action( 'wp_head', array( $this, 'do_favicon' ) );
		add_action( 'wp_head', array( $this, 'javascript_detection' ), 0 );
		add_action( 'wp_head', array( $this, 'wp_head' ) );
		add_action( 'wp_footer', array( $this, 'wp_footer' ) );

		add_filter( 'admin_body_class' , array( $this, 'admin_body_class' ) );
		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ) );
		add_filter( 'auto_core_update_send_email', '__return_false' );
		add_filter( 'auto_update_core', '__return_true' );
		add_filter( 'auto_update_plugin', '__return_true' );
		add_filter( 'auto_update_theme', '__return_true' );
		add_filter( 'body_class', array( $this, 'body_class' ) );
		add_filter( 'default_page_template_title', array( $this, 'default_page_template_title' ) );
		add_filter( 'embed_oembed_html', array( $this, 'embed_oembed_html' ), 99, 4 );
		add_filter( 'gform_confirmation_anchor', '__return_true' );
		add_filter( 'gform_init_scripts_footer', '__return_true' );
		add_filter( 'gform_submit_button', array( $this, 'gform_submit_button' ), 10, 2 );
		add_filter( 'get_next_post_sort', array( $this, 'get_next_post_sort' ) );
		add_filter( 'get_next_post_where',  array( $this, 'get_next_post_where' ) );
		add_filter( 'get_previous_post_sort', array( $this, 'get_previous_post_sort' ) );
		add_filter( 'get_previous_post_where', array( $this, 'get_previous_post_where' ) );
		add_filter( 'script_loader_tag', array( $this, 'script_loader_tag' ), 10, 2 );
		add_filter( 'show_admin_bar', '__return_false' );
		add_filter( 'tablepress_use_default_css', '__return_false' );
		add_filter( 'the_generator', '__return_false' );
		add_filter( 'tiny_mce_plugins', array( $this, 'disable_emojicons_tinymce' ) );
		add_filter( 'woocommerce_breadcrumb_defaults', array( $this, 'woocommerce_breadcrumb_defaults' ) );
		add_filter( 'woocommerce_enqueue_styles', '__return_false' );
		add_filter( 'wpseo_breadcrumb_output_wrapper', array( $this, 'wpseo_breadcrumb_output_wrapper' ) );
		add_filter( 'wpseo_breadcrumb_links', array( $this, 'wpseo_breadcrumb_links' ) );
		add_filter( 'wpseo_breadcrumb_separator', array( $this, 'wpseo_breadcrumb_separator' ) );
		add_filter( 'wpseo_breadcrumb_single_link', array( $this, 'wpseo_breadcrumb_single_link' ), 10, 2 );
		add_filter( 'wpseo_breadcrumb_single_link_wrapper', array( $this, 'wpseo_breadcrumb_single_link_wrapper' ) );
		add_filter( 'wpseo_metabox_prio', array( $this, 'wpseo_metabox_prio' ) );

		add_shortcode( 'protected_email', array( $this, 'sc_protected_email' ) );
		add_shortcode( 'makespace_sitemap', array( $this, 'sc_makespace_sitemap' ) );

		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
		if( function_exists( 'yoast_breadcrumb' ) ){
			remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
		}
		remove_action( 'wp_head', 'feed_links' );
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wp_shortlink_wp_head' );
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
		remove_action( 'wp_head', 'print_emoji_detection_script' , 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
	}

	function acf_map_key(){
		$google_api_key = get_field( 'msw_google_map_api_key', 'option' );
		if( $google_api_key ){
			acf_update_setting( 'google_api_key', $google_api_key );
		} elseif( defined( 'GOOGLE_MAPS_API_KEY' ) ) {
			acf_update_setting( 'google_api_key', GOOGLE_MAPS_API_KEY );
		}
	}

	function admin_bar_menu( $wp_admin_bar ){
		$makespace_theme_config = get_option( 'makespace_theme_config', array() );
		$development_mode = array_key_exists( 'development_mode', $makespace_theme_config ) ? $makespace_theme_config[ 'development_mode' ] : false;

		$args = array(
				'id'    => 'development_mode',
				'title' => 'Development Mode',
				'href' => 'https://www.google.com',
				'meta' => array(
				)
		);

		if( false == $development_mode ){
			$args[ 'meta' ][ 'class' ] = 'dev-off';
		} else {
			$args[ 'meta' ][ 'class' ] = 'dev-on' ;
		}


		$wp_admin_bar->add_menu( $args );
	}

	function admin_body_class( $classes ){
		return $classes;
	}

	function admin_footer_text( $text ){
		$text = 'Powered by <a href="https://www.makespaceweb.com" target="_blank">Makespace</a>';
		return $text;
	}

	function admin_menu(){
		acf_add_options_page( array(
			'page_title' => 'Makespace Theme Options',
			'menu_title' => 'Makespace',
			'icon_url' => 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE5LjIuMSwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPgo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzExIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCIKCSB2aWV3Qm94PSIwIDAgNjQgNTAiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDY0IDUwOyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+CjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+Cgkuc3Qwe2ZpbGwtcnVsZTpldmVub2RkO2NsaXAtcnVsZTpldmVub2RkO2ZpbGw6I0Y4OTcxRDt9Cjwvc3R5bGU+CjxnPgoJPGc+CgkJPHBhdGggY2xhc3M9InN0MCIgZD0iTTYwLjgsMy42Yy0wLjUsMC41LTEsMS0xLjYsMS4zYy0wLjEsMC4xLTIuMSwxLjMtMi40LDEuNWMtMC4zLDAuMi0wLjUsMC4zLTAuOCwwLjRjLTAuNiwwLjQtMS4zLDAuNy0yLDEKCQkJYy0wLjUsMC4yLTEsMC41LTEuNSwwLjZjLTAuMiwwLjEtMy4zLDEuNS0zLjgsMS43YzAuMiwwLjIsMS45LDAuOSwzLjEsMmMwLjQtMC41LDIuMy0zLjYsMi41LTMuOGMwLjIsMCwwLjMsMCwwLjQsMAoJCQlDNTQuOSw4LDU1LjMsNy45LDU1LjYsOGMwLjEsMCwwLjIsMC4yLDAuMiwwLjJjMC4yLDAuMiwxLjksMS4xLDIuMSwxLjJjMC41LDAuMiwxLjEsMC40LDEuNiwwLjdjMC40LDAsMC42LDAuMSwwLjgsMC40CgkJCWMwLjUsMC40LDEuNywxLDIsMS4yYzAsMC45LDAsMiwwLDIuOGMtMC4xLDAtMC4xLDAuMi0wLjIsMC4yYy0wLjEsMC0wLjEtMC4xLTAuMi0wLjJjLTAuMS0wLjEtMC4yLTAuMi0wLjItMC4yCgkJCWMtMC4xLTAuMywwLjEtMC4zLDAuMi0wLjVjMC4xLTAuNC0wLjItMS4xLTAuMS0xLjZjLTAuMS0wLjEtMS42LTAuOS0xLjktMWMtMC4zLTAuMS0wLjUtMC4zLTAuNy0wLjVjLTAuNC0wLjItMi40LTEuMS0yLjUtMS4yCgkJCWMtMC4zLTAuMS0wLjQtMC4zLTAuNy0wLjNjLTAuMSwwLTAuMywwLTAuNC0wLjFjMCwwLTAuMy0wLjItMC4zLTAuMmMtMC4yLDAuMS0wLjgsMS0xLDEuM2MtMC4xLDAuMi0xLjcsMi4zLTEuNiwyLjcKCQkJYzAuMSwwLjQsMC42LDAuOCwwLjcsMS4yYzAuMiwwLjUsMC4yLDEsMC4yLDEuNWMwLDAuNS0wLjIsMS4xLTAuNCwxLjZjLTAuMiwwLjUtMC41LDAuOC0wLjcsMS4yYy0wLjMsMC42LTAuNywxLTEuNCwxLjEKCQkJYy0wLjMsMC40LTAuNywwLjctMC42LDEuNGMwLjIsMC40LDAuNSwxLjMsMS4xLDEuNWMwLjksMCwxLjctMC44LDIuNSwwYzAuNiwwLDEtMC40LDEuMy0wLjdjMC40LTAuMiwwLjktMC40LDEuNC0wLjYKCQkJYzAuMSwwLjEsMC4zLDAuMiwwLjYsMC4zYzAuNCwwLjYsMC45LDEuMSwxLjMsMS43YzAsMC4xLDAsMC4yLDAsMC4yYzAuOCwwLjksMS4yLDIuMSwxLjgsMy4yYzAsMC42LDAuNCwwLjksMC4zLDEuNgoJCQljLTAuMSwwLjEtMC4yLDAuMS0wLjQsMC4xYzAsMC4zLDAuMywwLjMsMC40LDAuNWMwLDAuNCwwLjEsMC45LTAuMiwxLjFjLTAuMSwwLjEtMC4xLDAuOC0wLjIsMC45Yy0wLjEsMC4zLTAuMiwwLjUtMC4yLDAuOAoJCQljLTAuMiwwLjgtMC42LDEuNi0wLjksMi40YzAsMC4xLTAuMSwwLjQtMC4xLDAuNWMtMC4xLDAuMS0wLjIsMC4xLTAuMywwLjFjLTAuMSwwLjUsMC4yLDEuMS0wLjEsMS40YzAuMSwwLjMsMCwwLjYtMC4xLDAuOAoJCQljMC4xLDAuMywwLjIsMC41LDAuMiwwLjhjMC4xLDAuMywwLjUsMC4zLDAuNSwwLjdjMC4xLDAuMSwwLjYsMC45LDAuOCwxLjFjMC4xLDAuNywwLjgsMC45LDEuMiwxLjVjLTAuMSwwLjMtMC40LDAtMC42LDAuMQoJCQljLTAuMiwwLTAuNCwwLjItMC40LDAuNWMtMC40LTAuNS0wLjEtMS41LTAuNi0xLjhjMC0wLjQtMC43LTEuMy0wLjktMS40Yy0wLjIsMC4xLTAuMywwLjMtMC40LDAuNWMtMC4yLTAuMi0wLjEtMC41LTAuMi0wLjgKCQkJYy0wLjEtMC4yLTAuNC0wLjQtMC41LTAuNmMtMC4xLTAuMS0wLjItMC4xLTAuMy0wLjNjLTAuMi0wLjItMC40LTAuNy0wLjQtMWMwLTAuMSwwLjEtMC4yLDAuMS0wLjNjMC0wLjEtMC4xLTAuMi0wLjEtMC4zCgkJCWMwLTAuMiwwLjItMC4zLDAuMS0wLjZjMC0wLjItMC4yLTAuMy0wLjEtMC40YzAtMC41LDAuMi0wLjksMC4zLTEuNGMwLjEtMC45LTAuMS0xLjktMC4zLTIuOGMtMC4xLTAuMy0wLjQtMS0wLjMtMS4zCgkJCWMwLTAuMSwwLjEtMC4xLDAuMi0wLjJjMC0wLjEtMC4xLTAuMi0wLjItMC4zYy0wLjQsMC4yLTAuMywwLjgtMC41LDAuOWMtMC4xLTAuNS0wLjEtMS4yLDAuMy0xLjRjLTAuMi0xLjEtMC42LTEuOS0wLjktMi44CgkJCWMtMC4yLTAuMS0wLjItMC44LTAuNS0wLjdjLTAuMiwwLjQtMSwxLTEuNCwxLjNjLTAuNC0wLjItMC41LTAuMS0wLjktMC4yYy0wLjQtMC4yLTAuNi0wLjgtMC45LTAuOWMtMC40LDAtMS4yLDAuOC0xLjgsMC43CgkJCWMtMC4xLDAtMC4zLTAuMS0wLjQtMC4xYy0wLjItMC4xLTAuNS0wLjEtMC41LTAuNWMtMC4yLDAtMC4yLDAuMi0wLjQsMC4yYy0wLjMtMC4zLTAuNC0wLjgtMC43LTEuMmMwLDAuMS0xLjMsMC44LTEuNCwwLjkKCQkJYy0wLjEsMC4xLTEuOSwxLTIsMWMwLjEsMSwwLjQsMiwwLjUsM2MwLjIsMiwxLjIsMy41LDEuMSw0LjhjMCwwLjEtMC4xLDAuMi0wLjEsMC4zYy0wLjEsMC4yLTAuMywwLjQtMC4xLDAuNgoJCQljMCwwLjIsMC4yLDAuNywwLjMsMC45YzAsMC4xLDAsMC4yLDAsMC4yYzAsMC4xLDAuMiwwLjIsMC4yLDAuM2MwLjQsMC4zLDEuMSwwLjIsMS40LDAuNmMwLjIsMCwwLjUsMC4xLDAuOCwwCgkJCWMwLjEsMC4zLDAuMSwwLjgsMCwxLjFjLTAuMi0wLjItMC40LTAuNS0wLjYtMC44Yy0wLjEsMC0wLjEsMC0wLjIsMGMtMC4yLTAuMi0xLjItMC42LTEuNS0wLjZjLTAuMS0wLjItMC4xLTAuMy0wLjItMC41CgkJCWMtMC41LDAtMC42LTEuMS0wLjYtMS4xYy0wLjItMC4zLTAuNS0wLjMtMC43LTAuNmMtMC40LTAuNi0wLjQtMS4xLTAuNS0xLjVjLTAuMi0wLjktMC41LTIuMS0wLjYtM2MwLTAuMi0wLjEtMS0wLjItMS4xCgkJCWMtMC4yLTAuMS0wLjItMS44LTAuMy0yLjJjLTEuMiwwLjMtMi44LDAuMy00LDBjMC40LDAuNiwwLjcsMS41LDEuMywyYzAuMiwwLjgsMC42LDEuMywxLDJjMCwwLjEsMCwwLjEsMCwwLjIKCQkJYzAuMSwwLjIsMC4yLDAuNCwwLjMsMC42YzAuMiwwLjYsMCwxLjUsMC4zLDIuMWMwLjEsMC4yLDAuMywwLjMsMC4yLDAuNmMtMC4yLDAtMC4zLTAuMi0wLjMtMC4zYy0wLjEsMC0wLjMsMC0wLjQtMC4xCgkJCWMwLTAuOC0wLjMtMS41LTAuMS0yLjNjLTAuMS0wLjItMC4yLTAuNS0wLjItMC43Yy0wLjMsMC4xLTAuNi0xLjItMC45LTEuOGMtMC4xLDAtMC4yLDAtMC4yLDBjLTAuMy0wLjUtMi42LTMuNC0yLjgtMy41CgkJCWMtMC4xLDAtMC4yLDAuMi0wLjIsMC4yYy0wLjEsMC44LTAuMywxLjYtMC4yLDIuNWMwLDAuMS0wLjEsMC4xLTAuMiwwLjJjMCwwLjIsMCwwLjIsMCwwLjRjLTAuMSwwLjEtMC40LDAuOS0wLjgsMQoJCQljLTAuMiwwLTAuNiwwLjctMC43LDAuOGMtMC4yLDAuMy0wLjksMC43LTEuMiwwLjhjMSwxLjEsMS42LDIsMS44LDIuMmMwLjMsMC40LDAuOSwxLjcsMS4xLDEuOWMwLjUsMC42LDAuOSwxLjQsMS4zLDIuMQoJCQljMC4zLDAuNSwwLjksMC42LDAuOSwxLjNjMC4zLDAsMC41LDAuMiwwLjgsMC4yYzAuNiwwLjgsMS43LDEuMywyLjYsMS43YzAuNiwwLjMsMSwwLjksMC45LDEuNmMwLDAuNS0wLjUsMC44LTAuNiwxLjEKCQkJYy0wLjEsMC4yLDAuNiwwLjQsMC43LDAuNGMwLjMsMC4xLDEsMC41LDEuMSwwLjdjMC4xLDAuMiwwLjgsMC43LDAuOSwxYzAuMSwwLjMsMC4yLDAuNywwLjMsMWMwLDAuMywwLjIsMC40LDAuMywwLjYKCQkJYzAuMSwwLjIsMCwwLjUsMC4xLDAuN2MwLjIsMC40LDAuMywwLjgsMC42LDEuMWMwLjMsMC4zLDAuNiwwLjUsMC44LDAuOGMtMC4yLDAuMS0wLjUsMC4yLTAuNywwLjJjLTAuMSwwLjMtMC4zLDAuNC0wLjQsMC43CgkJCWMtMC4zLTAuNC0wLjMtMS0wLjQtMS41QzQ1LjQsNDgsNDUuMiw0Ny40LDQ1LDQ3Yy0wLjEtMC4yLTAuMi0wLjMtMC4zLTAuNmMtMC4xLTAuNC0wLjItMC45LTAuNC0xLjJjLTAuMi0wLjQtMC42LTAuOC0xLTEKCQkJYy0wLjItMC4xLTEtMC41LTEuMS0wLjZjLTAuMi0wLjEtMC40LTAuMi0wLjYtMC40Yy0wLjIsMC4xLTAuMywwLjQtMC42LDAuNGMtMC4zLDAuMS0wLjYsMC0xLDBjLTAuMywwLTAuOS0wLjUtMS4xLTAuNgoJCQljLTAuNC0wLjMtMC44LTAuNi0xLjMtMC44Yy0wLjUtMC4yLTAuOS0wLjUtMS40LTAuOGMtMC4yLTAuMS0wLjQtMC4yLTAuNS0wLjRjLTAuMS0wLjItMC4xLTAuNC0wLjItMC42Yy0wLjIsMC0wLjUsMC4yLTAuOCwwLjIKCQkJYy0wLjIsMC0wLjItMC4yLTAuNC0wLjNjLTAuMiwwLTAuNCwwLjItMC42LDAuMWMtMC4xLDAuMS0wLjEsMC4zLTAuMSwwLjZjLTAuMywwLjEtMC4zLDAuNS0wLjQsMC43Yy0wLjEsMC4zLTAuMywwLjYtMC41LDAuOQoJCQljLTAuMiwwLjMtMC41LDAuNC0wLjgsMC41Yy0wLjMsMC4yLTAuNiwwLjUtMC44LDAuN2MtMC4xLDAuMi0wLjksMC42LTAuOCwwLjhjMCwwLjItMC4zLDAuNi0wLjQsMC42YzAtMC4xLDAtMC4zLDAtMC41CgkJCWMtMC4xLTAuMi0wLjUtMC4yLTAuNy0wLjNjMC4xLDAsMC45LTAuNCwxLTAuNWMwLjMtMC4yLDAuNi0wLjQsMC44LTAuNmMwLjEtMC4xLDAuMS0wLjMsMC4yLTAuM2MwLjEtMC4xLDAuNi0wLjQsMC43LTAuNAoJCQljMC41LTAuMywwLjYtMC41LDAuOS0xYzAuMS0wLjIsMC4yLTAuMywwLjMtMC41YzAuMS0wLjIsMC0wLjUsMC4yLTAuNmMtMC4xLTAuMy0wLjItMC41LTAuMy0wLjdjLTAuMS0wLjEtMS4xLTEuNi0xLjItMi4xCgkJCWMwLTAuMS0wLjEsMC0wLjItMC4xYy0wLjgtMS40LTAuNS00LTAuNC00LjFjMC4xLTAuNCwwLjEtMC43LDAuMi0xLjFjMC0wLjEtMC4yLTAuMS0wLjMtMC4yYy0wLjItMC4zLDAuMi0wLjksMC4zLTEuMgoJCQljMC4xLTAuMiwwLjQtMC45LDAuNi0wLjljMC4xLTAuNSwwLjItMC45LDAuMi0xLjVjMC4zLTAuMSwxLjktMSwyLjMtMS40Yy0wLjEtMC4yLTAuMi0wLjItMC4yLTAuNGMwLTAuMywwLTAuNiwwLTAuOQoJCQljLTAuNSwwLjEtMSwwLjUtMS4yLDAuOGMtMC41LDAuNS0xLDAuOS0xLjUsMS40Yy0wLjMsMC4yLTEuMSwwLjktMS45LDEuNGMtMC44LDAuNS0xLDAuOS0xLjYsMS40Yy0wLjksMC43LTIuNSwxLjQtMy4xLDEuOAoJCQljLTAuMywwLjItMC44LDAuNy0xLjQsMS4xYy0wLjksMC41LTIuMSwwLjgtMi41LDFjLTAuNywwLjMtMS40LDAuNy0yLjEsMC45Yy0xLjUsMC41LTMuMiwwLjMtNC42LTAuNWMtMC41LTAuMy0xLjEtMC42LTEuNC0xLjEKCQkJYy0wLjMtMC42LTAuMy0xLjctMC4zLTIuNGMwLTAuNCwwLjItMS40LDAuMy0yLjNjMC4xLTEtMC4xLTAuOSwwLTEuNGMwLjEtMC43LDAuNy0yLjMsMC45LTIuOGMwLjItMC40LDAuNC0xLjUsMC42LTEuOQoJCQljMC40LTAuOSwwLjgtMS44LDEuNC0yLjZjMC41LTAuOCwxLjEtMS42LDEuNy0yLjRjMC42LTAuOCwxLjItMS42LDEuOS0yLjJjMC40LTAuNCwwLjktMC42LDEuNC0wLjljMC40LTAuMywwLjktMC42LDEuNC0wLjgKCQkJYzAuMi0wLjEsMC41LTAuMSwwLjctMC4yYzAuMSwwLDAuNS0wLjQsMC42LTAuNGMtMC44LTAuMS0xLjctMC4xLTIuNiwwYy0wLjcsMC4xLTEuNCwwLTIsMC4xYy0xLjQsMC4xLTIuOSwwLTQuMy0wLjEKCQkJYy0wLjMsMC0wLjcsMC0xLTAuMWMtMC40LTAuMS0wLjctMC4xLTEuMi0wLjJjLTAuNy0wLjEtMS4zLTAuNC0xLjgtMC44Yy0yLjIsMC4yLTQuNywwLTYuMi0wLjZjLTAuNC0wLjEtMC43LTAuMi0xLjEtMC40CgkJCWMtMi0xLTIuNC0yLjEtMi43LTIuOGMtMC4yLTAuNy0wLjMtMS4xLDAuMS0xLjhjMC4yLTAuNCwwLjktMC44LDEuMy0wLjlDMi4zLDYuMSwzLDYsMy43LDUuOGMwLjUtMC4yLDEuMS0wLjEsMS42LTAuMgoJCQlDNi42LDUuNSw3LjgsNS40LDksNS40YzEuMywwLDIuNSwwLDMuOCwwLjFjMC43LDAuMSwxLjMsMC4xLDIsMC4yYzEuMywwLjIsMi40LDAuNCwzLjYsMC42YzIuOCwwLjUsNS41LDEuNSw4LjIsMi41CgkJCWMwLjgsMC4zLDEuNSwwLjYsMi4zLDFjMC40LDAuMiwwLjgsMC40LDEuMiwwLjZjMC4yLDAuMSwxLjksMS4xLDIsMS4xYzAuOSwwLDQsMC43LDQuMiwwLjhjMSwwLjEsMy4xLTAuOSw1LjQtMC45CgkJCWMwLjEtMC4xLDAuMy0wLjksMS40LTEuNGMwLjYtMC4yLDEuMi0wLjQsMi0wLjNjMC4yLTEuMy0wLjMtMi41LTAuNC0zLjhjLTAuMy0wLjEtMi40LDEuMi0yLjUsMS4zYy0wLjEsMC4xLTAuMSwwLjItMC4yLDAuMwoJCQljLTAuMSwwLjEtMC4zLDAuMi0wLjMsMC4zYzAsMC4xLDAsMC4yLDAsMC4yYzAsMC4xLTAuNCwwLjktMC42LDEuMWMwLDAuMiwwLDAuNSwwLDAuN2MtMC4zLDAuMS0wLjQsMC4yLTAuNiwwLjQKCQkJYy0wLjEtMC4xLTAuMi0wLjMtMC4yLTAuNGMtMC4xLTAuNCwwLjMtMC41LDAuNC0wLjhjMC0wLjQsMC40LTEuMSwwLjQtMS4xYzAuMS0wLjIsMC4yLTAuNSwwLjMtMC43QzQxLjIsNyw0MS4zLDcsNDEuNSw2LjkKCQkJYzAuMS0wLjEsMi4yLTEuMywyLjMtMS40QzQzLjksNS4yLDQ0LDUsNDQuMiw0LjljMC4yLTAuMSwwLjYsMC4xLDAuNywwLjRjLTAuMSwwLjQsMC4yLDAuNiwwLjMsMC44YzAuMywwLjUsMC4yLDEsMC4yLDEuOAoJCQljMCwwLjcsMC4yLDEuNSwwLjUsMmMwLjItMC4xLDAuNCwwLDAuNi0wLjFjMC40LTAuMSwyLTEuNSwyLjktMi4zYzQuMS0zLjQsOS4yLTYuMSwxMS4yLTYuOWMwLjgtMC40LDItMC42LDMuMS0wLjQKCQkJYzAuNiwwLjMtMC4xLDAuOC0wLjMsMS4xQzYzLjMsMS40LDYxLjEsMy40LDYwLjgsMy42eiBNNTYuNSwyMmMtMC4xLDAuMi0wLjksMC43LTEsMC44Yy0wLjIsMC4yLTAuMywwLjItMC4zLDAuNgoJCQljMC4xLDAuMiwwLjQsMC4zLDAuNCwwLjVjMC45LDAuNSwyLjEsMi43LDIuMSwzLjFjMC4zLDAuMiwwLjMsMC43LDAuMywxLjFjLTAuMSwwLjEtMC4yLDAuMS0wLjIsMC4yYzAsMC4xLDAuMSwwLjIsMC4xLDAuMwoJCQljMC4xLDAuMywwLDAuNiwwLDAuOWMwLDAuMywwLDAuNSwwLDAuOGMtMC4xLDEuNC0wLjQsMi43LTAuNiwzLjljLTAuMSwwLjEtMC4zLDAuMS0wLjQsMC4yYzAuMSwwLjMtMC4xLDAuNi0wLjEsMC44CgkJCWMwLDAuMiwwLjEsMC4zLDAsMC42YzAsMC4xLTAuMSwwLTAuMSwwLjFjMC4yLDAuNywwLjYsMS4yLDEuMywxLjNjLTAuMS0wLjcsMC0xLjEtMC4xLTEuN2MwLjMtMC4yLDAuMS0xLDAuMi0xLjUKCQkJYzAuMi0wLjIsMC40LTEuMSwwLjUtMS4zYzAuMy0xLDAuMy0yLjUsMC43LTMuM2MwLjEsMCwwLjIsMCwwLjMtMC4xYzAuMi0wLjMtMC4yLTAuNiwwLTAuOWMtMC4xLTAuMS0wLjMtMC4xLTAuNC0wLjIKCQkJYy0wLjEtMC4xLTAuMS0wLjItMC4yLTAuMmMtMC40LDAtMC4yLDAuOC0wLjMsMS4xYy0wLjItMC40LTAuMi0wLjktMC4xLTEuM2MwLTAuMSwwLjItMC41LDAuMy0wLjZjMC4xLTAuNC0wLjMtMS4zLTAuNS0xLjUKCQkJYy0wLjItMC41LTAuMy0xLjEtMC40LTEuNmMtMC4yLTAuOC0wLjYtMS42LTEtMi4yQzU2LjcsMjIsNTYuNiwyMiw1Ni41LDIyeiIvPgoJPC9nPgo8L2c+Cjwvc3ZnPgo=',
			'redirect' => false,
			'menu_slug' => 'msw-framework-options',
			'autoload' => true
		) );
		acf_add_options_sub_page( array(
			'page_title' => 'Custom Post Type Creator',
			'menu_title' => 'Post Types',
			'menu_slug' => 'msw-framework-post-types',
			'parent_slug' => 'msw-framework-options',
			'autoload' => true
		) );
		if( have_rows( 'msw_post_type', 'option' ) ){
			while( have_rows( 'msw_post_type', 'option' ) ){
				the_row();
				$public_information = get_sub_field( 'public_information' ) ? get_sub_field( 'public_information' ) : array();
				if( in_array( 'has_archive', $public_information ) ){
					$plural = get_sub_field( 'plural_name' );
					$singular = get_sub_field( 'singular_name' );
					$slug = get_sub_field( 'slug' );
					acf_add_options_sub_page( array(
						'page_title' => $singular . ' Archive Settings',
						'menu_title' => $singular . ' Settings',
						'menu_slug' => 'msw-' . $slug . '-archive-settings',
						'parent_slug' => 'edit.php?post_type=' . $slug
					) );
				}
			}
		}
	}

	function after_setup_theme(){
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		set_post_thumbnail_size( 2048, 2048 );
		add_theme_support( 'woocommerce' );
		register_nav_menus( array(
			'primary' => 'Primary Navigation'
		) );
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );
		if( class_exists( 'GFForms' ) ){
			$gf = ABSPATH . 'wp-content/plugins/gravityforms';
			$themes = get_option( 'gf_imported_theme_file', array() );
			$theme = get_template();
			if( !isset( $themes[ $theme ] ) ){
				require_once( $gf . '/export.php' );
				if( GFExport::import_file( get_template_directory() . '/includes/gravityforms/standard-contact-form.json' ) ){
					$themes[ $theme ] = true;
					update_option( 'gf_imported_theme_file', $themes );
				}
			}
		}
	}

	function after_switch_theme(){
		$makespace_theme_config = get_option( 'makespace_theme_config', array() );

		if( !array_key_exists( 'did_install_framework', $makespace_theme_config ) ){
			if( !get_page_by_path( 'home-page' ) ){
				$id = wp_insert_post( array(
					'menu_order' => 0,
					'post_content' => '',
					'post_name' => 'home-page',
					'post_title' => 'Home Page',
					'post_type' => 'page',
					'post_status' => 'publish'
				) );
				update_option( 'show_on_front', 'page' );
				update_option( 'page_on_front', $id );
			}
			if( !get_page_by_path( 'blog' ) ){
				$id = wp_insert_post( array(
					'menu_order' => 10,
					'post_name' => 'blog',
					'post_title' => 'Blog',
					'post_type' => 'page',
					'post_status' => 'publish'
				) );
				wp_delete_post( 1, true );
				update_option( 'page_for_posts', $id );
				update_option( 'close_comments_days_old', 1 );
				update_option( 'close_comments_for_old_posts', 1 );
				update_option( 'comment_moderation', 1 );
				update_option( 'comment_registration', 1 );
				update_option( 'comments_notify', 0 );
				update_option( 'default_comment_status', 'closed' );
				update_option( 'default_pingback_flag', 0 );
				update_option( 'default_ping_status', 'closed' );
				update_option( 'moderation_notify', 0 );
				update_option( 'show_avatars', 0 );
				update_option( 'thread_comments', 0 );
			}
			if( !get_page_by_path( 'contact' ) ){
				$contact_page_id = wp_insert_post( array(
					'menu_order' => 20,
					'post_name' => 'contact',
					'post_title' => 'Contact Us',
					'post_type' => 'page',
					'post_status' => 'publish'
				) );
				add_post_meta( $contact_page_id, '_wp_page_template', 'page_contact.php' );
			}
			if( !get_page_by_path( 'privacy-policy' ) ){
				$privacy_policy = file_get_contents( get_template_directory() . '/includes/prepackaged-content/privacy_policy.txt' );
				$privacy_policy = str_replace( '{{site_name}}', get_bloginfo( 'name' ), $privacy_policy );
				wp_insert_post( array(
					'menu_order' => 97,
					'post_content' => $privacy_policy,
					'post_name' => 'privacy-policy',
					'post_title' => 'Online Privacy Policy',
					'post_type' => 'page',
					'post_status' => 'publish'
				) );
			}
			if( !get_page_by_path( 'site-info' ) ){
				$site_info = file_get_contents( get_template_directory() . '/includes/prepackaged-content/site_info.txt' );
				$site_info = str_replace( '{{site_name}}', get_bloginfo( 'name' ), $site_info );
				$site_info = str_replace( '{{year}}', date( 'Y' ), $site_info );
				wp_insert_post( array(
					'menu_order' => 98,
					'post_content' => $site_info,
					'post_name' => 'site-info',
					'post_title' => 'Site Info',
					'post_type' => 'page',
					'post_status' => 'publish'
				) );
			}
			if( !get_page_by_path( 'site-map' ) ){
				wp_insert_post( array(
					'menu_order' => 99,
					'post_content' => '[makespace_sitemap]',
					'post_name' => 'site-map',
					'post_title' => 'Site Map',
					'post_type' => 'page',
					'post_status' => 'publish'
				) );
			}
			if( get_page_by_path( 'sample-page' ) ){
				$sample_page = get_page_by_path( 'sample-page' );
				wp_delete_post( $sample_page->ID, true );
				wp_delete_comment( 1, true );
			}
			$has_timezone = get_option( 'timezone_string' );
			if( !$has_timezone ){
				update_option( 'timezone_string', 'America/Kentucky/Louisville' );
			}

			$this->create_sample_posts();
			$makespace_theme_config = get_option( 'makespace_theme_config', array() );

			if( !get_option( 'rg_gforms_disable_css' ) ){
				update_option( 'rg_gforms_disable_css', 1 );
				update_option( 'rg_gforms_enable_html5', 1 );
			}
			if( !get_option( 'rg_gforms_key' ) && defined( 'GF_LICENSE_KEY' ) ){
				update_option( 'rg_gforms_key', GF_LICENSE_KEY );
			}
			if( !get_field( 'n9m_option_google_map_key', 'option' ) && defined( 'GOOGLE_MAPS_API_KEY' ) ){
				update_field( 'n9m_option_google_map_key', GOOGLE_MAPS_API_KEY, 'option' );
			}
			if( defined( 'ACF_PRO_LICENSE_KEY' ) && !get_option( 'acf_pro_license', false ) ){
				$acf_license = array(
					'key' => ACF_PRO_LICENSE_KEY,
					'url' => home_url()
				);
				update_option( 'acf_pro_license', base64_encode( maybe_serialize( $acf_license ) ) );
			}
			update_option( 'thumbnail_size_w', 300 );
			update_option( 'thumbnail_size_h', 300 );
			update_option( 'medium_size_w', 900 );
			update_option( 'medium_size_h', 900 );
			update_option( 'large_size_w', 2048 );
			update_option( 'large_size_h', 2048 );
			flush_rewrite_rules();
			$makespace_theme_config[ 'did_install_framework' ] = 1;
			update_option( 'makespace_theme_config', $makespace_theme_config );
		}
	}

	function body_class( $classes ){
		$classes[] = 'makespace';
		if( 'ocn' == get_field( 'menu_type', 'option' ) ){
			$classes[] = 'nav-ocn';
		}
		return $classes;
	}

	function check_required_plugins(){
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if( !is_plugin_active( 'advanced-custom-fields-pro/acf.php' ) ){
			$activate_acf = activate_plugin( 'advanced-custom-fields-pro/acf.php', null );
			add_action( 'admin_notices', function(){
				echo '<div class="notice notice-info is-dismissible"><p><strong>Advanced Custom Fields PRO</strong> is a required plugin for the Makespace theme and has been automatically activated.</p></div>';
			} );
		}
	}

	function create_sample_posts( $post_type  = 'post' ){
		$makespace_theme_config = get_option( 'makespace_theme_config', array() );
		$populated_post_types = array_key_exists( 'populated_post_types', $makespace_theme_config ) ? $makespace_theme_config[ 'populated_post_types' ] : array();
		if( !in_array( $post_type, $populated_post_types ) ){

			$blog_titles = array(
				'This Post Has a Very Long Title and Should Look Great on All Devices',
				'Short Article Title',
				'This Is a Fascinating Article About Us',
				'Post About What Is Happening In Our Industry',
				'Ten Things We Say In Sample Blog Posts',
				'This Post Has a Very Long Title and Should Look Great on All Devices',
				'Short Article Title',
				'This Is a Fascinating Article About Us',
				'Post About What Is Happening In Our Industry',
				'Ten Things We Say In Sample Blog Posts',
				'This Post Has a Very Long Title and Should Look Great on All Devices',
				'Short Article Title'
			);

			$unsplash_images = array();

			if( post_type_supports( $post_type, 'thumbnail' ) ){

				if( !function_exists( 'media_handle_upload' ) ){
					require_once( ABSPATH . 'wp-admin/includes/image.php' );
					require_once( ABSPATH . 'wp-admin/includes/file.php' );
					require_once( ABSPATH . 'wp-admin/includes/media.php' );
				}

				for ( $i = 0; $i < 3; $i++ ){
					$url = 'https://unsplash.it/1200/800/?random';
					$tmp = download_url( $url );

					if( is_wp_error( $tmp ) ){
						write_log( 'COULD NOT SET A FEATURED IMAGE' );
						@unlink( $tmp );
					} else {
						$unsplash_images[] = $tmp;
					}
				}
			}

			$set_menu_order = false;

			if( post_type_supports( $post_type, 'page-attributes' ) ){
				$set_menu_order = true;
			}

			$content = array();
			$content[] = file_get_contents( get_template_directory() . '/includes/prepackaged-content/post_html_markup.txt' );
			$content[] = file_get_contents( get_template_directory() . '/includes/prepackaged-content/post_image_alignment.txt' );
			$content[] = file_get_contents( get_template_directory() . '/includes/prepackaged-content/post_lorem.txt' );

			for( $i = 1; $i < 12; $i++ ){
				$random = rand( 0, count( $unsplash_images ) - 1 );
				$post_id = 0;

				$post_id = wp_insert_post( array(
					'menu_order' => $set_menu_order ? $i : 0,
					'post_content' => $content[ rand( 0, count( $content ) - 1 ) ],
					'post_date' => date( 'Y-m-d H:i:s', time() - ( $i * DAY_IN_SECONDS ) ),
					'post_title' => $blog_titles[ $i - 1 ],
					'post_type' => $post_type,
					'post_status' => 'publish'
				) );

				if( !empty( $unsplash_images ) && 0 != ( $i % 3 ) ){
					$desc = 'Sample Image #' . $i;
					$file_array = array(
						'name' => 'unsplash.jpeg',
						'tmp_name' => $unsplash_images[ $random ]
					);
					$attach_id = media_handle_sideload( $file_array, $post_id, $desc );
					set_post_thumbnail( $post_id, $attach_id );
				}
			}
			$populated_post_types[] = $post_type;
			$makespace_theme_config[ 'populated_post_types' ] = $populated_post_types;
			update_option( 'makespace_theme_config', $makespace_theme_config );
		}
	}

	function default_page_template_title(){
		return 'General Content';
	}

	function disable_emojicons_tinymce( $plugins ){
		if( is_array( $plugins ) ){
			return array_diff( $plugins, array( 'wpemoji' ) );
		} else {
			return array();
		}
	}

	function disable_yoast_notifications(){
		if( is_plugin_active( 'wordpress-seo/wp-seo.php' ) && class_exists( 'Yoast_Notification_Center' ) ){
			remove_action( 'admin_notices', array( Yoast_Notification_Center::get(), 'display_notifications' ) );
			remove_action( 'all_admin_notices', array( Yoast_Notification_Center::get(), 'display_notifications' ) );
		}
	}

	function do_favicon(){
		$favicon = get_field( 'favicon', 'option' );
		if( $favicon ){
			echo '<link rel="icon" type="image/png" href="' . $favicon . '">' . PHP_EOL;
		}
	}

	function embed_oembed_html( $html, $url, $attr, $post_id ){
		if( false !== strpos( $html, 'youtube' ) || false !== strpos( $html, 'vimeo' ) || false !== strpos( $html, 'v.wordpress.com' ) ){
			return '<div class="responsive-video-outer"><div class="responsive-video">' . $html . '</div></div>';
		}
		return $html;
	}

	function flush_rewrite_after_register_post_types( $post_id ){
		if( empty( $_POST[ 'acf' ] ) ){
			return;
		}
		if( 'options' == $post_id ){
			// Get the field key of the post type creator
			$field_object = get_field_object( 'msw_post_type', $post_id );
			if( isset( $_POST[ 'acf' ][ $field_object[ 'key' ] ] ) ){
				flush_rewrite_rules();
			}
		}
	}

	function get_next_post_sort( $sort ){
		global $post;
		if( is_singular() && post_type_supports( $post->post_type, 'page-attributes' ) ){
			$sort = 'ORDER BY p.menu_order ASC LIMIT 1';
		}
		return $sort;
	}
	function get_next_post_where( $where ){
		global $post, $wpdb;
		if( is_singular() && post_type_supports( $post->post_type, 'page-attributes' ) ){
			$where = $wpdb->prepare( "WHERE p.menu_order > '%s' AND p.post_type = '%s' AND p.post_status = 'publish'", $post->menu_order, $post->post_type );
		}
		return $where;
	}
	function get_previous_post_sort( $sort ){
		global $post;
		if( is_singular() && post_type_supports( $post->post_type, 'page-attributes' ) ){
			$sort = 'ORDER BY p.menu_order DESC LIMIT 1';
		}
		return $sort;
	}
	function get_previous_post_where( $where ){
		global $post, $wpdb;
		if( is_singular() && post_type_supports( $post->post_type, 'page-attributes' ) ){
			$where = $wpdb->prepare( "WHERE p.menu_order < '%s' AND p.post_type = '%s' AND p.post_status = 'publish'", $post->menu_order, $post->post_type );
		}
		return $where;
	}

	function gform_submit_button( $button_input, $form ){
		preg_match( "/<input([^\/>]*)(\s\/)*>/", $button_input, $button_match );
		$button_atts = str_replace( "value='" . $form[ 'button' ][ 'text' ] . "' ", "", $button_match[1] );
		return '<button ' . $button_atts . '>' . $form[ 'button' ][ 'text' ] . '</button>';
	}

	function javascript_detection(){
		echo '<script>(function(html){html.className = html.className.replace(/\bno-js\b/,\'js\')})(document.documentElement);</script>' . PHP_EOL;
	}

	function live_reload(){
		if( defined( 'WP_DEBUG' ) && true === WP_DEBUG ){
			$contents = '/**' . PHP_EOL
						. ' * This file exists to let Gulp know that a post has been saved in the dashboard so it can livereload in development.' . PHP_EOL
						. ' */'. PHP_EOL
						. 'Last updated: ' . time();
			$filename = get_stylesheet_directory() . '/.gulpwatch';
			file_put_contents( $filename, $contents );
		}
	}

	function login_enqueue_scripts(){
		echo '<style type="text/css">body,html{background:#f8f7f2;font-family:arial}body.login h1 a{background-repeat:no-repeat;background-image:url(data:image/png;base64,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);background-size:320px 65px;height:65px;line-height:1;width:320px}.wp-core-ui .button.button-large{background:#F8971D;border:none;border-radius:0;box-shadow:none;display:inline-block;font-weight:700;position:relative;text-transform:uppercase;-webkit-transition-property:background-color;transition-property:background-color;-webkit-transition-duration:.5s;transition-duration:.5s;height:36px;line-height:36px;text-shadow:none}.login-action-lostpassword.wp-core-ui .button.button-large{width:100%}.wp-core-ui .button.button-large:hover{background-color:#f36a22}.login .message{border-color:#F8971D}.login input[type=text]:focus,.login input[type=password]:focus{border:1px solid #ddd;box-shadow:none}.login form .forgetmenot{margin-top:7px}.login #backtoblog,.login #nav{text-align:center}.login #backtoblog a,.login #nav a{-webkit-transition-property:color;transition-property:color;-webkit-transition-duration:.3s;transition-duration:.3s}.login #backtoblog a:hover,.login #nav a:hover{color:#F8971D}</style>' . PHP_EOL;
	}

	function phpmailer_init( $phpmailer ){
		if( 1 == get_field( 'msw_ses_send_via_amazon', 'option' ) ){
			$phpmailer->isSMTP();
			$phpmailer->Host = 'email-smtp.us-east-1.amazonaws.com';
			$phpmailer->SMTPAuth = true;
			$phpmailer->Port = 587;
			$phpmailer->Username = defined( 'AWS_SES_USER' ) ? AWS_SES_USER : '';
			$phpmailer->Password = defined( 'AWS_SES_PASS' ) ? AWS_SES_PASS : '';
			$phpmailer->SMTPSecure = 'tls';
			$phpmailer->From = get_field( 'msw_ses_sender_email', 'option' ) ? get_field( 'msw_ses_sender_email', 'option' ) : 'wp@makespaceweb.com';
			$phpmailer->FromName = get_field( 'msw_ses_sender_name', 'option' ) ? get_field( 'msw_ses_sender_name', 'option' ) : get_bloginfo( 'name' );
		}
	}

	public static function read_time( $post_id = 0 ){
		if( is_single() && 0 == $post_id ){
			global $post;
			$post_id = $post->ID;
			$text = get_the_content();
		} else {
			$content_post = get_post( $post_id );
			$text = $content_post->post_content;
		}
		$words = str_word_count( strip_tags( $text ) );
		$min = (int)ceil( $words / 200 );
		$min = max( 1, $min );

		$min = apply_filters( 'msw_read_time_minutes', $min );
		$time_string = apply_filters( 'msw_read_time_text', 'min read' );

		return apply_filters( 'msw_read_time', $min . ' ' . $time_string );
	}

	function register_post_types(){
		if( have_rows( 'msw_post_type', 'option' ) ){
			while( have_rows( 'msw_post_type', 'option' ) ) : the_row();
				$plural = get_sub_field( 'plural_name' );
				$singular = get_sub_field( 'singular_name' );
				$slug = get_sub_field( 'slug' );
				$public_information = get_sub_field( 'public_information' ) ? get_sub_field( 'public_information' ) : array();
				$new_post_type = register_post_type( $slug, array(
					'labels' => array(
						'name'               => $plural,
						'singular_name'      => $singular,
						'menu_name'          => $plural,
						'name_admin_bar'     => $singular,
						'add_new'            => 'Add New',
						'add_new_item'       => 'Add New ' . $singular,
						'new_item'           => 'New ' . $singular,
						'edit_item'          => 'Edit ' . $singular,
						'view_item'          => 'View ' . $singular,
						'all_items'          => 'All ' . $plural,
						'search_items'       => 'Search ' . $plural,
						'parent_item_colon'  => 'Parent ' . $plural . ':',
						'not_found'          => 'No ' . $plural . ' found.',
						'not_found_in_trash' => 'No ' . $plural . ' found in Trash.',
						'menu_name'          => get_sub_field( 'menu_name' ) ? get_sub_field( 'menu_name' ) : $plural
					),
					'exclude_from_search' => in_array( 'exclude_from_search', $public_information ),
				    'menu_icon' => get_sub_field( 'dashicon' ),
				    'menu_position' => (int)get_sub_field( 'menu_position' ),
					'public' => true,
					'has_archive' => in_array( 'has_archive', $public_information ),
					'hierarchical' => true,
					'supports' => get_sub_field( 'options_supports' )
				) );

				$this->create_sample_posts( $slug );

				if( have_rows( 'taxonomies' ) ){
					while( have_rows( 'taxonomies' ) ) : the_row();
						$tax_name_plural = get_sub_field( 'plural_name' );
						$tax_name_singular = get_sub_field( 'singular_name' );
						$tax_slug = get_sub_field( 'slug' );
						register_taxonomy( $tax_slug, $slug, array(
							'labels' => array(
								'name'                       => $tax_name_plural,
								'singular_name'              => $tax_name_singular,
								'search_items'               => 'Search ' . $tax_name_plural,
								'popular_items'              => 'Popular ' . $tax_name_plural,
								'all_items'                  => 'All ' . $tax_name_plural,
								'parent_item'                => 'Parent ' . $tax_name_singular,
								'parent_item_colon'          => 'Parent ' . $tax_name_singular . ':',
								'edit_item'                  => 'Edit ' . $tax_name_singular,
								'update_item'                => 'Update ' . $tax_name_singular,
								'add_new_item'               => 'Add New ' . $tax_name_singular,
								'new_item_name'              => 'New ' . $tax_name_singular . ' Name',
								'separate_items_with_commas' => 'Separate ' . $tax_name_plural . ' with commas',
								'add_or_remove_items'        => 'Add or remove ' . $tax_name_plural,
								'choose_from_most_used'      => 'Choose from the most used ' . $tax_name_plural,
								'not_found'                  => 'No ' . $tax_name_plural . ' found.',
								'menu_name'                  => $tax_name_plural
							),
							'hierarchical' => 'yes' == get_sub_field( 'type' ),
							'show_admin_column' => true,
							'show_in_nav_menus' => false,
						) );
					endwhile;
				}
			endwhile;
		}
	}

	function sc_protected_email( $args, $content = null ){
		$atts = shortcode_atts( array(
			'email' => ''
		), $args );
		if( is_email( $atts[ 'email' ] ) ){
			$atts[ 'content' ] = !empty( $content ) ? $content : $atts[ 'email' ];
			return '<a href="' . antispambot( 'mailto:' . $atts[ 'email' ] ) . '">' . antispambot( $atts[ 'content' ] ) . '</a>';
		}
		return;
	}


	function sc_makespace_sitemap( $args, $content = null ){
		// Makes sure that the blog overview page isn't the front page and if so,
		// exclude that page from the page list.
		$blogID = get_option( 'page_for_posts' );
		$exclude = array();
		if( $blogID != get_option( 'page_on_front' ) ) {
			$exclude[] = $blogID;
		}
		if( class_exists( 'WooCommerce' ) ){
			$exclude[] = get_option( 'woocommerce_shop_page_id' );
			$exclude[] = get_option( 'woocommerce_cart_page_id' );
			$exclude[] = get_option( 'woocommerce_checkout_page_id' );
			$exclude[] = get_option( 'woocommerce_pay_page_id' );
			$exclude[] = get_option( 'woocommerce_thanks_page_id' );
			$exclude[] = get_option( 'woocommerce_myaccount_page_id' );
			$exclude[] = get_option( 'woocommerce_edit_address_page_id' );
			$exclude[] = get_option( 'woocommerce_view_order_page_id' );
			$exclude[] = get_option( 'woocommerce_terms_page_id' );
		}

		$exclude = implode( ',', $exclude );

		// Unwraps the page list of the <ul> and excludes the blog overview page
		// in order to then add the blog overview page and posts at the end
		$args = array( 'title_li' => '', 'exclude' => $exclude, 'echo' => 0, 'sort_column' => 'menu_order' );
		$pages = '<ul id="sitemap">';
		$pages .= wp_list_pages( $args );

		// On large sites, listing all of the old blog posts will be too
		// resource intensive. Instead, if there is a blog "page", we'll
		// list that and then list the categories under it. If there is
		// no blog page, we'll just list the categories.
		$posts_msw = get_posts();
		if( $posts_msw ) {
			if( $blogID ){
				// We have a blog page
				$pages .= '<li><a href="' . get_the_permalink( $blogID ) . '" title="' . get_the_title( $blogID ) . '">' . get_the_title( $blogID ) . '</a><ul>';
				$pages .= wp_list_categories( array(
					'echo' => false,
					'orderby' => 'name',
					'title_li' => ''
				) );
				$pages .= '</ul></li>';
			} else {
				$pages .= wp_list_categories( array(
					'echo' => false,
					'orderby' => 'name',
					'title_li' => apply_filters( 'makespace_sitemap_blog_title', 'Article Categories' )
				) );
			}
		}

		if( class_exists( 'WooCommerce' ) ){
			$woocommerce_shop_page_id = get_option( 'woocommerce_shop_page_id' );
			$woocommerce_products = get_posts( array(
				'nopaging' => true,
				'post_type' => 'product'
			) );
			if( $woocommerce_products ){
				$pages .= '<li><a href="' . get_permalink( $woocommerce_shop_page_id ) . '" title="' . get_the_title( $woocommerce_shop_page_id ) . '">' . get_the_title( $woocommerce_shop_page_id ) . '</a>';
				$pages .= '<ul>';
				$product_terms = get_terms( array(
					'taxonomy' => 'product_cat',
				) );
				if( $product_terms ){
					$pages .= '<li><strong>Categories</strong><ul>';
					foreach( $product_terms as $product_term ){
						$pages .= '<li><a href="' . get_term_link( $product_term->term_id ) . '" title="' . $product_term->name . '">' . $product_term->name . '</a></li>';
					}
					$pages .= '</ul></li>';
				}
				if( $product_terms ){
					$pages .= '<li><strong>All Products</strong><ul>';
					foreach( $woocommerce_products as $woocommerce_product ){
						$pages .= '<li><a href="' . get_permalink( $woocommerce_product->ID ) . '" title="' . $woocommerce_product->post_title . '">' . $woocommerce_product->post_title . '</a></li>';
					}
					$pages .= '</ul></li>';
				}
				$pages .= '</ul>';
				$pages .= '</li>';
			}
		}

		$pages = apply_filters( 'makespace_add_to_sitemap', $pages );

		$pages .= '</ul>';

		return $pages;
	}

	function script_loader_tag( $tag, $handle ){
		if( 'google-maps' == $handle ){
			$tag = str_replace( ' src', ' async defer src', $tag );
		}
        return $tag;
	}

	function tgmpa_register(){
		tgmpa( array(
			array(
				'name' => 'Add to Any Social Sharing',
				'slug' => 'add-to-any'
			),
			array(
				'name' => 'Advanced Custom Fields (Pro)',
				'slug' => 'advanced-custom-fields-pro',
				'source' => 'advanced-custom-fields-pro.zip'
			),
			array(
				'name' => 'AMP: Accelerated Mobile Pages',
				'slug' => 'amp'
			),
			array(
				'name' => 'AMP: Glue for Yoast SEO & AMP',
				'slug' => 'glue-for-yoast-seo-amp'
			),
			array(
				'name' => 'Custom Post Type UI',
				'slug' => 'custom-post-type-ui'
			),
			array(
				'name' => 'Font Awesome 4 Menus',
				'slug' => 'font-awesome-4-menus'
			),
			array(
				'name' => 'Gravity Forms',
				'slug' => 'gravityforms',
				'source' => 'gravityforms_2.1.3.8.zip'
			),
			array(
				'name' => 'Redirection',
				'slug' => 'redirection'
			),
			array(
				'name' => 'Post Duplicator',
				'slug' => 'post-duplicator'
			),
			array(
				'name' => 'Simple Page Ordering',
				'slug' => 'simple-page-ordering'
			),
			array(
				'name' => 'The Events Calendar',
				'slug' => 'the-events-calendar'
			),
			array(
				'name' => 'Tablepress',
				'slug' => 'tablepress'
			),
			array(
				'name' => 'WooCommerce',
				'slug' => 'woocommerce'
			),
			array(
				'name' => 'WP Migrate DB',
				'slug' => 'wp-migrate-db'
			),
			array(
				'name' => 'WP Smush Image Optimization',
				'slug' => 'wp-smushit'
			),
			array(
				'name' => 'WP Super Cache',
				'slug' => 'wp-super-cache'
			),
			array(
				'is_callable' => 'wpseo_init',
				'name' => 'Yoast SEO',
				'slug' => 'wordpress-seo',
			),
		), array(
			'has_notices' => false,
			'default_path' => get_template_directory() . '/includes/tgmpa/plugins/',
			'is_automatic' => true,
			'parent_slug' => 'plugins.php',
			'strings' => array(
				'menu_title' => 'Top Plugins',
				'page_title' => 'List of Top or Approved Plugins',
				'return' => 'Return to top plugins'
			)
		) );
	}

	function woocommerce_after_main_content(){
		echo '</section>';
	}

	function woocommerce_before_main_content(){
		echo '<section class="container">';
	}

	function woocommerce_breadcrumb_defaults( $defaults ){
		$defaults[ 'after' ] = '</li>';
		$defaults[ 'before' ] = '<li>';
		$defaults[ 'delimiter' ] = '';
		$defaults[ 'wrap_after' ] = '</ul></nav>';
		$defaults[ 'wrap_before' ] = '<nav id="breadcrumbs"><ul>';
		return $defaults;
	}

	function woocommerce_yoast_breadcrumbs(){
		if ( function_exists( 'yoast_breadcrumb' ) ){
			yoast_breadcrumb( '<nav id="breadcrumbs">', '</nav>' );
		}
	}

	function wp_dashboard_setup(){
		remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );
	}

	function wp_enqueue_scripts(){
		wp_register_script( 'livereload', 'http://localhost:35729/livereload.js?snipver=1', null, false, true );
		if( true === WP_DEBUG ){
			wp_enqueue_script( 'livereload' );
		}
		wp_enqueue_script( 'jquery' );
	}

	function wp_head(){
		$has_posts = get_posts();
		if( $has_posts ){
			echo '<link rel="alternate" type="application/rss+xml" title="' . get_bloginfo( 'name' ) . ' RSS Feed" href="' . get_bloginfo( 'rss2_url' ) . '">' . PHP_EOL;
		}
		the_field( 'header_javascript', 'option' );
	}

	function wp_footer(){
		the_field( 'footer_javascript', 'option' );
	}

	function wpseo_breadcrumb_output_wrapper( $wrapper ){
		return 'ul';
	}

	function wpseo_breadcrumb_links( $crumbs ){
		if( class_exists( 'WooCommerce' ) ){
			if( is_shop() || is_product_category() || is_product_tag() || is_product() || is_cart() || is_checkout() || is_account_page() ){
				$woocommerce_shop_page_id = get_option( 'woocommerce_shop_page_id' );
				$did_replace_product_link = false;
				for( $i = 0; $i < count( $crumbs ); $i++ ){
					if( array_key_exists( 'ptarchive', $crumbs[ $i ] ) ){
						$crumbs[ $i ] = array(
							'text' => get_the_title( $woocommerce_shop_page_id ),
							'url' => get_permalink( $woocommerce_shop_page_id ),
							'allow_html' => 1
						);
						$did_replace_product_link = true;
					}
				}
				if( !$did_replace_product_link ){
					$shop_page_link = array( array(
						'text' => get_the_title( $woocommerce_shop_page_id ),
						'url' => get_permalink( $woocommerce_shop_page_id ),
						'allow_html' => 1
					) );
					array_splice( $crumbs, 1, 0, $shop_page_link );
				}
			}
		}
		return $crumbs;
	}

	function wpseo_breadcrumb_separator( $sep ){
		return '';
	}

	function wpseo_breadcrumb_single_link( $link_output, $link ){
		if( false === strpos( $link_output, 'breadcrumb_last' ) ){
			$link_output = $link_output . '</li>';
		} else {
			preg_match( '/(.?<span(?:.*?)>(?:.*?)<\/span>)/', $link_output, $matches );
			if( count( $matches ) && 1 < count( $matches ) ){
				$link_output = '<li>' . $matches[ 1 ] . '</li>';
			} else {
				$link_output = '<li>' . $link_output;
			}
		}
		return $link_output;
	}

	function wpseo_breadcrumb_single_link_wrapper( $wrapper ){
		return 'li';
	}

	function wpseo_metabox_prio(){
		return 'low';
	}

}

$MakespaceFramework = new MakespaceFramework();