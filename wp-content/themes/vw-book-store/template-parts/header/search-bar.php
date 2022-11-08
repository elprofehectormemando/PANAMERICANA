<?php
/**
 * The template part for displaying woocommerce info
 *
 * @package VW Book Store 
 * @subpackage vw_book_store
 * @since VW Book Store 1.0
 */
?>

<div class="search-bar">
  <div class="container">
    <?php if(class_exists('woocommerce')){ ?>
    <div class="row">
      <div class="col-lg-3 col-md-4">
        <?php if( get_theme_mod('vw_book_store_category_text','ALL CATEGORIES') != ''){ ?>
          <button class="product-btn"><?php echo esc_html(get_theme_mod('vw_book_store_category_text','ALL CATEGORIES'));?><i class="fa fa-bars" aria-hidden="true"></i></button>
        <?php } ?>
        <div class="product-cat">
          <?php
            $args = array(
              //'number'     => $number,
              'orderby'    => 'title',
              'order'      => 'ASC',
              'hide_empty' => 0,
              'parent'  => 0
              //'include'    => $ids
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
      <div class="col-lg-7 col-md-7">
        <?php get_product_search_form()?>
      </div>
      <div class="col-lg-2 col-md-1">
        <div class="cart_icon">
          <a href="<?php echo esc_url( get_theme_mod('vw_book_store_cart_link','' )); ?>"><i class="<?php echo esc_attr(get_theme_mod('vw_book_store_cart_icon','fas fa-shopping-bag')); ?>"></i><span class="screen-reader-text"><?php esc_html_e( 'Cart','vw-book-store' );?></span></a>
          <?php if(get_theme_mod('vw_book_store_cart_link',true) != ''){ ?>
            <li class="cart_box">
              <span class="cart-value"> <?php echo esc_html(wp_kses_data( WC()->cart->get_cart_contents_count())); ?></span>
            </li> 
          <?php }?>
        </div>
      </div>
    </div>
    <?php }else {
      echo '<h6>'.esc_html('Please Install Woocommerce Plugin','vw-book-store').'<h6>'; }?>
  </div>
</div>