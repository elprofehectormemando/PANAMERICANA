<?php
	
	/*-----------------------First highlight color-------------------*/

	$vw_book_store_first_color = get_theme_mod('vw_book_store_first_color');

	$vw_book_store_custom_css = '';

	if($vw_book_store_first_color != false){
		$vw_book_store_custom_css .=' .search-bar, .more-btn a:hover, .woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button, .woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt, input[type="submit"], #footer .tagcloud a:hover, #sidebar .custom-social-icons i, #footer .custom-social-icons i, #footer-2, #sidebar input[type="submit"], #sidebar .tagcloud a:hover, nav.woocommerce-MyAccount-navigation ul li, .blogbutton-small, #comments input[type="submit"].submit, .pagination span, .pagination a, #comments a.comment-reply-link, #footer a.custom_read_more, #sidebar a.custom_read_more, .nav-previous a:hover, .nav-next a:hover, .woocommerce nav.woocommerce-pagination ul li a, #preloader, #footer .wp-block-search .wp-block-search__button, #sidebar .wp-block-search .wp-block-search__button{';
			$vw_book_store_custom_css .='background-color: '.esc_attr($vw_book_store_first_color).';';
		$vw_book_store_custom_css .='}';
	}
	if($vw_book_store_first_color != false){
		$vw_book_store_custom_css .='a, .logo h1 a, button.product-btn, #footer h3, .woocommerce-message::before, .post-navigation a:hover .post-title, .post-navigation a:focus .post-title,#footer li a:hover, #sidebar ul li a, .main-navigation ul.sub-menu a:hover, .page-template-custom-home-page .main-navigation .current_page_item > a, .page-template-custom-home-page .main-navigation .current-menu-item > a, .logo h1 a, .logo p.site-title a, .post-main-box:hover h2 a, .post-main-box:hover .post-info a, .single-post .post-info:hover a, #slider .inner_carousel h1 a:hover, #footer .wp-block-search .wp-block-search__label{';
			$vw_book_store_custom_css .='color: '.esc_attr($vw_book_store_first_color).';';
		$vw_book_store_custom_css .='}';
	}
	if($vw_book_store_first_color != false){
		$vw_book_store_custom_css .='.woocommerce-message{';
			$vw_book_store_custom_css .='border-top-color: '.esc_attr($vw_book_store_first_color).';';
		$vw_book_store_custom_css .='}';
	}
	if($vw_book_store_first_color != false){
		$vw_book_store_custom_css .='.main-navigation ul ul{';
			$vw_book_store_custom_css .='border-color: '.esc_attr($vw_book_store_first_color).';';
		$vw_book_store_custom_css .='}';
	}

	/*------------------Second highlight color-------------------*/

	$vw_book_store_second_color = get_theme_mod('vw_book_store_second_color');

	if($vw_book_store_second_color != false){
		$vw_book_store_custom_css .='.search-bar button[type="submit"], span.cart-value, #slider .carousel-control-prev-icon, #slider .carousel-control-next-icon, .more-btn a, .scrollup i, .woocommerce span.onsale, .woocommerce #respond input#submit:hover, .woocommerce a.button:hover, .woocommerce button.button:hover, .woocommerce input.button:hover, .woocommerce #respond input#submit.alt:hover, .woocommerce a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce input.button.alt:hover, #sidebar .custom-social-icons i:hover, #footer .custom-social-icons i:hover, .blogbutton-small:hover, #footer .widget_price_filter .ui-slider .ui-slider-range, #footer .widget_price_filter .ui-slider .ui-slider-handle, #footer .woocommerce-product-search button, #sidebar .woocommerce-product-search button, #sidebar .widget_price_filter .ui-slider .ui-slider-range, #sidebar .widget_price_filter .ui-slider .ui-slider-handle, #footer a.custom_read_more:hover{';
			$vw_book_store_custom_css .='background-color: '.esc_attr($vw_book_store_second_color).';';
		$vw_book_store_custom_css .='}';
	}

	if($vw_book_store_second_color != false){
		$vw_book_store_custom_css .='.woocommerce #respond input#submit:hover, .woocommerce a.button:hover, .woocommerce button.button:hover, .woocommerce input.button:hover, .woocommerce #respond input#submit.alt:hover, .woocommerce a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce input.button.alt:hover{';
			$vw_book_store_custom_css .='background-color: '.esc_attr($vw_book_store_second_color).'!important;';
		$vw_book_store_custom_css .='}';
	}

	if($vw_book_store_second_color != false){
		$vw_book_store_custom_css .='.search-box i, .top-bar .custom-social-icons i:hover, .post-main-box h3 a, .entry-content a, .main-navigation .current_page_item > a, .main-navigation .current-menu-item > a, .main-navigation a:hover, .post-main-box h2 a, h2.section-title a, #footer .textwidget a, .logo .site-title a:hover, .top-bar a:hover, .entry-summary a, #sidebar .wp-block-search .wp-block-search__label{';
			$vw_book_store_custom_css .='color: '.esc_attr($vw_book_store_second_color).';';
		$vw_book_store_custom_css .='}';
	}
	if($vw_book_store_second_color != false){
		$vw_book_store_custom_css .='#sidebar h3{';
			$vw_book_store_custom_css .='color: '.esc_attr($vw_book_store_second_color).'!important;';
		$vw_book_store_custom_css .='}';
	}
	if($vw_book_store_second_color != false){
		$vw_book_store_custom_css .='
		@media screen and (max-width:1000px){
			.toggle-nav i, .main-navigation ul.sub-menu a:hover{';
			$vw_book_store_custom_css .='color: '.esc_attr($vw_book_store_second_color).';';
		$vw_book_store_custom_css .='} }';
	}
	if($vw_book_store_second_color != false){
		$vw_book_store_custom_css .='#header .nav ul.sub-menu li a:hover, nav.woocommerce-MyAccount-navigation ul li{';
			$vw_book_store_custom_css .='border-left-color: '.esc_attr($vw_book_store_second_color).';';
		$vw_book_store_custom_css .='}';
	}
	if($vw_book_store_second_color != false){
		$vw_book_store_custom_css .='#footer .wp-block-search .wp-block-search__button, #sidebar input[type="submit"], #sidebar .wp-block-search .wp-block-search__button{
		box-shadow: 5px 5px 0 0 '.esc_attr($vw_book_store_second_color).';
		}';
	}

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

	/*--------------------------- Slider Opacity -------------------*/

	$vw_book_store_theme_lay = get_theme_mod( 'vw_book_store_slider_opacity_color','0.5');

	if($vw_book_store_theme_lay == '0'){
		$vw_book_store_custom_css .='#slider img{';
			$vw_book_store_custom_css .='opacity:0';
		$vw_book_store_custom_css .='}';
		}else if($vw_book_store_theme_lay == '0.1'){
		$vw_book_store_custom_css .='#slider img{';
			$vw_book_store_custom_css .='opacity:0.1';
		$vw_book_store_custom_css .='}';
		}else if($vw_book_store_theme_lay == '0.2'){
		$vw_book_store_custom_css .='#slider img{';
			$vw_book_store_custom_css .='opacity:0.2';
		$vw_book_store_custom_css .='}';
		}else if($vw_book_store_theme_lay == '0.3'){
		$vw_book_store_custom_css .='#slider img{';
			$vw_book_store_custom_css .='opacity:0.3';
		$vw_book_store_custom_css .='}';
		}else if($vw_book_store_theme_lay == '0.4'){
		$vw_book_store_custom_css .='#slider img{';
			$vw_book_store_custom_css .='opacity:0.4';
		$vw_book_store_custom_css .='}';
		}else if($vw_book_store_theme_lay == '0.5'){
		$vw_book_store_custom_css .='#slider img{';
			$vw_book_store_custom_css .='opacity:0.5';
		$vw_book_store_custom_css .='}';
		}else if($vw_book_store_theme_lay == '0.6'){
		$vw_book_store_custom_css .='#slider img{';
			$vw_book_store_custom_css .='opacity:0.6';
		$vw_book_store_custom_css .='}';
		}else if($vw_book_store_theme_lay == '0.7'){
		$vw_book_store_custom_css .='#slider img{';
			$vw_book_store_custom_css .='opacity:0.7';
		$vw_book_store_custom_css .='}';
		}else if($vw_book_store_theme_lay == '0.8'){
		$vw_book_store_custom_css .='#slider img{';
			$vw_book_store_custom_css .='opacity:0.8';
		$vw_book_store_custom_css .='}';
		}else if($vw_book_store_theme_lay == '0.9'){
		$vw_book_store_custom_css .='#slider img{';
			$vw_book_store_custom_css .='opacity:0.9';
		$vw_book_store_custom_css .='}';
		}

	/*---------------------Slider Content Layout -------------------*/

	$vw_book_store_theme_lay = get_theme_mod( 'vw_book_store_slider_content_option','Center');
	
    if($vw_book_store_theme_lay == 'Left'){
		$vw_book_store_custom_css .='#slider .carousel-caption, #slider .inner_carousel, #slider .inner_carousel h1{';
			$vw_book_store_custom_css .='text-align:left; left:15%; right:45%;';
		$vw_book_store_custom_css .='}';
	}else if($vw_book_store_theme_lay == 'Center'){
		$vw_book_store_custom_css .='#slider .carousel-caption, #slider .inner_carousel, #slider .inner_carousel h1{';
			$vw_book_store_custom_css .='text-align:center; left:20%; right:20%;';
		$vw_book_store_custom_css .='}';
	}else if($vw_book_store_theme_lay == 'Right'){
		$vw_book_store_custom_css .='#slider .carousel-caption, #slider .inner_carousel, #slider .inner_carousel h1{';
			$vw_book_store_custom_css .='text-align:right; left:45%; right:15%;';
		$vw_book_store_custom_css .='}';
	}

	/*------------- Slider Content Padding Settings ------------------*/

	$vw_book_store_slider_content_padding_top_bottom = get_theme_mod('vw_book_store_slider_content_padding_top_bottom');
	$vw_book_store_slider_content_padding_left_right = get_theme_mod('vw_book_store_slider_content_padding_left_right');
	if($vw_book_store_slider_content_padding_top_bottom != false || $vw_book_store_slider_content_padding_left_right != false){
		$vw_book_store_custom_css .='#slider .carousel-caption{';
			$vw_book_store_custom_css .='top: '.esc_attr($vw_book_store_slider_content_padding_top_bottom).'; bottom: '.esc_attr($vw_book_store_slider_content_padding_top_bottom).';left: '.esc_attr($vw_book_store_slider_content_padding_left_right).';right: '.esc_attr($vw_book_store_slider_content_padding_left_right).';';
		$vw_book_store_custom_css .='}';
	}

	/*---------------------------Slider Height ------------*/

	$vw_book_store_slider_height = get_theme_mod('vw_book_store_slider_height');
	if($vw_book_store_slider_height != false){
		$vw_book_store_custom_css .='#slider img{';
			$vw_book_store_custom_css .='height: '.esc_attr($vw_book_store_slider_height).';';
		$vw_book_store_custom_css .='}';
	}

	/*---------------------------Blog Layout -------------------*/

	$vw_book_store_theme_lay = get_theme_mod( 'vw_book_store_blog_layout_option','Default');
    if($vw_book_store_theme_lay == 'Default'){
		$vw_book_store_custom_css .='.post-main-box{';
			$vw_book_store_custom_css .='';
		$vw_book_store_custom_css .='}';
	}else if($vw_book_store_theme_lay == 'Center'){
		$vw_book_store_custom_css .='.post-main-box, .post-main-box h2, .post-info, .new-text p, .content-bttn, #our-services .service-text p{';
			$vw_book_store_custom_css .='text-align:center;';
		$vw_book_store_custom_css .='}';
		$vw_book_store_custom_css .='.post-info, .content-bttn{';
			$vw_book_store_custom_css .='margin-top:10px;';
		$vw_book_store_custom_css .='}';
	}else if($vw_book_store_theme_lay == 'Left'){
		$vw_book_store_custom_css .='.post-main-box, .post-main-box h2, .post-info, .new-text p, .content-bttn, #our-services p{';
			$vw_book_store_custom_css .='text-align:Left;';
		$vw_book_store_custom_css .='}';
	}

	/*------------------Responsive Media -----------------------*/

	$vw_book_store_resp_topbar = get_theme_mod( 'vw_book_store_resp_topbar_hide_show',false);
	if($vw_book_store_resp_topbar == true && get_theme_mod( 'vw_book_store_topbar_hide_show', false) == false){
    	$vw_book_store_custom_css .='.top-bar{';
			$vw_book_store_custom_css .='display:none;';
		$vw_book_store_custom_css .='} ';
	}
    if($vw_book_store_resp_topbar == true){
    	$vw_book_store_custom_css .='@media screen and (max-width:575px) {';
		$vw_book_store_custom_css .='.top-bar{';
			$vw_book_store_custom_css .='display:block;';
		$vw_book_store_custom_css .='} }';
	}else if($vw_book_store_resp_topbar == false){
		$vw_book_store_custom_css .='@media screen and (max-width:575px) {';
		$vw_book_store_custom_css .='.top-bar{';
			$vw_book_store_custom_css .='display:none;';
		$vw_book_store_custom_css .='} }';
	}

	$vw_book_store_resp_stickyheader = get_theme_mod( 'vw_book_store_stickyheader_hide_show',false);
	if($vw_book_store_resp_stickyheader == true && get_theme_mod( 'vw_book_store_sticky_header',false) != true){
    	$vw_book_store_custom_css .='.header-fixed{';
			$vw_book_store_custom_css .='position:static;';
		$vw_book_store_custom_css .='} ';
	}
    if($vw_book_store_resp_stickyheader == true){
    	$vw_book_store_custom_css .='@media screen and (max-width:575px) {';
		$vw_book_store_custom_css .='.header-fixed{';
			$vw_book_store_custom_css .='position:fixed;';
		$vw_book_store_custom_css .='} }';
	}else if($vw_book_store_resp_stickyheader == false){
		$vw_book_store_custom_css .='@media screen and (max-width:575px){';
		$vw_book_store_custom_css .='.header-fixed{';
			$vw_book_store_custom_css .='position:static;';
		$vw_book_store_custom_css .='} }';
	}

	$vw_book_store_resp_slider = get_theme_mod( 'vw_book_store_resp_slider_hide_show',false);
	if($vw_book_store_resp_slider == true && get_theme_mod( 'vw_book_store_slider_hide_show', false) == false){
    	$vw_book_store_custom_css .='#slider{';
			$vw_book_store_custom_css .='display:none;';
		$vw_book_store_custom_css .='} ';
	}
    if($vw_book_store_resp_slider == true){
    	$vw_book_store_custom_css .='@media screen and (max-width:575px) {';
		$vw_book_store_custom_css .='#slider{';
			$vw_book_store_custom_css .='display:block;';
		$vw_book_store_custom_css .='} }';
	}else if($vw_book_store_resp_slider == false){
		$vw_book_store_custom_css .='@media screen and (max-width:575px) {';
		$vw_book_store_custom_css .='#slider{';
			$vw_book_store_custom_css .='display:none;';
		$vw_book_store_custom_css .='} }';
	}

	$vw_book_store_sidebar = get_theme_mod( 'vw_book_store_sidebar_hide_show',true);
    if($vw_book_store_sidebar == true){
    	$vw_book_store_custom_css .='@media screen and (max-width:575px) {';
		$vw_book_store_custom_css .='#sidebar{';
			$vw_book_store_custom_css .='display:block;';
		$vw_book_store_custom_css .='} }';
	}else if($vw_book_store_sidebar == false){
		$vw_book_store_custom_css .='@media screen and (max-width:575px) {';
		$vw_book_store_custom_css .='#sidebar{';
			$vw_book_store_custom_css .='display:none;';
		$vw_book_store_custom_css .='} }';
	}

	$vw_book_store_resp_scroll_top = get_theme_mod( 'vw_book_store_resp_scroll_top_hide_show',true);
	if($vw_book_store_resp_scroll_top == true && get_theme_mod( 'vw_book_store_hide_show_scroll',true) != true){
    	$vw_book_store_custom_css .='.scrollup i{';
			$vw_book_store_custom_css .='visibility:hidden !important;';
		$vw_book_store_custom_css .='} ';
	}
    if($vw_book_store_resp_scroll_top == true){
    	$vw_book_store_custom_css .='@media screen and (max-width:575px) {';
		$vw_book_store_custom_css .='.scrollup i{';
			$vw_book_store_custom_css .='visibility:visible !important;';
		$vw_book_store_custom_css .='} }';
	}else if($vw_book_store_resp_scroll_top == false){
		$vw_book_store_custom_css .='@media screen and (max-width:575px){';
		$vw_book_store_custom_css .='.scrollup i{';
			$vw_book_store_custom_css .='visibility:hidden !important;';
		$vw_book_store_custom_css .='} }';
	}

	$vw_book_store_resp_menu_toggle_btn_bg_color = get_theme_mod('vw_book_store_resp_menu_toggle_btn_bg_color');
	if($vw_book_store_resp_menu_toggle_btn_bg_color != false){
		$vw_book_store_custom_css .='.toggle-nav i{';
			$vw_book_store_custom_css .='background-color: '.esc_attr($vw_book_store_resp_menu_toggle_btn_bg_color).';';
		$vw_book_store_custom_css .='}';
	}

	/*------------- Top Bar Settings ------------------*/

	$vw_book_store_topbar_padding_top_bottom = get_theme_mod('vw_book_store_topbar_padding_top_bottom');
	if($vw_book_store_topbar_padding_top_bottom != false){
		$vw_book_store_custom_css .='.top-bar{';
			$vw_book_store_custom_css .='padding-top: '.esc_attr($vw_book_store_topbar_padding_top_bottom).'; padding-bottom: '.esc_attr($vw_book_store_topbar_padding_top_bottom).';';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_navigation_menu_font_size = get_theme_mod('vw_book_store_navigation_menu_font_size');
	if($vw_book_store_navigation_menu_font_size != false){
		$vw_book_store_custom_css .='.main-navigation a{';
			$vw_book_store_custom_css .='font-size: '.esc_attr($vw_book_store_navigation_menu_font_size).';';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_nav_menus_font_weight = get_theme_mod( 'vw_book_store_navigation_menu_font_weight','Default');
    if($vw_book_store_nav_menus_font_weight == 'Default'){
		$vw_book_store_custom_css .='.main-navigation a{';
			$vw_book_store_custom_css .='';
		$vw_book_store_custom_css .='}';
	}else if($vw_book_store_nav_menus_font_weight == 'Normal'){
		$vw_book_store_custom_css .='.main-navigation a{';
			$vw_book_store_custom_css .='font-weight: normal;';
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

	/*-------------- Sticky Header Padding ----------------*/

	$vw_book_store_sticky_header_padding = get_theme_mod('vw_book_store_sticky_header_padding');
	if($vw_book_store_sticky_header_padding != false){
		$vw_book_store_custom_css .='.header-fixed{';
			$vw_book_store_custom_css .='padding: '.esc_attr($vw_book_store_sticky_header_padding).';';
		$vw_book_store_custom_css .='}';
	}

	/*------------------ Search Settings -----------------*/
	
	$vw_book_store_search_font_size = get_theme_mod('vw_book_store_search_font_size');
	if($vw_book_store_search_font_size != false){
		$vw_book_store_custom_css .='.search-box i{';
			$vw_book_store_custom_css .='font-size: '.esc_attr($vw_book_store_search_font_size).';';
		$vw_book_store_custom_css .='}';
	}

	/*---------------- Button Settings ------------------*/

	$vw_book_store_button_padding_top_bottom = get_theme_mod('vw_book_store_button_padding_top_bottom');
	$vw_book_store_button_padding_left_right = get_theme_mod('vw_book_store_button_padding_left_right');
	if($vw_book_store_button_padding_top_bottom != false || $vw_book_store_button_padding_left_right != false){
		$vw_book_store_custom_css .='.post-main-box .blogbutton-small{';
			$vw_book_store_custom_css .='padding-top: '.esc_attr($vw_book_store_button_padding_top_bottom).'; padding-bottom: '.esc_attr($vw_book_store_button_padding_top_bottom).';padding-left: '.esc_attr($vw_book_store_button_padding_left_right).';padding-right: '.esc_attr($vw_book_store_button_padding_left_right).';';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_button_border_radius = get_theme_mod('vw_book_store_button_border_radius');
	if($vw_book_store_button_border_radius != false){
		$vw_book_store_custom_css .='.blogbutton-small{';
			$vw_book_store_custom_css .='border-radius: '.esc_attr($vw_book_store_button_border_radius).'px;';
		$vw_book_store_custom_css .='}';
	}

	/*------------- Single Blog Page------------------*/

	$vw_book_store_featured_image_border_radius = get_theme_mod('vw_book_store_featured_image_border_radius', 0);
	if($vw_book_store_featured_image_border_radius != false){
		$vw_book_store_custom_css .='.box-image img, .feature-box img{';
			$vw_book_store_custom_css .='border-radius: '.esc_attr($vw_book_store_featured_image_border_radius).'px;';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_featured_image_box_shadow = get_theme_mod('vw_book_store_featured_image_box_shadow',0);
	if($vw_book_store_featured_image_box_shadow != false){
		$vw_book_store_custom_css .='.box-image img, .feature-box img, #content-vw img{';
			$vw_book_store_custom_css .='box-shadow: '.esc_attr($vw_book_store_featured_image_box_shadow).'px '.esc_attr($vw_book_store_featured_image_box_shadow).'px '.esc_attr($vw_book_store_featured_image_box_shadow).'px #cccccc;';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_single_blog_post_navigation_show_hide = get_theme_mod('vw_book_store_single_blog_post_navigation_show_hide',true);
	if($vw_book_store_single_blog_post_navigation_show_hide != true){
		$vw_book_store_custom_css .='.post-navigation{';
			$vw_book_store_custom_css .='display: none;';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_single_blog_comment_title = get_theme_mod('vw_book_store_single_blog_comment_title', 'Leave a Reply');
	if($vw_book_store_single_blog_comment_title == ''){
		$vw_book_store_custom_css .='#comments h2#reply-title {';
			$vw_book_store_custom_css .='display: none;';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_single_blog_comment_button_text = get_theme_mod('vw_book_store_single_blog_comment_button_text', 'Post Comment');
	if($vw_book_store_single_blog_comment_button_text == ''){
		$vw_book_store_custom_css .='#comments p.form-submit {';
			$vw_book_store_custom_css .='display: none;';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_comment_width = get_theme_mod('vw_book_store_single_blog_comment_width');
	if($vw_book_store_comment_width != false){
		$vw_book_store_custom_css .='#comments textarea{';
			$vw_book_store_custom_css .='width: '.esc_attr($vw_book_store_comment_width).';';
		$vw_book_store_custom_css .='}';
	}

	/*-------------- Copyright Alignment ----------------*/

	$vw_book_store_footer_background_color = get_theme_mod('vw_book_store_footer_background_color');
	if($vw_book_store_footer_background_color != false){
		$vw_book_store_custom_css .='#footer{';
			$vw_book_store_custom_css .='background-color: '.esc_attr($vw_book_store_footer_background_color).';';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_copyright_font_size = get_theme_mod('vw_book_store_copyright_font_size');
	if($vw_book_store_copyright_font_size != false){
		$vw_book_store_custom_css .='.copyright p{';
			$vw_book_store_custom_css .='font-size: '.esc_attr($vw_book_store_copyright_font_size).';';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_copyright_padding_top_bottom = get_theme_mod('vw_book_store_copyright_padding_top_bottom');
	if($vw_book_store_copyright_padding_top_bottom != false){
		$vw_book_store_custom_css .='#footer-2{';
			$vw_book_store_custom_css .='padding-top: '.esc_attr($vw_book_store_copyright_padding_top_bottom).'; padding-bottom: '.esc_attr($vw_book_store_copyright_padding_top_bottom).';';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_copyright_alignment = get_theme_mod('vw_book_store_copyright_alignment');
	if($vw_book_store_copyright_alignment != false){
		$vw_book_store_custom_css .='.copyright p{';
			$vw_book_store_custom_css .='text-align: '.esc_attr($vw_book_store_copyright_alignment).';';
		$vw_book_store_custom_css .='}';
	}

	/*----------------Sroll to top Settings ------------------*/

	$vw_book_store_scroll_to_top_font_size = get_theme_mod('vw_book_store_scroll_to_top_font_size');
	if($vw_book_store_scroll_to_top_font_size != false){
		$vw_book_store_custom_css .='.scrollup i{';
			$vw_book_store_custom_css .='font-size: '.esc_attr($vw_book_store_scroll_to_top_font_size).';';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_scroll_to_top_padding = get_theme_mod('vw_book_store_scroll_to_top_padding');
	$vw_book_store_scroll_to_top_padding = get_theme_mod('vw_book_store_scroll_to_top_padding');
	if($vw_book_store_scroll_to_top_padding != false){
		$vw_book_store_custom_css .='.scrollup i{';
			$vw_book_store_custom_css .='padding-top: '.esc_attr($vw_book_store_scroll_to_top_padding).';padding-bottom: '.esc_attr($vw_book_store_scroll_to_top_padding).';';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_scroll_to_top_width = get_theme_mod('vw_book_store_scroll_to_top_width');
	if($vw_book_store_scroll_to_top_width != false){
		$vw_book_store_custom_css .='.scrollup i{';
			$vw_book_store_custom_css .='width: '.esc_attr($vw_book_store_scroll_to_top_width).';';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_scroll_to_top_height = get_theme_mod('vw_book_store_scroll_to_top_height');
	if($vw_book_store_scroll_to_top_height != false){
		$vw_book_store_custom_css .='.scrollup i{';
			$vw_book_store_custom_css .='height: '.esc_attr($vw_book_store_scroll_to_top_height).';';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_scroll_to_top_border_radius = get_theme_mod('vw_book_store_scroll_to_top_border_radius');
	if($vw_book_store_scroll_to_top_border_radius != false){
		$vw_book_store_custom_css .='.scrollup i{';
			$vw_book_store_custom_css .='border-radius: '.esc_attr($vw_book_store_scroll_to_top_border_radius).'px;';
		$vw_book_store_custom_css .='}';
	}

	/*----------------Social Icons Settings ------------------*/

	$vw_book_store_social_icon_font_size = get_theme_mod('vw_book_store_social_icon_font_size');
	if($vw_book_store_social_icon_font_size != false){
		$vw_book_store_custom_css .='#sidebar .custom-social-icons i, #footer .custom-social-icons i, .top-bar .custom-social-icons i{';
			$vw_book_store_custom_css .='font-size: '.esc_attr($vw_book_store_social_icon_font_size).';';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_social_icon_padding = get_theme_mod('vw_book_store_social_icon_padding');
	if($vw_book_store_social_icon_padding != false){
		$vw_book_store_custom_css .='#sidebar .custom-social-icons i, #footer .custom-social-icons i{';
			$vw_book_store_custom_css .='padding: '.esc_attr($vw_book_store_social_icon_padding).';';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_social_icon_width = get_theme_mod('vw_book_store_social_icon_width');
	if($vw_book_store_social_icon_width != false){
		$vw_book_store_custom_css .='#sidebar .custom-social-icons i, #footer .custom-social-icons i{';
			$vw_book_store_custom_css .='width: '.esc_attr($vw_book_store_social_icon_width).';';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_social_icon_height = get_theme_mod('vw_book_store_social_icon_height');
	if($vw_book_store_social_icon_height != false){
		$vw_book_store_custom_css .='#sidebar .custom-social-icons i, #footer .custom-social-icons i{';
			$vw_book_store_custom_css .='height: '.esc_attr($vw_book_store_social_icon_height).';';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_social_icon_border_radius = get_theme_mod('vw_book_store_social_icon_border_radius');
	if($vw_book_store_social_icon_border_radius != false){
		$vw_book_store_custom_css .='#sidebar .custom-social-icons i, #footer .custom-social-icons i{';
			$vw_book_store_custom_css .='border-radius: '.esc_attr($vw_book_store_social_icon_border_radius).'px;';
		$vw_book_store_custom_css .='}';
	}

	/*----------------Woocommerce Products Settings ------------------*/

	$vw_book_store_products_padding_top_bottom = get_theme_mod('vw_book_store_products_padding_top_bottom');
	if($vw_book_store_products_padding_top_bottom != false){
		$vw_book_store_custom_css .='.woocommerce ul.products li.product, .woocommerce-page ul.products li.product{';
			$vw_book_store_custom_css .='padding-top: '.esc_attr($vw_book_store_products_padding_top_bottom).'!important; padding-bottom: '.esc_attr($vw_book_store_products_padding_top_bottom).'!important;';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_products_padding_left_right = get_theme_mod('vw_book_store_products_padding_left_right');
	if($vw_book_store_products_padding_left_right != false){
		$vw_book_store_custom_css .='.woocommerce ul.products li.product, .woocommerce-page ul.products li.product{';
			$vw_book_store_custom_css .='padding-left: '.esc_attr($vw_book_store_products_padding_left_right).'!important; padding-right: '.esc_attr($vw_book_store_products_padding_left_right).'!important;';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_products_box_shadow = get_theme_mod('vw_book_store_products_box_shadow');
	if($vw_book_store_products_box_shadow != false){
		$vw_book_store_custom_css .='.woocommerce ul.products li.product, .woocommerce-page ul.products li.product{';
				$vw_book_store_custom_css .='box-shadow: '.esc_attr($vw_book_store_products_box_shadow).'px '.esc_attr($vw_book_store_products_box_shadow).'px '.esc_attr($vw_book_store_products_box_shadow).'px #ddd;';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_products_border_radius = get_theme_mod('vw_book_store_products_border_radius', 0);
	if($vw_book_store_products_border_radius != false){
		$vw_book_store_custom_css .='.woocommerce ul.products li.product, .woocommerce-page ul.products li.product{';
			$vw_book_store_custom_css .='border-radius: '.esc_attr($vw_book_store_products_border_radius).'px;';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_products_btn_padding_top_bottom = get_theme_mod('vw_book_store_products_btn_padding_top_bottom');
	if($vw_book_store_products_btn_padding_top_bottom != false){
		$vw_book_store_custom_css .='.woocommerce a.button{';
			$vw_book_store_custom_css .='padding-top: '.esc_attr($vw_book_store_products_btn_padding_top_bottom).' !important; padding-bottom: '.esc_attr($vw_book_store_products_btn_padding_top_bottom).' !important;';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_products_btn_padding_left_right = get_theme_mod('vw_book_store_products_btn_padding_left_right');
	if($vw_book_store_products_btn_padding_left_right != false){
		$vw_book_store_custom_css .='.woocommerce a.button{';
			$vw_book_store_custom_css .='padding-left: '.esc_attr($vw_book_store_products_btn_padding_left_right).' !important; padding-right: '.esc_attr($vw_book_store_products_btn_padding_left_right).' !important;';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_products_button_border_radius = get_theme_mod('vw_book_store_products_button_border_radius', 0);
	if($vw_book_store_products_button_border_radius != false){
		$vw_book_store_custom_css .='.woocommerce ul.products li.product .button, a.checkout-button.button.alt.wc-forward,.woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button, .woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt{';
			$vw_book_store_custom_css .='border-radius: '.esc_attr($vw_book_store_products_button_border_radius).'px;';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_woocommerce_sale_position = get_theme_mod( 'vw_book_store_woocommerce_sale_position','right');
    if($vw_book_store_woocommerce_sale_position == 'left'){
		$vw_book_store_custom_css .='.woocommerce ul.products li.product .onsale{';
			$vw_book_store_custom_css .='left: -10px; right: auto;';
		$vw_book_store_custom_css .='}';
	}else if($vw_book_store_woocommerce_sale_position == 'right'){
		$vw_book_store_custom_css .='.woocommerce ul.products li.product .onsale{';
			$vw_book_store_custom_css .='left: auto; right: 0;';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_woocommerce_sale_font_size = get_theme_mod('vw_book_store_woocommerce_sale_font_size');
	if($vw_book_store_woocommerce_sale_font_size != false){
		$vw_book_store_custom_css .='.woocommerce span.onsale{';
			$vw_book_store_custom_css .='font-size: '.esc_attr($vw_book_store_woocommerce_sale_font_size).';';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_woocommerce_sale_padding_top_bottom = get_theme_mod('vw_book_store_woocommerce_sale_padding_top_bottom');
	if($vw_book_store_woocommerce_sale_padding_top_bottom != false){
		$vw_book_store_custom_css .='.woocommerce span.onsale{';
			$vw_book_store_custom_css .='padding-top: '.esc_attr($vw_book_store_woocommerce_sale_padding_top_bottom).'!important; padding-bottom: '.esc_attr($vw_book_store_woocommerce_sale_padding_top_bottom).'!important;';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_woocommerce_sale_padding_left_right = get_theme_mod('vw_book_store_woocommerce_sale_padding_left_right');
	if($vw_book_store_woocommerce_sale_padding_left_right != false){
		$vw_book_store_custom_css .='.woocommerce span.onsale{';
			$vw_book_store_custom_css .='padding-left: '.esc_attr($vw_book_store_woocommerce_sale_padding_left_right).'!important; padding-right: '.esc_attr($vw_book_store_woocommerce_sale_padding_left_right).'!important;';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_woocommerce_sale_border_radius = get_theme_mod('vw_book_store_woocommerce_sale_border_radius', 0);
	if($vw_book_store_woocommerce_sale_border_radius != false){
		$vw_book_store_custom_css .='.woocommerce span.onsale{';
			$vw_book_store_custom_css .='border-radius: '.esc_attr($vw_book_store_woocommerce_sale_border_radius).'px !important;';
		$vw_book_store_custom_css .='}';
	}

	/*------------------ Logo  -------------------*/

	$vw_book_store_logo_padding = get_theme_mod('vw_book_store_logo_padding');
	if($vw_book_store_logo_padding != false){
		$vw_book_store_custom_css .='.logo{';
			$vw_book_store_custom_css .='padding: '.esc_attr($vw_book_store_logo_padding).';';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_logo_margin = get_theme_mod('vw_book_store_logo_margin');
	if($vw_book_store_logo_margin != false){
		$vw_book_store_custom_css .='.logo{';
			$vw_book_store_custom_css .='margin: '.esc_attr($vw_book_store_logo_margin).';';
		$vw_book_store_custom_css .='}';
	}

	// Site title Font Size
	$vw_book_store_site_title_font_size = get_theme_mod('vw_book_store_site_title_font_size');
	if($vw_book_store_site_title_font_size != false){
		$vw_book_store_custom_css .='.logo h1 a, .logo p.site-title a{';
			$vw_book_store_custom_css .='font-size: '.esc_attr($vw_book_store_site_title_font_size).';';
		$vw_book_store_custom_css .='}';
	}

	// Site tagline Font Size
	$vw_book_store_site_tagline_font_size = get_theme_mod('vw_book_store_site_tagline_font_size');
	if($vw_book_store_site_tagline_font_size != false){
		$vw_book_store_custom_css .='.logo p.site-description{';
			$vw_book_store_custom_css .='font-size: '.esc_attr($vw_book_store_site_tagline_font_size).';';
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