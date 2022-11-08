<?php
/**
 * VW Book Store: Block Patterns
 *
 * @package VW Book Store
 * @since   1.0.0
 */

/**
 * Register Block Pattern Category.
 */
if ( function_exists( 'register_block_pattern_category' ) ) {

	register_block_pattern_category(
		'vw-book-store',
		array( 'label' => __( 'VW Book Store', 'vw-book-store' ) )
	);
}

/**
 * Register Block Patterns.
 */
if ( function_exists( 'register_block_pattern' ) ) {
	register_block_pattern(
		'vw-book-store/banner-section',
		array(
			'title'      => __( 'Banner Section', 'vw-book-store' ),
			'categories' => array( 'vw-book-store' ),
			'content'    => "<!-- wp:cover {\"url\":\"" . esc_url(get_template_directory_uri()) . "/inc/block-patterns/images/banner.png\",\"id\":263,\"dimRatio\":50,\"align\":\"full\",\"className\":\"banner-section\"} -->\n<div class=\"wp-block-cover alignfull banner-section\"><span aria-hidden=\"true\" class=\"wp-block-cover__gradient-background has-background-dim\"></span><img class=\"wp-block-cover__image-background wp-image-263\" alt=\"\" src=\"" . esc_url(get_template_directory_uri()) . "/inc/block-patterns/images/banner.png\" data-object-fit=\"cover\"/><div class=\"wp-block-cover__inner-container\"><!-- wp:columns {\"align\":\"wide\",\"className\":\"m-0\"} -->\n<div class=\"wp-block-columns alignwide m-0\"><!-- wp:column {\"width\":\"25%\"} -->\n<div class=\"wp-block-column\" style=\"flex-basis:25%\"></div>\n<!-- /wp:column -->\n\n<!-- wp:column {\"width\":\"50%\",\"className\":\"slider-cont\"} -->\n<div class=\"wp-block-column slider-cont\" style=\"flex-basis:50%\"><!-- wp:heading {\"textAlign\":\"center\",\"level\":1,\"style\":{\"typography\":{\"fontSize\":35}},\"textColor\":\"white\"} -->\n<h1 class=\"has-text-align-center has-white-color has-text-color\" style=\"font-size:35px\">LOREM IPSUM DAMET, CONSECTETUR ELIT </h1>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph {\"align\":\"center\",\"style\":{\"typography\":{\"fontSize\":14}},\"textColor\":\"white\",\"className\":\"text-center px-5 mx-5\"} -->\n<p class=\"has-text-align-center text-center px-5 mx-5 has-white-color has-text-color\" style=\"font-size:14px\">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:buttons {\"className\":\"text-center mt-3\"} -->\n<div class=\"wp-block-buttons text-center mt-3\"><!-- wp:button {\"textColor\":\"white\",\"align\":\"center\",\"style\":{\"color\":{\"background\":\"#f06845\"},\"border\":{\"radius\":\"3px\"}}} -->\n<div class=\"wp-block-button aligncenter\"><a class=\"wp-block-button__link has-white-color has-text-color has-background\" style=\"border-radius:3px;background-color:#f06845\">READ MORE</a></div>\n<!-- /wp:button --></div>\n<!-- /wp:buttons --></div>\n<!-- /wp:column -->\n\n<!-- wp:column {\"width\":\"25%\"} -->\n<div class=\"wp-block-column\" style=\"flex-basis:25%\"></div>\n<!-- /wp:column --></div>\n<!-- /wp:columns --></div></div>\n<!-- /wp:cover -->",
		)
	);

	register_block_pattern(
		'vw-book-store/products-section',
		array(
			'title'      => __( 'Products Section', 'vw-book-store' ),
			'categories' => array( 'vw-book-store' ),
			'content'    => "<!-- wp:group {\"align\":\"wide\",\"className\":\"products-section py-5 m-0\"} -->\n<div class=\"wp-block-group alignwide products-section py-5 m-0\"><div class=\"wp-block-group__inner-container\"><!-- wp:heading {\"align\":\"wide\",\"className\":\"pb-2 mx-0\",\"style\":{\"typography\":{\"fontSize\":28},\"color\":{\"text\":\"#3f3f3f\"}}} -->\n<h2 class=\"alignwide pb-2 mx-0 has-text-color\" style=\"color:#3f3f3f;font-size:28px\">LOREM IPSUM</h2>\n<!-- /wp:heading -->\n\n<!-- wp:woocommerce/product-category {\"columns\":4,\"rows\":1,\"categories\":[17],\"contentVisibility\":{\"title\":true,\"price\":true,\"rating\":false,\"button\":true},\"align\":\"wide\",\"className\":\"m-0\"} /--></div></div>\n<!-- /wp:group -->",
		)
	);
}