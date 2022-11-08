<?php
/**
 * The template part for displaying navigation
 *
 * @package Ebook Store
 * @subpackage ebook_store
 * @since Ebook Store 1.0
 */
?>

<div id="header" class="menubar <?php if( get_theme_mod( 'vw_book_store_sticky_header', false) != '' || get_theme_mod( 'vw_book_store_stickyheader_hide_show', false) != '') { ?> header-sticky"<?php } else { ?>close-sticky <?php } ?>">
  <div class="container">
    <div class="row bg-home">
      <div class="col-lg-9 col-md-9 col-4 align-self-center">
        <?php if(has_nav_menu('primary')){ ?>
          <div class="toggle-nav mobile-menu">
            <button onclick="vw_book_store_menu_open_nav()" class="responsivetoggle"><i class="<?php echo esc_attr(get_theme_mod('vw_book_store_res_open_menus_icon','fas fa-bars')); ?>"></i><span class="screen-reader-text"><?php esc_html_e('Open Button','ebook-store'); ?></span></button>
          </div>
        <?php } ?>
        <div id="mySidenav" class="nav sidenav">
          <nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Top Menu', 'ebook-store' ); ?>">
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
             <a href="javascript:void(0)" class="closebtn mobile-menu" onclick="vw_book_store_menu_close_nav()"><i class="<?php echo esc_attr(get_theme_mod('vw_book_store_res_close_menu_icon','fas fa-times')); ?>"></i><span class="screen-reader-text"><?php esc_html_e('Close Button','ebook-store'); ?></span></a>
          </nav>
        </div>
      </div>
      <div class="col-lg-3 col-md-3 col-8 align-self-center">
        <?php if(get_theme_mod('ebook_store_discount_text') != '') {?>
          <p class="discount-text mb-0 text-lg-end"><?php echo esc_html(get_theme_mod('ebook_store_discount_text',__('Get discount text','ebook-store'))); ?> <a target="_blank" href="<?php echo esc_url( get_theme_mod('ebook_store_discount_sale_link','') ); ?>"><?php echo esc_html(get_theme_mod('ebook_store_discount_sale_text',__('season sale','ebook-store'))); ?></a></p>
        <?php }?>
      </div>
    </div>
  </div>
</div>