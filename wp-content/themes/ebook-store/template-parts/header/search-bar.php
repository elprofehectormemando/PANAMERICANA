<?php
/**
 * The template part for displaying woocommerce info
 *
 * @package Ebook Store
 * @subpackage ebook_store
 * @since Ebook Store 1.0
 */
?>

<div class="search-bar">
  <div class="container">
    <div class="row">
      <div class="logo col-lg-3 col-md-3 col-12 align-self-center">
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

      <?php if(class_exists('woocommerce')){ ?>
        <div class="col-lg-3 col-md-3 col-12 align-self-center">
          <?php if( get_theme_mod('vw_book_store_category_text','ALL CATEGORIES') != ''){ ?>
            <div class="text-lg-end text-md-center text-center mb-lg-0 mb-md-0 mb-2">
              <button class="product-btn"><?php echo esc_html(get_theme_mod('vw_book_store_category_text','ALL CATEGORIES'));?><i class="fa fa-bars" aria-hidden="true"></i></button>
            </div>
          <?php } ?>
          <div class="product-cat">
            <?php
              $args = array(
                'orderby'    => 'title',
                'order'      => 'ASC',
                'hide_empty' => 0,
                'parent'  => 0
              );
              $product_categories = get_terms( 'product_cat', $args );
              $count = count($product_categories);
              if ( $count > 0 ){
                foreach ( $product_categories as $product_category ) {
                  $product_category_id   = $product_category->term_id;
                  $cat_link = get_category_link( $product_category_id );
                  if ($product_category->category_parent == 0) { ?>
                <li class="drp_dwn_menu"><a href="<?php echo esc_url(get_term_link( $product_category ) ); ?>">
                <?php
              }
              echo esc_html( $product_category->name ); ?></a><i class="fas fa-chevron-right"></i></li>
                <?php
                }
              }
            ?>
          </div>
        </div>
        <div class="col-lg-4 col-md-4 col-12 align-self-center">
          <div class="mb-lg-0 mb-md-0 mb-2">
            <?php get_product_search_form()?>
          </div>
        </div>
        <div class="col-lg-2 col-md-2 col-12 align-self-center">
          <div class="text-lg-end text-md-center text-center">
            <span class="wishlist mt-2 mt-lg-0">
              <?php if(defined('YITH_WCWL')){ ?>
                <a class="wishlist_view position-relative" href="<?php echo YITH_WCWL()->get_wishlist_url(); ?>"><i class="fas fa-heart"></i>
                <?php $wishlist_count = YITH_WCWL()->count_products(); ?>
                <span class="wishlist-counter"><?php echo $wishlist_count; ?></span></a>
              <?php }?>
            </span>
            <span class="cart_icon">
              <a href="<?php if(function_exists('wc_get_cart_url')){ echo esc_url(wc_get_cart_url()); } ?>" title="<?php esc_attr_e( 'shopping cart','ebook-store' ); ?>"><i class="<?php echo esc_attr(get_theme_mod('vw_book_store_cart_icon','fas fa-shopping-bag')); ?>"></i><span class="screen-reader-text"><?php esc_html_e( 'shopping cart','ebook-store' );?></span></a>
              <?php if(get_theme_mod('vw_book_store_cart_link',true) != ''){ ?>
                <li class="cart_box">
                  <span class="cart-value"> <?php echo wp_kses_data( WC()->cart->get_cart_contents_count() );?></span>
                </li> 
              <?php }?>
            </span>
          </div>
        </div>
      <?php } ?>
    </div>
  </div>
</div>