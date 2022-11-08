<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package VW Book Store
 */

get_header(); ?>

<main id="maincontent" role="main" class="content-vw">
	<div class="container">
        <div class="page-content">
        	<h1><?php echo esc_html(get_theme_mod('vw_book_store_404_page_title',__('404 Not Found','vw-book-store')));?></h1>	
			<p class="text-404"><?php echo esc_html(get_theme_mod('vw_book_store_404_page_content',__('Looks like you have taken a wrong turn, Dont worry, it happens to the best of us.','vw-book-store')));?></p>
			<?php if( get_theme_mod('vw_book_store_404_page_button_text','Return to the home page') != ''){ ?>
				<div class="error-btn">
	        		<a href="<?php echo esc_url(home_url()); ?>"><?php echo esc_html(get_theme_mod('vw_book_store_404_page_button_text',__('Return to the home page','vw-book-store')));?><span class="screen-reader-text"><?php echo esc_html(get_theme_mod('vw_book_store_404_page_button_text',__('Return to the home page','vw-book-store')));?></span></a>
				</div>
			<?php } ?>
			<div class="clearfix"></div>
        </div>
	</div>
</main>

<?php get_footer(); ?>