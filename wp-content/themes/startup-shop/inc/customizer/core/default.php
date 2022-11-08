<?php
/**
 * Default theme options.
 *
 * @package startup-shop
 */

if ( ! function_exists( 'startup_shop_get_default_theme_options' ) ) :

	/**
	 * Get default theme options
	 *
	 * @since 1.0.0
	 *
	 * @return array Default theme options.
	 */
	function startup_shop_get_default_theme_options() {

		$defaults = array();
		
		
		/*Posts Layout*/
		$defaults['blog_layout']     				= 'content-sidebar';
		$defaults['single_post_layout']     		= 'content-sidebar';
		
		$defaults['blog_loop_content_type']     	= 'excerpt';
		
		$defaults['blog_meta_hide']     			= false;
		$defaults['signle_meta_hide']     			= false;
		
		/*Posts Layout*/
		$defaults['page_layout']     				= 'no-sidebar';
		
		/*layout*/
		$defaults['copyright_text']					= esc_html__( 'Copyright All right reserved', 'startup-shop' );
		$defaults['read_more_text']					= esc_html__( 'Read More', 'startup-shop' );
		$defaults['index_hide_thumb']     			= false;
		$defaults['dev_credits']     				= false;
			
		
		/*Posts Layout*/
		$defaults['__fb_pro_link']     				= '';
		$defaults['__tw_pro_link']     				= '';
		$defaults['__you_pro_link']     		    = '';
		$defaults['__pr_pro_link']     				= '';
		
		$defaults['__primary_color']     			= '#6c757d';
		$defaults['__secondary_color']     			= '#ed1c24';
		
		
		

		// Pass through filter.
		$defaults = apply_filters( 'startup_shop_filter_default_theme_options', $defaults );

		return $defaults;

	}

endif;
