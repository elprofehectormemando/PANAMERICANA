<?php
/**
 *  Ebook Store: Block Patterns
 *
 * @package  Ebook Store
 * @since   1.0.0
 */

/**
 * Register Block Pattern Category.
 */
if ( function_exists( 'register_block_pattern_category' ) ) {

	register_block_pattern_category(
		'ebook-store',
		array( 'label' => __( 'Ebook Store', 'ebook-store' ) )
	);
}

/**
 * Register Block Patterns.
 */
if ( function_exists( 'register_block_pattern' ) ) {
	register_block_pattern(
		'ebook-store/slider-section',
		array(
			'title'      => __( 'Slider Section', 'ebook-store' ),
			'categories' => array( 'ebook-store' ),
			'content'    => "<!-- wp:cover {\"url\":\"" . get_theme_file_uri() . "/inc/block-patterns/images/slider.png\",\"id\":44,\"dimRatio\":50,\"minHeight\":550,\"align\":\"full\",\"className\":\"slider-section\"} -->\n<div class=\"wp-block-cover alignfull slider-section\" style=\"min-height:550px\"><span aria-hidden=\"true\" class=\"wp-block-cover__background has-background-dim\"></span><img class=\"wp-block-cover__image-background wp-image-44\" alt=\"\" src=\"" . get_theme_file_uri() . "/inc/block-patterns/images/slider.png\" data-object-fit=\"cover\"/><div class=\"wp-block-cover__inner-container\"><!-- wp:heading {\"textAlign\":\"center\",\"level\":1,\"style\":{\"typography\":{\"fontSize\":\"35px\",\"fontStyle\":\"normal\",\"fontWeight\":\"800\"}}} -->\n<h1 class=\"has-text-align-center\" style=\"font-size:35px;font-style:normal;font-weight:800\">WE CAN HELP GET YOUR BOOKS IN ORDER</h1>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph {\"align\":\"center\",\"style\":{\"typography\":{\"fontSize\":\"16px\"}}} -->\n<p class=\"has-text-align-center\" style=\"font-size:16px\">Lorem ipsum&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:buttons {\"layout\":{\"type\":\"flex\",\"justifyContent\":\"center\"}} -->\n<div class=\"wp-block-buttons\"><!-- wp:button {\"textColor\":\"white\",\"style\":{\"typography\":{\"fontSize\":\"16px\"},\"border\":{\"radius\":\"0px\"},\"color\":{\"background\":\"#f06845\"}},\"className\":\"slider-btn\"} -->\n<div class=\"wp-block-button has-custom-font-size slider-btn\" style=\"font-size:16px\"><a class=\"wp-block-button__link has-white-color has-text-color has-background\" style=\"border-radius:0px;background-color:#f06845\">READ MORE</a></div>\n<!-- /wp:button --></div>\n<!-- /wp:buttons --></div></div>\n<!-- /wp:cover -->",
		)
	);

	register_block_pattern(
		'ebook-store/banner-section',
		array(
			'title'      => __( 'Banner-Section', 'ebook-store' ),
			'categories' => array( 'ebook-store' ),
			'content'    => "<!-- wp:columns {\"className\":\"banner-section mt-5 mt-lg-5 mt-md-5\"} -->\n<div class=\"wp-block-columns banner-section mt-5 mt-lg-5 mt-md-5\"><!-- wp:column {\"width\":\"70%\",\"className\":\"banner-section-content\"} -->\n<div class=\"wp-block-column banner-section-content\" style=\"flex-basis:70%\"><!-- wp:cover {\"url\":\"" . get_theme_file_uri() . "/inc/block-patterns/images/banner-section-bg.png\",\"id\":52,\"dimRatio\":0,\"isDark\":false} -->\n<div class=\"wp-block-cover is-light\"><span aria-hidden=\"true\" class=\"wp-block-cover__background has-background-dim-0 has-background-dim\"></span><img class=\"wp-block-cover__image-background wp-image-52\" alt=\"\" src=\"" . get_theme_file_uri() . "/inc/block-patterns/images/banner-section-bg.png\" data-object-fit=\"cover\"/><div class=\"wp-block-cover__inner-container\"><!-- wp:columns {\"verticalAlignment\":\"center\",\"className\":\"banner-box\"} -->\n<div class=\"wp-block-columns are-vertically-aligned-center banner-box\"><!-- wp:column {\"verticalAlignment\":\"center\",\"width\":\"75%\"} -->\n<div class=\"wp-block-column is-vertically-aligned-center\" style=\"flex-basis:75%\"><!-- wp:heading {\"level\":3,\"style\":{\"typography\":{\"fontSize\":\"28px\",\"fontStyle\":\"normal\",\"fontWeight\":\"700\"},\"color\":{\"text\":\"#1a1616\"}}} -->\n<h3 class=\"has-text-color\" style=\"color:#1a1616;font-size:28px;font-style:normal;font-weight:700\">The Best New Books Of This Month</h3>\n<!-- /wp:heading --></div>\n<!-- /wp:column -->\n\n<!-- wp:column {\"verticalAlignment\":\"center\",\"width\":\"25%\"} -->\n<div class=\"wp-block-column is-vertically-aligned-center\" style=\"flex-basis:25%\"><!-- wp:buttons -->\n<div class=\"wp-block-buttons\"><!-- wp:button {\"textColor\":\"white\",\"style\":{\"typography\":{\"fontSize\":\"15px\"},\"color\":{\"background\":\"#f06845\"},\"border\":{\"radius\":\"0px\"}},\"className\":\"banner-btn\"} -->\n<div class=\"wp-block-button has-custom-font-size banner-btn\" style=\"font-size:15px\"><a class=\"wp-block-button__link has-white-color has-text-color has-background\" style=\"border-radius:0px;background-color:#f06845\">READ MORE</a></div>\n<!-- /wp:button --></div>\n<!-- /wp:buttons --></div>\n<!-- /wp:column --></div>\n<!-- /wp:columns --></div></div>\n<!-- /wp:cover --></div>\n<!-- /wp:column -->\n\n<!-- wp:column {\"width\":\"30%\",\"className\":\"product-section\"} -->\n<div class=\"wp-block-column product-section\" style=\"flex-basis:30%\"><!-- wp:woocommerce/product-category {\"columns\":1,\"rows\":1,\"categories\":[15],\"contentVisibility\":{\"image\":true,\"title\":true,\"price\":true,\"rating\":true,\"button\":true}} /--></div>\n<!-- /wp:column --></div>\n<!-- /wp:columns -->",
		)
	);
}