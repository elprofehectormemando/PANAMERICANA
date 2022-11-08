<?php
/**
 * The template part for displaying navigation
 *
 * @package VW Book Store 
 * @subpackage vw_book_store
 * @since VW Book Store 1.0
 */
?>
<?php
  $vw_book_store_search_hide_show = get_theme_mod( 'vw_book_store_search_hide_show' );
  if ( 'Disable' == $vw_book_store_search_hide_show ) {
   $colmd = 'col-lg-9 col-md-7';
  } else { 
   $colmd = 'col-lg-8 col-md-6 col-6';
  } 
?>

<div id="header" class="menubar <?php if( get_theme_mod( 'vw_book_store_sticky_header', false) != '' || get_theme_mod( 'vw_book_store_stickyheader_hide_show', false) != '') { ?> header-sticky"<?php } else { ?>close-sticky <?php } ?>">
  <div class="container">
    <div class="row bg-home">
      <div class="logo col-lg-3 col-md-5 align-self-center">
        <?php if ( has_custom_logo() ) : ?>
          <div class="site-logo"><?php the_custom_logo(); ?></div>
        <?php endif; ?>
        <?php $blog_info = get_bloginfo( 'name' ); ?>
          <?php if ( ! empty( $blog_info ) ) : ?>
            <?php if ( is_front_page() && is_home() ) : ?>
              <?php if( get_theme_mod('vw_book_store_logo_title_hide_show',true) != ''){ ?>
                <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
              <?php } ?>
            <?php else : ?>
              <?php if( get_theme_mod('vw_book_store_logo_title_hide_show',true) != ''){ ?>
                <p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
              <?php } ?>
            <?php endif; ?>
          <?php endif; ?>
          <?php
            $description = get_bloginfo( 'description', 'display' );
            if ( $description || is_customize_preview() ) :
          ?>
          <?php if( get_theme_mod('vw_book_store_tagline_hide_show',true) != ''){ ?>
            <p class="site-description">
              <?php echo esc_html($description); ?>
            </p>
          <?php } ?>
        <?php endif; ?>
      </div>
      <div class="<?php echo esc_html( $colmd ); ?> align-self-center">
        <?php if(has_nav_menu('primary')){ ?>
          <div class="toggle-nav mobile-menu">
            <button onclick="vw_book_store_menu_open_nav()" class="responsivetoggle"><i class="<?php echo esc_attr(get_theme_mod('vw_book_store_res_open_menus_icon','fas fa-bars')); ?>"></i><span class="screen-reader-text"><?php esc_html_e('Open Button','vw-book-store'); ?></span></button>
          </div>
        <?php } ?>
        <div id="mySidenav" class="nav sidenav">
          <nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Top Menu', 'vw-book-store' ); ?>">
            <?php 
              if(has_nav_menu('primary')){
                wp_nav_menu( array( 
                  'theme_location' => 'primary',
                  'container_class' => 'main-menu clearfix' ,
                  'menu_class' => 'clearfix',
                  'items_wrap' => '<ul id="%1$s" class="%2$s mobile_nav">%3$s</ul>',
                  'fallback_cb' => 'wp_page_menu',
                ) ); 
              } 
            ?>
             <a href="javascript:void(0)" class="closebtn mobile-menu" onclick="vw_book_store_menu_close_nav()"><i class="<?php echo esc_attr(get_theme_mod('vw_book_store_res_close_menu_icon','fas fa-times')); ?>"></i><span class="screen-reader-text"><?php esc_html_e('Close Button','vw-book-store'); ?></span></a>
          </nav>
        </div>
      </div>
      <?php if ( 'Disable' != $vw_book_store_search_hide_show ) {?>
        <div class="search-box col-lg-1 col-md-1 col-6 align-self-center">
          <span><a href="#"><i class="<?php echo esc_attr(get_theme_mod('vw_book_store_search_icon','fas fa-search')); ?>"></i></a></span>
        </div>
      <?php } ?>
    </div>
    <div class="serach_outer">
      <div class="closepop"><a href="#maincontent"><i class="<?php echo esc_attr(get_theme_mod('vw_book_store_search_close_icon','fa fa-window-close')); ?>"></i></a></div>
      <div class="serach_inner">
        <?php get_search_form(); ?>
      </div>
    </div>
  </div>
</div>