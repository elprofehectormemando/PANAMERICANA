<?php
/**
 * Template part for displaying results in search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package startup-shop
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( array('startup-shop-blogwrap') ); ?>>

     <?php
    /**
    * Hook - startup_shop_posts_blog_media.
    *
    * @hooked startup_shop_posts_formats_thumbnail - 10
    */
    do_action( 'startup_shop_posts_blog_media' );
    ?>
    <div class="post">
               
        <?php
        /**
        * Hook - diet_shop_site_content_type.
        *
        * @hooked site_loop_heading - 10
        * @hooked render_meta_list  - 20
        * @hooked site_content_type - 30
        */
        
        $meta = array();
        
        if ( is_singular() ) :
            
            if( startup_shop_get_option('signle_meta_hide') != true ){
                
                $meta = array( 'author', 'date', 'category', 'comments' );
            }
            $meta    = apply_filters( 'startup_shop_single_post_meta', $meta );
            
        else :
            if( startup_shop_get_option('blog_meta_hide') != true ){
                
                $meta = array( 'author', 'date', 'category', 'comments' );
            }
            $meta    = apply_filters( 'startup_shop_blog_meta', $meta );
         endif;
    
        if( get_post_type( get_the_ID() ) == 'page' ){
            $meta    = array('');
        }
        do_action( 'startup_shop_site_content_type', $meta  );
        ?>
      
       
    </div>
    
</article><!-- #post-<?php the_ID(); ?> -->
