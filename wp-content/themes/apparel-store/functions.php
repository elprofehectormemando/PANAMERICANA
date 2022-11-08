<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:
function apparel_store_theme_setup(){

    // Make theme available for translation.
    load_theme_textdomain( 'apparel-store', get_stylesheet_directory_uri() . '/languages' );
    
}
add_action( 'after_setup_theme', 'apparel_store_theme_setup' );

if ( !function_exists( 'apparel_store_parent_css' ) ):
    function apparel_store_parent_css() {
        wp_enqueue_style( 'chld_thm_cfg_parent', trailingslashit( get_template_directory_uri() ) . 'style.css', array( 'bootstrap','icofont','scrollbar','magnific-popup','owl-carousel','startup-shop-common' ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'apparel_store_parent_css', 10 );


// Remove hook from Parent
if( !function_exists('apparel_store_disable_from_parent') ):

    add_action('init','apparel_store_disable_from_parent',10);
    function apparel_store_disable_from_parent(){
        
      global $startup_shop_Header_Layout;
      remove_action('startup_shop_site_header', array( $startup_shop_Header_Layout, 'site_header_layout' ), 30 );
     
     global $startup_shop_body_layout;
     remove_action('startup_shop_container_wrap_end', array( $startup_shop_body_layout, 'get_sidebar' ), 10 );
     
    }
    
endif;
// END ENQUEUE PARENT ACTION


if( !function_exists('apparel_store_site_header') ):
    add_action('startup_shop_site_header','apparel_store_site_header', 30 );
    /**
    * Container before
    *
    * @return $html
    */
    function apparel_store_site_header(){
        
    ?>
    <div id="header-2nd" class="replace-3rd-style">
        <div class="container">
        <div class="align-items-center d-flex responsive">
            <div>
                <div class="block">
                 <?php do_action('startup_shop_header_layout_1_branding');?>
                </div>
            </div>
            <div class="text-right ms-auto">
               <div id="navbar">
                 <?php do_action('startup_shop_header_layout_1_navigation');?>
               </div>
            </div>
             <div class="text-right responsive-wrap icon-warp">
               
                 <ul class="social-icon d-flex">

                    <?php if( startup_shop_get_option('__fb_pro_link')!="" ) : ?> 
                    <li><a href="<?php echo esc_url( startup_shop_get_option('__fb_pro_link') );?>" target="_blank" rel="nofollow"><i class="icofont-facebook"></i></a></li>
                    <?php endif;?>

                    <?php if( startup_shop_get_option('__tw_pro_link')!="" ) : ?> 
                    <li><a href="<?php echo esc_url( startup_shop_get_option('__tw_pro_link') );?>" target="_blank" rel="nofollow"><i class="icofont-twitter"></i></a></li>
                    <?php endif;?>

                    <?php if( startup_shop_get_option('__you_pro_link')!="" ) : ?> 
                    <li><a href="<?php echo esc_url( startup_shop_get_option('__you_pro_link') );?>" target="_blank" rel="nofollow"><i class="icofont-youtube-play"></i></a></li>
                    <?php endif;?>
                    <li><a href="<?php echo esc_url( startup_shop_get_option('__you_pro_link') );?>" target="_blank" rel="nofollow"><i class="icofont-youtube-play"></i></a></li>

                <?php if( function_exists('startup_shop_woocommerce_cart_link')) :?>   
                <li><?php startup_shop_woocommerce_cart_link(); ?></li>
                <?php endif;?>

                </ul>   

                 <button class="startup-shop-rd-navbar-toggle" tabindex="0" autofocus="true"><i class="icofont-navigation-menu"></i></button></div>
            
            </div>
        </div>
    </div>
    <?php 
    }
endif;

if( !function_exists('apparel_store_theme_options') ):
    add_filter('startup_shop_filter_default_theme_options','apparel_store_theme_options' );
    /**
    * Container before
    *
    * @return $array
    */
    function apparel_store_theme_options( $defaults ){

        $defaults['blog_layout']                    = 'full-container';
        $defaults['single_post_layout']             = 'no-sidebar';

        return $defaults;
    }
endif;
    

add_action('startup_shop_container_wrap_end', 'apparel_store_get_sidebar', 10 );

function apparel_store_get_sidebar( $layout = '' ){
    
    if( class_exists( 'WooCommerce' ) && ( is_shop() || is_product_category() ) ) { return; }   
    switch ( $layout ) {
    case 'sidebar-content':
        $layout = 'col-xl-4 col-md-4 col-12 order-1 startup-shop-sidebar';
        break;
    case 'no-sidebar':
        return false;
        break;
    case 'full-container':
        return false;
        break;  
    default:
        $layout = 'col-xl-4 col-md-4 col-12 order-2 startup-shop-sidebar';
    }   
    ?>
    <div class="<?php echo esc_attr( $layout );?>">
        <?php get_sidebar();?>
    </div>
    <?php
}

if( !function_exists('apparel_store_shop_page_layout') ):
    add_filter('startup_shop_container_wrap_column_start_filter','apparel_store_shop_page_layout' );
    /**
    * Container before
    *
    * @return $array
    */
    function apparel_store_shop_page_layout( $html ){
       if( class_exists( 'WooCommerce' ) && ( is_shop() || is_product_category() ) ) {  
        $html     = '<div class="col-md-12">
                        <main id="main" class="site-main">';
        }

        return $html;
    }
endif;

function apparel_store_woocommerce_loop_columns() {
    return 4;
}
add_filter( 'loop_shop_columns', 'apparel_store_woocommerce_loop_columns',999 );

function apparel_store_filter_header_args( $args ) {
    $args['default-image'] = get_stylesheet_directory_uri() . '/assets/image/custom-header.jpg';
    return $args;
}
add_filter( 'startup_shop_custom_header_args', 'apparel_store_filter_header_args' );


function apparel_store_loop_navigation() {
    return 'number';
}
add_filter( 'startup_shop_loop_navigation_filter', 'apparel_store_loop_navigation' );

