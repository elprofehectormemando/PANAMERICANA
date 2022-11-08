<?php
	if ( ! function_exists( 'ebook_store_setup' ) ) :

	function ebook_store_setup() {
		$GLOBALS['content_width'] = apply_filters( 'ebook_store_content_width', 640 );
		
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'woocommerce' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'html5', array( 'comment-list', 'search-form', 'comment-form', ) );
		add_theme_support( 'custom-logo', array(
			'height'      => 240,
			'width'       => 240,
			'flex-height' => true,
		) );

		add_theme_support( 'custom-background', array(
			'default-color' => 'f1f1f1'
		) );

		/*
		 * This theme styles the visual editor to resemble the theme style,
		 * specifically font, colors, icons, and column width.
		 */
		add_editor_style( array( 'css/editor-style.css', vw_book_store_font_url() ) );

		// Theme Activation Notice
		global $pagenow;

		if (is_admin() && ('themes.php' == $pagenow) && isset( $_GET['activated'] )) {
			add_action('admin_notices', 'ebook_store_activation_notice');
		}
	}
	endif;

	add_action( 'after_setup_theme', 'ebook_store_setup' );

	// Notice after Theme Activation
	function ebook_store_activation_notice() {
		echo '<div class="notice notice-success is-dismissible welcome-notice">';
			echo '<p>'. esc_html__( 'Thank you for choosing Ebook Store Theme. Would like to have you on our Welcome page so that you can reap all the benefits of our Ebook Store Theme.', 'ebook-store' ) .'</p>';
			echo '<span><a href="'. esc_url( admin_url( 'themes.php?page=ebook_store_guide' ) ) .'" class="button button-primary">'. esc_html__( 'GET STARTED', 'ebook-store' ) .'</a></span>';
			echo '<span class="demo-btn"><a href="'. esc_url( 'https://www.vwthemes.net/ebook-store/' ) .'" class="button button-primary" target=_blank>'. esc_html__( 'VIEW DEMO', 'ebook-store' ) .'</a></span>';
			echo '<span class="upgrade-btn"><a href="'. esc_url( 'https://www.vwthemes.com/themes/ebook-store-wordpress-theme/' ) .'" class="button button-primary" target=_blank>'. esc_html__( 'UPGRADE PRO', 'ebook-store' ) .'</a></span>';
		echo '</div>';
	}

	add_action( 'wp_enqueue_scripts', 'ebook_store_enqueue_styles' );
	function ebook_store_enqueue_styles() {
    	$parent_style = 'vw-book-store-basic-style'; // Style handle of parent theme.
    	
		wp_enqueue_style( 'bootstrap-style', esc_url(get_template_directory_uri()).'/css/bootstrap.css' );
		wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
		wp_enqueue_style( 'ebook-store-style', get_stylesheet_uri(), array( $parent_style ) );
		require get_theme_file_path( '/inline-style.php' );
		wp_add_inline_style( 'ebook-store-style',$vw_book_store_custom_css );
		require get_parent_theme_file_path( '/inline-style.php' );
		wp_add_inline_style( 'vw-book-store-basic-style',$vw_book_store_custom_css );
		wp_enqueue_style( 'ebook-store-block-style', get_theme_file_uri('/assets/css/blocks.css') );
		wp_enqueue_style( 'ebook-store-block-patterns-style-frontend', get_theme_file_uri('/inc/block-patterns/css/block-frontend.css') );

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}
		
	add_action( 'init', 'ebook_store_remove_parent_function');
	function ebook_store_remove_parent_function() {
		remove_action( 'admin_notices', 'vw_book_store_activation_notice' );
		remove_action( 'admin_menu', 'vw_book_store_gettingstarted' );
	}

	add_action( 'customize_register', 'Ebook_Store_Customize_register', 11 );
	function Ebook_Store_Customize_register($wp_customize) {
		global $wp_customize;
		$wp_customize->remove_section( 'vw_book_store_upgrade_pro_link' );
		$wp_customize->remove_section( 'vw_book_store_get_started_link' );
		$wp_customize->remove_control( 'vw_book_store_search_hide_show' );
		$wp_customize->remove_control( 'vw_book_store_search_icon' );
		$wp_customize->remove_control( 'vw_book_store_search_close_icon' );
		$wp_customize->remove_control( 'vw_book_store_search_font_size' );
		$wp_customize->remove_control( 'vw_book_store_search_placeholder' );
		$wp_customize->remove_control( 'vw_book_store_social_icon_padding' );
		$wp_customize->remove_control( 'vw_book_store_social_icon_width' );
		$wp_customize->remove_control( 'vw_book_store_social_icon_height' );
		$wp_customize->remove_control( 'vw_book_store_social_icon_border_radius' );

		//Header
		$wp_customize->add_setting('ebook_store_discount_text',array(
			'default'=> '',
			'sanitize_callback'	=> 'sanitize_text_field'
		));
		$wp_customize->add_control('ebook_store_discount_text',array(
			'label'	=> esc_html__('Add Discount Text','ebook-store'),
			'input_attrs' => array(
	            'placeholder' => esc_html__( 'Get Mega Discount', 'ebook-store' ),
	        ),
			'section'=> 'vw_book_store_topbar',
			'type'=> 'text'
		));

		$wp_customize->add_setting('ebook_store_discount_sale_text',array(
			'default'=> '',
			'sanitize_callback'	=> 'sanitize_text_field'
		));
		$wp_customize->add_control('ebook_store_discount_sale_text',array(
			'label'	=> esc_html__('Add Sale Text','ebook-store'),
			'input_attrs' => array(
	            'placeholder' => esc_html__( 'season sale', 'ebook-store' ),
	        ),
			'section'=> 'vw_book_store_topbar',
			'type'=> 'text'
		));

		$wp_customize->add_setting('ebook_store_discount_sale_link',array(
			'default'=> '',
			'sanitize_callback'	=> 'esc_url_raw'
		));
		$wp_customize->add_control('ebook_store_discount_sale_link',array(
			'label'	=> esc_html__('Sale Link','ebook-store'),
			'input_attrs' => array(
	            'placeholder' => esc_html__( 'www.example.com', 'ebook-store' ),
	        ),
			'section'=> 'vw_book_store_topbar',
			'type'=> 'url'
		));	

		//Banner Section
		$wp_customize->add_section( 'ebook_store_banner_section', array(
	    	'title'      => __( 'Banner Section', 'ebook-store' ),
	    	'description' => "For more options of banner section </br><a class='go-pro-btn' target='_blank' href='". esc_url(VW_BOOK_STORE_GO_PRO_URL) ." '>GO PRO</a>",
			'panel' => 'vw_book_store_panel_id',
			'priority' => 10,
		));

		$wp_customize->add_setting( 'ebook_store_banner_page', array(
			'default'           => '',
			'sanitize_callback' => 'ebook_store_sanitize_dropdown_pages'
		));
		$wp_customize->add_control( 'ebook_store_banner_page', array(
			'label'    => __( 'Select Banner Page', 'ebook-store' ),
			'section'  => 'ebook_store_banner_section',
			'type'     => 'dropdown-pages'
		));

		$wp_customize->add_setting( 'ebook_store_banner_product_page' , array(
			'default'           => '',
			'sanitize_callback' => 'ebook_store_sanitize_dropdown_pages'
		));
		$wp_customize->add_control( 'ebook_store_banner_product_page' , array(
			'label'    => __( 'Select Product Page', 'ebook-store' ),
			'section'  => 'ebook_store_banner_section',		
			'type'     => 'dropdown-pages'
		) );
	}

	add_action( 'customize_register', 'ebook_store_typography_customize_register', 12 );
	function ebook_store_typography_customize_register( $wp_customize ) {
		$wp_customize->remove_control( 'vw_book_store_second_color' );
	}

	define('EBOOK_STORE_FREE_THEME_DOC',__('https://www.vwthemesdemo.com/docs/free-ebook-store/','ebook-store'));
	define('EBOOK_STORE_SUPPORT',__('https://wordpress.org/support/theme/ebook-store/','ebook-store'));
	define('EBOOK_STORE_REVIEW',__('https://wordpress.org/support/theme/ebook-store/reviews','ebook-store'));
	define('EBOOK_STORE_BUY_NOW',__('https://www.vwthemes.com/themes/ebook-store-wordpress-theme/','ebook-store'));
	define('EBOOK_STORE_LIVE_DEMO',__('https://www.vwthemes.net/ebook-store/','ebook-store'));
	define('EBOOK_STORE_PRO_DOC',__('https://www.vwthemesdemo.com/docs/vw-ebook-store-pro/','ebook-store'));
	define('EBOOK_STORE_FAQ',__('https://www.vwthemes.com/faqs/','ebook-store'));
	define('EBOOK_STORE_CONTACT',__('https://www.vwthemes.com/contact/','ebook-store'));
	define('EBOOK_STORE_CHILD_THEME',__('https://developer.wordpress.org/themes/advanced-topics/child-themes/','ebook-store'));
	define('EBOOK_STORE_CREDIT',__('https://www.vwthemes.com/themes/free-ebook-wordpress-theme/','ebook-store'));

	if ( ! function_exists( 'ebook_store_credit' ) ) {
		function ebook_store_credit(){
			echo "<a href=".esc_url(EBOOK_STORE_CREDIT)." target='_blank'>". esc_html__('Ebook Store WordPress Theme','ebook-store') ."</a>";
		}
	}

	if ( ! defined( 'VW_BOOK_STORE_GO_PRO_URL' ) ) {
		define( 'VW_BOOK_STORE_GO_PRO_URL', 'https://www.vwthemes.com/themes/ebook-store-wordpress-theme/');
	}

	/**
	 * Enqueue block editor style
	 */
	function ebook_store_block_editor_styles() {
		wp_enqueue_style( 'ebook-store-font', vw_book_store_font_url(), array() );
	    wp_enqueue_style( 'ebook-store-block-patterns-style-editor', get_theme_file_uri( '/inc/block-patterns/css/block-editor.css' ), false, '1.0', 'all' );
	}
	add_action( 'enqueue_block_editor_assets', 'ebook_store_block_editor_styles' );

	function ebook_store_sanitize_choices( $input, $setting ) {
	    global $wp_customize; 
	    $control = $wp_customize->get_control( $setting->id ); 
	    if ( array_key_exists( $input, $control->choices ) ) {
	        return $input;
	    } else {
	        return $setting->default;
	    }
	}

	function ebook_store_sanitize_dropdown_pages( $page_id, $setting ) {
	  	$page_id = absint( $page_id );
	  	return ( 'publish' == get_post_status( $page_id ) ? $page_id : $setting->default );
	}

	/* Theme Widgets Setup */

	function ebook_store_widgets_init() {
		register_sidebar( array(
			'name'          => __( 'Footer Navigation 1', 'ebook-store' ),
			'description'   => __( 'Appears on footer 1', 'ebook-store' ),
			'id'            => 'footer-1',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );

		register_sidebar( array(
			'name'          => __( 'Footer Navigation 2', 'ebook-store' ),
			'description'   => __( 'Appears on footer 2', 'ebook-store' ),
			'id'            => 'footer-2',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );

		register_sidebar( array(
			'name'          => __( 'Footer Navigation 3', 'ebook-store' ),
			'description'   => __( 'Appears on footer 3', 'ebook-store' ),
			'id'            => 'footer-3',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );

		register_sidebar( array(
			'name'          => __( 'Footer Navigation 4', 'ebook-store' ),
			'description'   => __( 'Appears on footer 4', 'ebook-store' ),
			'id'            => 'footer-4',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );

		register_sidebar( array(
			'name'          => __( 'Social Icon', 'ebook-store' ),
			'description'   => __( 'Appears on topbar', 'ebook-store' ),
			'id'            => 'social-icon',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );
	}
	add_action( 'widgets_init', 'ebook_store_widgets_init' );

// Customizer Pro
load_template( ABSPATH . WPINC . '/class-wp-customize-section.php' );

class Ebook_Store_Customize_Section_Pro extends WP_Customize_Section {
	public $type = 'ebook-store';
	public $pro_text = '';
	public $pro_url = '';
	public function json() {
		$json = parent::json();
		$json['pro_text'] = $this->pro_text;
		$json['pro_url']  = esc_url( $this->pro_url );
		return $json;
	}
	protected function render_template() { ?>
		<li id="accordion-section-{{ data.id }}" class="accordion-section control-section control-section-{{ data.type }} cannot-expand">
			<h3 class="accordion-section-title">
				{{ data.title }}
				<# if ( data.pro_text && data.pro_url ) { #>
					<a href="{{ data.pro_url }}" class="button button-secondary alignright" target="_blank">{{ data.pro_text }}</a>
				<# } #>
			</h3>
		</li>
	<?php }
}

final class Ebook_Store_Customize {
	public static function get_instance() {
		static $instance = null;
		if ( is_null( $instance ) ) {
			$instance = new self;
			$instance->setup_actions();
		}
		return $instance;
	}
	private function __construct() {}
	private function setup_actions() {
		// Register panels, sections, settings, controls, and partials.
		add_action( 'customize_register', array( $this, 'sections' ) );
		// Register scripts and styles for the controls.
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_control_scripts' ), 0 );
	}
	public function sections( $manager ) {
		// Register custom section types.
		$manager->register_section_type( 'Ebook_Store_Customize_Section_Pro' );
		// Register sections.
		$manager->add_section( new Ebook_Store_Customize_Section_Pro( $manager, 'ebook_store_upgrade_pro_link',
		array(
			'priority'   => 1,
			'title'    => esc_html__( 'Ebook Store PRO', 'ebook-store' ),
			'pro_text' => esc_html__( 'UPGRADE PRO', 'ebook-store' ),
			'pro_url'  => esc_url('https://www.vwthemes.com/themes/ebook-store-wordpress-theme/'),
		)));

		// Register sections.
		$manager->add_section(new Ebook_Store_Customize_Section_Pro($manager,'ebook_store_get_started_link',array(
			'priority'   => 1,
			'title'    => esc_html__( 'DOCUMENTATION', 'ebook-store' ),
			'pro_text' => esc_html__( 'DOCS', 'ebook-store' ),
			'pro_url'  => esc_url('https://www.vwthemesdemo.com/docs/free-ebook-store/'),
		)));
	}
	public function enqueue_control_scripts() {
		wp_enqueue_script( 'ebook-store-customize-controls', get_stylesheet_directory_uri() . '/assets/js/customize-controls-child.js', array( 'customize-controls' ) );
		wp_enqueue_style( 'ebook-store-customize-controls', get_stylesheet_directory_uri() . '/assets/css/customize-controls-child.css' );
	}
}
Ebook_Store_Customize::get_instance();

/* getstart */
require get_theme_file_path('/inc/getstart/getstart.php');

/* Plugin Activation */
require get_theme_file_path() . '/inc/getstart/plugin-activation.php';

/* Tgm */
require get_theme_file_path() . '/inc/tgm/tgm.php';

/* Block Pattern */
require get_theme_file_path('/inc/block-patterns/block-patterns.php');