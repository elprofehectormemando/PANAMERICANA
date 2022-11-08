<?php
/**
 * The template part for displaying Top header
 *
 * @package Ebook Store
 * @subpackage ebook_store
 * @since Ebook Store 1.0
 */
?>
<?php if( get_theme_mod( 'vw_book_store_topbar_hide_show', false) != '' || get_theme_mod( 'vw_book_store_resp_topbar_hide_show', false) != '') { ?>
  <div class="top-bar">
    <div class="container">
      <div class="row">
        <div class="col-lg-7 col-md-8 align-self-center">
          <div class="row">
            <div class="col-lg-3 col-md-3 align-self-center">
              <?php if ( get_theme_mod('vw_book_store_my_account_text','') != "" ) {?>
                <i class="<?php echo esc_attr(get_theme_mod('vw_book_store_header_my_account_icon','fas fa-user')); ?>"></i><a href="<?php echo esc_url( get_theme_mod('vw_book_store_my_account_link','') ); ?>"><?php echo esc_html( get_theme_mod('vw_book_store_my_account_text','') ); ?><span class="screen-reader-text"><?php esc_html_e( 'My Account','ebook-store' );?></span></a>
              <?php }?>
            </div>
            <div class="col-lg-2 col-md-3 align-self-center">
              <?php if ( get_theme_mod('vw_book_store_help_text','') != "" ) {?>
                <i class="<?php echo esc_attr(get_theme_mod('vw_book_store_help_icon','fas fa-question-circle')); ?>"></i><a href="<?php echo esc_url( get_theme_mod('vw_book_store_help_link','') ); ?>"><?php echo esc_html( get_theme_mod('vw_book_store_help_text','') ); ?><span class="screen-reader-text"><?php esc_html_e( 'Help','ebook-store' );?></span></a>
              <?php }?>
            </div>
            <div class="col-lg-7 col-md-6 align-self-center">
              <?php if ( get_theme_mod('vw_book_store_email','') != "" ) {?>
                <i class="<?php echo esc_attr(get_theme_mod('vw_book_store_email_icon','fas fa-envelope')); ?>"></i><span><a href="mailto:<?php echo esc_attr(get_theme_mod('vw_book_store_email',''));?>"><?php echo esc_html(get_theme_mod('vw_book_store_email',''));?></a></span>
              <?php }?>
            </div>
          </div>
        </div>
        <div class="col-lg-5 col-md-4 align-self-center">
          <?php dynamic_sidebar('social-icon'); ?>
        </div>
      </div>
    </div>
  </div>
<?php }?>