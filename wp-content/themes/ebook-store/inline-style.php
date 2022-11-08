<?php

	/*------------ First highlight color --------------*/

	$vw_book_store_first_color = get_theme_mod('vw_book_store_first_color');

	$vw_book_store_custom_css = '';

	if($vw_book_store_first_color != false){
		$vw_book_store_custom_css .='#preloader, #footer .tagcloud a:hover, nav.woocommerce-MyAccount-navigation ul li, .blogbutton-small, #banner .woocommerce ul.products li.product .price,  #banner .banner-btn a, #banner .woocommerce span.onsale, #slider .more-btn a, #slider .carousel-control-prev-icon, #slider .carousel-control-next-icon, #header, .search-bar button.product-btn, span.cart-value, span.wishlist-counter, .woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button, .woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt, input[type="submit"], input.button, #footer .tagcloud a:hover, .scrollup i, #footer-2, #sidebar h3, #sidebar .wp-block-search .wp-block-search__label, #comments input[type="submit"].submit, #comments a.comment-reply-link, #sidebar .tagcloud a:hover, #sidebar input[type="submit"], #footer .wp-block-search .wp-block-search__button, #sidebar .wp-block-search .wp-block-search__button, .woocommerce span.onsale, .products li:hover a.add_to_cart_button, .pagination span, .pagination a, .error-btn a, .woocommerce nav.woocommerce-pagination ul li a, #footer a.custom_read_more, #sidebar a.custom_read_more, .nav-previous a, #sidebar h2{';
			$vw_book_store_custom_css .='background-color: '.esc_attr($vw_book_store_first_color).';';
		$vw_book_store_custom_css .='}';
	}
	if($vw_book_store_first_color != false){
		$vw_book_store_custom_css .='.products li:hover a.add_to_cart_button, #footer .custom-social-icons i{';
			$vw_book_store_custom_css .='background: '.esc_attr($vw_book_store_first_color).'!important;';
		$vw_book_store_custom_css .='}';
	}
	if($vw_book_store_first_color != false){
		$vw_book_store_custom_css .='.top-bar{';
			$vw_book_store_custom_css .='background: rgba(0, 0, 0, 0)linear-gradient(120deg, '.esc_attr($vw_book_store_first_color).' 42%, #f4f3ec 16%) repeat scroll 0 0;';
		$vw_book_store_custom_css .='}';
	}
	if($vw_book_store_first_color != false){
		$vw_book_store_custom_css .='a, #footer h3, #footer .wp-block-search .wp-block-search__label, .entry-content a, #sidebar .textwidget a, #footer .textwidget a, .comment-body p a, .entry-summary a, #footer li a:hover, #sidebar ul li a:hover, .post-main-box:hover h2 a, .post-main-box:hover .post-info a, .single-post .post-info:hover a, #slider .inner_carousel h1 a:hover, .main-navigation ul.sub-menu a:hover, .wishlist a, .cart_icon i, .top-bar .custom-social-icons i:hover, .logo h1 a, .logo p.site-title a, .entry-content code{';
			$vw_book_store_custom_css .='color: '.esc_attr($vw_book_store_first_color).';';
		$vw_book_store_custom_css .='}';
	}
	if($vw_book_store_first_color != false){
		$vw_book_store_custom_css .='#sidebar ul li a:hover{';
			$vw_book_store_custom_css .='color: '.esc_attr($vw_book_store_first_color).'!important;';
		$vw_book_store_custom_css .='}';
	}
	if($vw_book_store_first_color != false){
		$vw_book_store_custom_css .='.cart_icon i, .wishlist i{';
			$vw_book_store_custom_css .='border-color: '.esc_attr($vw_book_store_first_color).';';
		$vw_book_store_custom_css .='}';
	}
	if($vw_book_store_first_color != false){
		$vw_book_store_custom_css .='.main-navigation ul ul, #banner .woocommerce span.onsale:before{';
			$vw_book_store_custom_css .='border-top-color: '.esc_attr($vw_book_store_first_color).';';
		$vw_book_store_custom_css .='}';
	}
	if($vw_book_store_first_color != false){
		$vw_book_store_custom_css .='.main-navigation ul ul{';
			$vw_book_store_custom_css .='border-bottom-color: '.esc_attr($vw_book_store_first_color).';';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_custom_css .='@media screen and (max-width:768px) {';
		if($vw_book_store_first_color != false){
			$vw_book_store_custom_css .='.top-bar{
			background-color:'.esc_attr($vw_book_store_first_color).';
			}';
		}
	$vw_book_store_custom_css .='}';

	$vw_book_store_custom_css .='@media screen and (max-width:1000px) {';
		if($vw_book_store_first_color != false){
			$vw_book_store_custom_css .='.main-navigation a:hover, .page-template-custom-home-page .main-navigation .current_page_item > a, .page-template-custom-home-page .main-navigation .current-menu-item > a{
			color:'.esc_attr($vw_book_store_first_color).';
			}';
		}
	$vw_book_store_custom_css .='}';

	/*---------------------------Width Layout -------------------*/

	$vw_book_store_theme_lay = get_theme_mod( 'vw_book_store_width_option','Full Width');

    if($vw_book_store_theme_lay == 'Boxed'){
		$vw_book_store_custom_css .='body{';
			$vw_book_store_custom_css .='max-width: 1140px; width: 100%; padding-right: 15px; padding-left: 15px; margin-right: auto; margin-left: auto;';
		$vw_book_store_custom_css .='}';
		$vw_book_store_custom_css .='.scrollup i{';
			$vw_book_store_custom_css .='right: 100px;';
		$vw_book_store_custom_css .='}';
		$vw_book_store_custom_css .='.scrollup.left i{';
			$vw_book_store_custom_css .='left: 100px;';
		$vw_book_store_custom_css .='}';
	}else if($vw_book_store_theme_lay == 'Wide Width'){
		$vw_book_store_custom_css .='body{';
			$vw_book_store_custom_css .='width: 100%;padding-right: 15px;padding-left: 15px;margin-right: auto;margin-left: auto;';
		$vw_book_store_custom_css .='}';
		$vw_book_store_custom_css .='.scrollup i{';
			$vw_book_store_custom_css .='right: 30px;';
		$vw_book_store_custom_css .='}';
		$vw_book_store_custom_css .='.scrollup.left i{';
			$vw_book_store_custom_css .='left: 30px;';
		$vw_book_store_custom_css .='}';
	}else if($vw_book_store_theme_lay == 'Full Width'){
		$vw_book_store_custom_css .='body{';
			$vw_book_store_custom_css .='max-width: 100%;';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_header_menus_color = get_theme_mod('vw_book_store_header_menus_color');
	if($vw_book_store_header_menus_color != false){
		$vw_book_store_custom_css .='.main-navigation a{';
			$vw_book_store_custom_css .='color: '.esc_attr($vw_book_store_header_menus_color).';';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_header_menus_hover_color = get_theme_mod('vw_book_store_header_menus_hover_color');
	if($vw_book_store_header_menus_hover_color != false){
		$vw_book_store_custom_css .='.main-navigation a:hover, .main-navigation .current_page_item > a, .main-navigation .current-menu-item > a{';
			$vw_book_store_custom_css .='color: '.esc_attr($vw_book_store_header_menus_hover_color).';';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_header_submenus_color = get_theme_mod('vw_book_store_header_submenus_color');
	if($vw_book_store_header_submenus_color != false){
		$vw_book_store_custom_css .='.main-navigation ul ul a{';
			$vw_book_store_custom_css .='color: '.esc_attr($vw_book_store_header_submenus_color).';';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_header_submenus_hover_color = get_theme_mod('vw_book_store_header_submenus_hover_color');
	if($vw_book_store_header_submenus_hover_color != false){
		$vw_book_store_custom_css .='.main-navigation ul.sub-menu a:hover{';
			$vw_book_store_custom_css .='color: '.esc_attr($vw_book_store_header_submenus_hover_color).';';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_resp_menu_toggle_btn_bg_color = get_theme_mod('vw_book_store_resp_menu_toggle_btn_bg_color');
	if($vw_book_store_resp_menu_toggle_btn_bg_color != false){
		$vw_book_store_custom_css .='.toggle-nav i{';
			$vw_book_store_custom_css .='background-color: '.esc_attr($vw_book_store_resp_menu_toggle_btn_bg_color).';';
		$vw_book_store_custom_css .='}';
	}

	/*------------------ Preloader Background Color  -------------------*/

	$vw_book_store_preloader_bg_color = get_theme_mod('vw_book_store_preloader_bg_color');
	if($vw_book_store_preloader_bg_color != false){
		$vw_book_store_custom_css .='#preloader{';
			$vw_book_store_custom_css .='background-color: '.esc_attr($vw_book_store_preloader_bg_color).';';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_preloader_border_color = get_theme_mod('vw_book_store_preloader_border_color');
	if($vw_book_store_preloader_border_color != false){
		$vw_book_store_custom_css .='.loader-line{';
			$vw_book_store_custom_css .='border-color: '.esc_attr($vw_book_store_preloader_border_color).'!important;';
		$vw_book_store_custom_css .='}';
	}