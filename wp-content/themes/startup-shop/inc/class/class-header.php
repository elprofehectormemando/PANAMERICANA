<?php
/**
 * The Site Theme Header Class 
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package startup-shop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
class startup_shop_Header_Layout{
	/**
	 * Function that is run after instantiation.
	 *
	 * @return void
	 */
	public function __construct() {

		add_action('startup_shop_header_layout_1_branding', array( $this, 'get_site_branding' ), 10 );

		add_action('startup_shop_header_layout_1_navigation', array( $this, 'get_site_navigation' ), 10 );



		add_action('startup_shop_site_header', array( $this, 'site_skip_to_content' ), 5 );
		
		add_action('startup_shop_site_header', array( $this, 'get_site_search_form' ), 20 );

		add_action('startup_shop_site_header', array( $this, 'site_header_layout' ), 30 );

	
		add_action('startup_shop_site_header', array( $this, 'site_hero_sections' ), 999 );

		add_action('startup_shop_site_header', array( $this, 'get_site_breadcrumb' ), 9999 );
		
		
	}
	
	/**
	* Container before
	*
	* @return $html
	*/
	function site_skip_to_content(){
		
		echo '<a class="skip-link screen-reader-text" href="#content">'. esc_html__( 'Skip to content', 'startup-shop' ) .'</a>';
	}

	/**
	* Container before
	*
	* @return $html
	*/
	function site_header_layout(){
		?>
		<header id="masthead" class="site-header">
        
			<div class="container">
				<div class="align-items-center d-flex responsive">
					<div class="branding-wrap">
						<div class="block">
							<?php do_action('startup_shop_header_layout_1_branding');?>
						</div>
					</div>
                    
					<div class="text-right widget-wrap ms-auto">
                   		<?php
                        if ( is_active_sidebar( 'logo-right' ) ) {
                      		  dynamic_sidebar( 'logo-right' );
                        }elseif( class_exists('ATA_WC_Smart_Popup_Cart') || class_exists('ATA_WC_Smart_Popup_Cart_PRO') ) {
                        	do_action('apsw_search_bar_preview');
                        }
                        ?>
						<button class="startup-shop-rd-navbar-toggle" tabindex="0" autofocus="true"><i class="icofont-navigation-menu"></i></button>
						
					</div>

					
				</div>
			</div>
            
            <nav id="navbar">
            <div class="container">
            	<div class="row">
                	<div class="col-md-9"> <?php $this->get_site_navigation();?></div>
                    <div class="col-md-3 text-right"><?php echo wp_kses( $this->get_site_header_icon(), $this->alowed_tags() ); ?></div>
                </div>
            </div>
            <div class="clearfix"></div>
            </nav>

           
            
		</header>
		<?php		
	}
	
	
	/**
	* Get the Site logo
	*
	* @return HTML
	*/
	public function get_site_branding (){
		
		$html = '<div class="logo-wrap">';
		
		if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
			$html .= get_custom_logo();
			
		}
		if ( display_header_text() == true ){
			
			$html .= '<h3><a href="'.esc_url( home_url( '/' ) ).'" rel="home" class="site-title">';
			$html .= get_bloginfo( 'name' );
			$html .= '</a></h3>';
			$description = get_bloginfo( 'description', 'display' );
		
			if ( $description ) :
			    $html .=  '<div class="site-description">'.esc_html($description).'</div>';
			endif;
		}
		
		
		$html .= '</div>';
		
		$html = apply_filters( 'get_site_branding_filter', $html );
		
		echo wp_kses( $html, $this->alowed_tags() );
		
	}
	
	/**
	* Get the Site Main Menu / Navigation
	*
	* @return HTML
	*/
	public function get_site_navigation (){
		
		?>
        <button class="startup-shop-navbar-close"><i class="icofont-ui-close"></i></button>
        <?php
        wp_nav_menu( array(
            'theme_location'    => 'menu-1',
            'depth'             => 3,
            'menu_class'  		=> 'startup-shop-main-menu navigation-menu',
            'container'			=> 'ul',
            //'fallback_cb'       => 'startup_shop_navwalker::fallback',
        ) );
       
		
	}
	/**
	* Get the Site Main Menu / Navigation
	*
	* @return HTML
	*/
	public function get_site_header_icon(){
	?>
    <ul class="social-icon">
    
    	<?php if( startup_shop_get_option('__fb_pro_link')!="" ) : ?> 
        <li><a href="<?php echo esc_url( startup_shop_get_option('__fb_pro_link') );?>" target="_blank" rel="nofollow"><i class="icofont-facebook"></i></a></li>
        <?php endif;?>
        
        <?php if( startup_shop_get_option('__tw_pro_link')!="" ) : ?> 
        <li><a href="<?php echo esc_url( startup_shop_get_option('__tw_pro_link') );?>" target="_blank" rel="nofollow"><i class="icofont-twitter"></i></a></li>
        <?php endif;?>
        
        <?php if( startup_shop_get_option('__you_pro_link')!="" ) : ?> 
        <li><a href="<?php echo esc_url( startup_shop_get_option('__you_pro_link') );?>" target="_blank" rel="nofollow"><i class="icofont-youtube-play"></i></a></li>
        <?php endif;?>
    </ul>	
	<?php
	}
	/**
	* Get the Site search bar
	*
	* @return HTML
	*/
	public function get_site_search_form (){
	?>
	<div class="fly-search-bar" id="fly-search-bar">
        <div class="container-wrap">
			<?php 
            $css =	'';
            if( class_exists('APSW_Product_Search_Finale_Class') && class_exists( 'WooCommerce' ) ){
           		 do_action('apsw_search_bar_preview');
           		 $css = 'active_product_search';
            }else{
            	get_search_form();
            }
            ?>
            <a href="javascript:void(0)" class="search-close-trigger <?php echo esc_attr( $css );?>"><i class="icofont-close"></i></a>
        </div>
	</div>		
	<?php
	}

	public function get_site_breadcrumb (){
		if ( is_404() || 'templates/without-hero.php' == get_page_template_slug() ) return;
		if( function_exists('bcn_display') && ( !is_home() || !is_front_page())  ):?>
        
           <div class="startup-shop-breadcrumbs-wrap"><div class="container"><div class="row"><div class="col-md-12">
           <ul class="startup-shop-breadcrumbs" typeof="BreadcrumbList" vocab="https://schema.org/">
                <?php bcn_display_list();?>
           </ul></div></div></div>
           </div>
            
        <?php
		endif; 
	}
	/**
	* Get the hero sections
	*
	* @return HTML
	*/
	public function site_hero_sections(){
		if ( is_404() || 'templates/without-hero.php' == get_page_template_slug() ) return;
		
		if ( is_front_page() && is_active_sidebar( 'slider' ) ) : 
		 dynamic_sidebar( 'slider' );
		else: 
		$header_image = get_header_image();
		?>
        	
		<div id="static_header_banner" class="header-style-1">

		    <div class="site-header-text-wrap">
		    	<?php echo wp_kses( $this->hero_block_heading(), $this->alowed_tags() );?>
		    </div>

		    <?php if( !empty( $header_image ) ) : ?>
		    <div class="site-header-bg-wrap">

		    	<div class="site-header-bg background-effect" style=" background-image: url(<?php echo esc_url( $header_image );?>); background-attachment: scroll; "></div>
		   </div>
		   <?php endif;?>

		</div>
		<?php
		endif;
	}
	/**
	 * Add Banner Title.
	 *
	 * @since 1.0.0
	 */
	function hero_block_heading() {
		 echo '<div class="site-header-text-wrap">';
		
			if ( is_home() ) {
					echo '<h1 class="page-title-text">';
					echo bloginfo( 'name' );
					echo '</h1>';
					echo '<p class="subtitle">';
					echo esc_html(get_bloginfo( 'description', 'display' ));
					echo '</p>';
			}else if ( function_exists('is_shop') && is_shop() ){
				if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
					echo '<h1 class="page-title-text">';
					echo esc_html( woocommerce_page_title() );
					echo '</h1>';
				}
			}else if( function_exists('is_product_category') && is_product_category() ){
				echo '<h1 class="page-title-text">';
				echo esc_html( woocommerce_page_title() );
				echo '</h1>';
				echo '<p class="subtitle">';
				do_action( 'woocommerce_archive_description' );
				echo '</p>';
				
			}elseif ( is_singular() ) {
				echo '<h1 class="page-title-text">';
					echo single_post_title( '', false );
				echo '</h1>';
			} elseif ( is_archive() ) {
				
				the_archive_title( '<h1 class="page-title-text">', '</h1>' );
				the_archive_description( '<p class="archive-description subtitle">', '</p>' );
				
			} elseif ( is_search() ) {
				echo '<h1 class="title">';
					printf( /* translators:straing */ esc_html__( 'Search Results for: %s', 'startup-shop' ),  get_search_query() );
				echo '</h1>';
			} elseif ( is_404() ) {
				echo '<h1 class="display-1">';
					esc_html_e( 'Oops! That page can&rsquo;t be found.', 'startup-shop' );
				echo '</h1>';
			}
		
		echo '</div>';
	}
	private function alowed_tags(){
		
		if( function_exists('startup_shop_alowed_tags') ){ 
			return startup_shop_alowed_tags(); 
		}else{
			return array();	
		}
		
	}
}

$startup_shop_Header_Layout = new startup_shop_Header_Layout();