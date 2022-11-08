<?php
//about theme info
add_action( 'admin_menu', 'ebook_store_gettingstarted' );
function ebook_store_gettingstarted() {    	
	add_theme_page( esc_html__('About Ebook Store', 'ebook-store'), esc_html__('About Ebook Store', 'ebook-store'), 'edit_theme_options', 'ebook_store_guide', 'ebook_store_mostrar_guide');   
}

// Add a Custom CSS file to WP Admin Area
function ebook_store_admin_theme_style() {
   wp_enqueue_style('ebook-store-custom-admin-style', esc_url(get_theme_file_uri()) . '/inc/getstart/getstart.css');
   wp_enqueue_script('ebook-store-tabs', esc_url(get_theme_file_uri()) . '/inc/getstart/js/tab.js');
}
add_action('admin_enqueue_scripts', 'ebook_store_admin_theme_style');

//guidline for about theme
function ebook_store_mostrar_guide() { 
	//custom function about theme customizer
	$return = add_query_arg( array()) ;
	$theme = wp_get_theme( 'ebook-store' );
?>

<div class="wrapper-info">
    <div class="col-left">
    	<h2><?php esc_html_e( 'Welcome to Ebook Store Theme', 'ebook-store' ); ?> <span class="version">Version: <?php echo esc_html($theme['Version']);?></span></h2>
    	<p><?php esc_html_e('All our WordPress themes are modern, minimalist, 100% responsive, seo-friendly,feature-rich, and multipurpose that best suit designers, bloggers and other professionals who are working in the creative fields.','ebook-store'); ?></p>
    </div>
    <div class="col-right">
    	<div class="logo">
			<img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/getstart/images/final-logo.png" alt="" />
		</div>
		<div class="update-now">
			<h4><?php esc_html_e('Buy Ebook Store at 20% Discount','ebook-store'); ?></h4>
			<h4><?php esc_html_e('Use Coupon','ebook-store'); ?> ( <span><?php esc_html_e('vwpro20','ebook-store'); ?></span> ) </h4> 
			<div class="info-link">
				<a href="<?php echo esc_url( EBOOK_STORE_BUY_NOW ); ?>" target="_blank"> <?php esc_html_e( 'Upgrade to Pro', 'ebook-store' ); ?></a>
			</div>
		</div>
    </div>

    <div class="tab-sec">
		<div class="tab">
		  	<button class="tablinks" onclick="ebook_store_open_tab(event, 'lite_theme')"><?php esc_html_e( 'Setup With Customizer', 'ebook-store' ); ?></button>
		  	<button class="tablinks" onclick="ebook_store_open_tab(event, 'block_pattern')"><?php esc_html_e( 'Setup With Block Pattern', 'ebook-store' ); ?></button>
		  	<button class="tablinks" onclick="ebook_store_open_tab(event, 'gutenberg_editor')"><?php esc_html_e( 'Setup With Gutunberg Block', 'ebook-store' ); ?></button>
		  	<button class="tablinks" onclick="ebook_store_open_tab(event, 'product_addons_editor')"><?php esc_html_e( 'Woocommerce Product Addons', 'ebook-store' ); ?></button>
		  		<button class="tablinks" onclick="ebook_store_open_tab(event, 'theme_pro')"><?php esc_html_e( 'Get Premium', 'ebook-store' ); ?></button>
		  	<button class="tablinks" onclick="ebook_store_open_tab(event, 'free_pro')"><?php esc_html_e( 'Support', 'ebook-store' ); ?></button>
		</div>
		
		<?php
			$ebook_store_plugin_custom_css = '';
			if(class_exists('Ibtana_Visual_Editor_Menu_Class')){
				$ebook_store_plugin_custom_css ='display: block';
			}
		?>
		<div id="lite_theme" class="tabcontent open">
			<?php if(!class_exists('Ibtana_Visual_Editor_Menu_Class')){ 
				$plugin_ins = Ebook_Store_Plugin_Activation_Settings::get_instance();
				$ebook_store_actions = $plugin_ins->recommended_actions;
				?>
				<div class="ebook-store-recommended-plugins">
				    <div class="ebook-store-action-list">
				        <?php if ($ebook_store_actions): foreach ($ebook_store_actions as $key => $ebook_store_actionValue): ?>
				                <div class="ebook-store-action" id="<?php echo esc_attr($ebook_store_actionValue['id']);?>">
			                        <div class="action-inner">
			                            <h3 class="action-title"><?php echo esc_html($ebook_store_actionValue['title']); ?></h3>
			                            <div class="action-desc"><?php echo esc_html($ebook_store_actionValue['desc']); ?></div>
			                            <?php echo wp_kses_post($ebook_store_actionValue['link']); ?>
			                            <a class="ibtana-skip-btn" get-start-tab-id="lite-theme-tab" href="javascript:void(0);"><?php esc_html_e('Skip','ebook-store'); ?></a>
			                        </div>
				                </div>
				            <?php endforeach;
				        endif; ?>
				    </div>
				</div>
			<?php } ?>
			<div class="lite-theme-tab" style="<?php echo esc_attr($ebook_store_plugin_custom_css); ?>">
				<h3><?php esc_html_e( 'Lite Theme Information', 'ebook-store' ); ?></h3>
				<hr class="h3hr">
			  	<p><?php esc_html_e('Ebook store WordPress is a theme for book lovers who are fond of book libraries and series. This theme can be used by anyone, Even if the users are writers, authors, or Book reviewers. Ebook store is completely made for Publishing House, Literary Clubs, and bookshop owners. It comes with a clean, modern, and responsive layout, so the users do not have to worry about anything. This Ebook store is compatible with Gutenberg. Gutenberg is perfect for building websites that have much detailed information. The home page has a full-width image and social icons. The sidebar has all your data (name, contact details), with links to your Word format and other social profiles. The Ebook store is an excellent choice for those who want to create an online store. Its professional design and customization options make it a great option for building a unique and fully-functional website. Additionally, WooCommerce support has been set up and will manage your store. So users can create an online store quickly and easily by setting up products using drag and drop functionality, the social icons widget, which allows you to add social icons to your sidebar or footer column, and Custom Google Fonts allows users to choose their favorite font from the Google fonts store instead of the default system fonts','ebook-store'); ?></p>
			  	<div class="col-left-inner">
			  		<h4><?php esc_html_e( 'Theme Documentation', 'ebook-store' ); ?></h4>
					<p><?php esc_html_e( 'If you need any assistance regarding setting up and configuring the Theme, our documentation is there.', 'ebook-store' ); ?></p>
					<div class="info-link">
						<a href="<?php echo esc_url( EBOOK_STORE_FREE_THEME_DOC ); ?>" target="_blank"> <?php esc_html_e( 'Documentation', 'ebook-store' ); ?></a>
					</div>
					<hr>
					<h4><?php esc_html_e('Theme Customizer', 'ebook-store'); ?></h4>
					<p> <?php esc_html_e('To begin customizing your website, start by clicking "Customize".', 'ebook-store'); ?></p>
					<div class="info-link">
						<a target="_blank" href="<?php echo esc_url( admin_url('customize.php') ); ?>"><?php esc_html_e('Customizing', 'ebook-store'); ?></a>
					</div>
					<hr>				
					<h4><?php esc_html_e('Having Trouble, Need Support?', 'ebook-store'); ?></h4>
					<p> <?php esc_html_e('Our dedicated team is well prepared to help you out in case of queries and doubts regarding our theme.', 'ebook-store'); ?></p>
					<div class="info-link">
						<a href="<?php echo esc_url( EBOOK_STORE_SUPPORT ); ?>" target="_blank"><?php esc_html_e('Support Forum', 'ebook-store'); ?></a>
					</div>
					<hr>
					<h4><?php esc_html_e('Reviews & Testimonials', 'ebook-store'); ?></h4>
					<p> <?php esc_html_e('All the features and aspects of this WordPress Theme are phenomenal. I\'d recommend this theme to all.', 'ebook-store'); ?>  </p>
					<div class="info-link">
						<a href="<?php echo esc_url( EBOOK_STORE_REVIEW ); ?>" target="_blank"><?php esc_html_e('Reviews', 'ebook-store'); ?></a>
					</div>
			  		<div class="link-customizer">
						<h3><?php esc_html_e( 'Link to customizer', 'ebook-store' ); ?></h3>
						<hr class="h3hr">
						<div class="first-row">
							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-buddicons-buddypress-logo"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[control]=custom_logo') ); ?>" target="_blank"><?php esc_html_e('Upload your logo','ebook-store'); ?></a>
								</div>
								<div class="row-box2">
									<span class="dashicons dashicons-slides"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_book_store_slidersettings') ); ?>" target="_blank"><?php esc_html_e('Slider Settings','ebook-store'); ?></a>
								</div>
							</div>
							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-welcome-write-blog"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_book_store_topbar') ); ?>" target="_blank"><?php esc_html_e('Topbar Settings','ebook-store'); ?></a>
								</div>
								<div class="row-box2">
									<span class="dashicons dashicons-editor-table"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=ebook_store_banner_section') ); ?>" target="_blank"><?php esc_html_e('Banner Section','ebook-store'); ?></a>
								</div>
							</div>
							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-menu"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[panel]=nav_menus') ); ?>" target="_blank"><?php esc_html_e('Menus','ebook-store'); ?></a>
								</div>
								<div class="row-box2">
									<span class="dashicons dashicons-screenoptions"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[panel]=widgets') ); ?>" target="_blank"><?php esc_html_e('Footer Widget','ebook-store'); ?></a>
								</div>
							</div>

							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-format-gallery"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_book_store_post_settings') ); ?>" target="_blank"><?php esc_html_e('Post settings','ebook-store'); ?></a>
								</div>
								 <div class="row-box2">
									<span class="dashicons dashicons-align-center"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_book_store_woocommerce_section') ); ?>" target="_blank"><?php esc_html_e('WooCommerce Layout','ebook-store'); ?></a>
								</div> 
							</div>
							
							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-admin-generic"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_book_store_left_right') ); ?>" target="_blank"><?php esc_html_e('General Settings','ebook-store'); ?></a>
								</div>
								<div class="row-box2">
									<span class="dashicons dashicons-text-page"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_book_store_footer') ); ?>" target="_blank"><?php esc_html_e('Footer Text','ebook-store'); ?></a>
								</div>
							</div>
						</div>
					</div>
			  	</div>
				<div class="col-right-inner">
					<h3 class="page-template"><?php esc_html_e('How to set up Home Page Template','ebook-store'); ?></h3>
				  	<hr class="h3hr">
					<p><?php esc_html_e('Follow these instructions to setup Home page.','ebook-store'); ?></p>
               <ul>
                  <p><span class="strong"><?php esc_html_e('1. Create a new page :','ebook-store'); ?></span><?php esc_html_e(' Go to ','ebook-store'); ?>
				  		<b><?php esc_html_e(' Dashboard >> Pages >> Add New Page','ebook-store'); ?></b></p>
                  <p><?php esc_html_e('Name it as "Home" then select the template "Custom Home Page".','ebook-store'); ?></p>
                  <img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/getstart/images/home-page-template.png" alt="" />
                  <p><span class="strong"><?php esc_html_e('2. Set the front page:','ebook-store'); ?></span><?php esc_html_e(' Go to ','ebook-store'); ?>
				  		<b><?php esc_html_e(' Settings >> Reading ','ebook-store'); ?></b></p>
				  		<p><?php esc_html_e('Select the option of Static Page, now select the page you created to be the homepage, while another page to be your default page.','ebook-store'); ?></p>
                  <img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/getstart/images/set-front-page.png" alt="" />
                  <p><?php esc_html_e(' Once you are done with this, then follow the','ebook-store'); ?> <a class="doc-links" href="https://www.vwthemesdemo.com/docs/free-ebook-store/" target="_blank"><?php esc_html_e('Documentation','ebook-store'); ?></a></p>
               </ul>
			  	</div>
			</div>
		</div>	

		<div id="block_pattern" class="tabcontent">
			<?php if(!class_exists('Ibtana_Visual_Editor_Menu_Class')){ 
				$plugin_ins = Ebook_Store_Plugin_Activation_Settings::get_instance();
				$ebook_store_actions = $plugin_ins->recommended_actions;
				?>
				<div class="ebook-store-recommended-plugins">
				    <div class="ebook-store-action-list">
				        <?php if ($ebook_store_actions): foreach ($ebook_store_actions as $key => $ebook_store_actionValue): ?>
				                <div class="ebook-store-action" id="<?php echo esc_attr($ebook_store_actionValue['id']);?>">
			                        <div class="action-inner">
			                            <h3 class="action-title"><?php echo esc_html($ebook_store_actionValue['title']); ?></h3>
			                            <div class="action-desc"><?php echo esc_html($ebook_store_actionValue['desc']); ?></div>
			                            <?php echo wp_kses_post($ebook_store_actionValue['link']); ?>
			                            <a class="ibtana-skip-btn" href="javascript:void(0);" get-start-tab-id="gutenberg-editor-tab"><?php esc_html_e('Skip','ebook-store'); ?></a>
			                        </div>
				                </div>
				            <?php endforeach;
				        endif; ?>
				    </div>
				</div>
			<?php } ?>
			<div class="gutenberg-editor-tab" style="<?php echo esc_attr($ebook_store_plugin_custom_css); ?>">
				<div class="block-pattern-img">
				  	<h3><?php esc_html_e( 'Block Patterns', 'ebook-store' ); ?></h3>
					<hr class="h3hr">
					<p><?php esc_html_e('Follow the below instructions to setup Home page with Block Patterns.','ebook-store'); ?></p>
	              	<p><b><?php esc_html_e('Click on Below Add new page button >> Click on "+" Icon >> Click Pattern Tab >> Click on homepage sections >> Publish.','ebook-store'); ?></span></b></p>
	              	<div class="ebook-store-pattern-page">
				    	<a href="javascript:void(0)" class="vw-pattern-page-btn button-primary button"><?php esc_html_e('Add New Page','ebook-store'); ?></a>
				    </div>
	              	<img src="<?php echo esc_url(get_theme_file_uri()); ?>/inc/getstart/images/block-pattern.png" alt="" />	
	            </div>

	            <div class="block-pattern-link-customizer">
		            <div class="link-customizer-with-block-pattern">
							<h3><?php esc_html_e( 'Link to customizer', 'ebook-store' ); ?></h3>
							<hr class="h3hr">
							<div class="first-row">
								<div class="row-box">
									<div class="row-box1">
										<span class="dashicons dashicons-buddicons-buddypress-logo"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[control]=custom_logo') ); ?>" target="_blank"><?php esc_html_e('Upload your logo','ebook-store'); ?></a>
									</div>
									<div class="row-box2">
										<span class="dashicons dashicons-slides"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_book_store_slidersettings') ); ?>" target="_blank"><?php esc_html_e('Slider Settings','ebook-store'); ?></a>
									</div>
								</div>
								<div class="row-box">
									<div class="row-box1">
										<span class="dashicons dashicons-menu"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[panel]=nav_menus') ); ?>" target="_blank"><?php esc_html_e('Menus','ebook-store'); ?></a>
									</div>
									
									<div class="row-box2">
										<span class="dashicons dashicons-text-page"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_book_store_footer') ); ?>" target="_blank"><?php esc_html_e('Footer Text','ebook-store'); ?></a>
									</div>
								</div>

								<div class="row-box">
									<div class="row-box1">
										<span class="dashicons dashicons-format-gallery"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_book_store_post_settings') ); ?>" target="_blank"><?php esc_html_e('Post settings','ebook-store'); ?></a>
									</div>
									 <div class="row-box2">
										<span class="dashicons dashicons-align-center"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_book_store_woocommerce_section') ); ?>" target="_blank"><?php esc_html_e('WooCommerce Layout','ebook-store'); ?></a>
									</div> 
								</div>
								
								<div class="row-box">
									<div class="row-box1">
										<span class="dashicons dashicons-admin-generic"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_book_store_left_right') ); ?>" target="_blank"><?php esc_html_e('General Settings','ebook-store'); ?></a>
									</div>
									 <div class="row-box2">
										<span class="dashicons dashicons-screenoptions"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[panel]=widgets') ); ?>" target="_blank"><?php esc_html_e('Footer Widget','ebook-store'); ?></a>
									</div> 
								</div>
							</div>
						</div>
					</div>		
	        </div>
		</div>

		<div id="gutenberg_editor" class="tabcontent">
			<?php if(!class_exists('Ibtana_Visual_Editor_Menu_Class')){ 
			$plugin_ins = Ebook_Store_Plugin_Activation_Settings::get_instance();
			$ebook_store_actions = $plugin_ins->recommended_actions;
			?>
				<div class="ebook-store-recommended-plugins">
				    <div class="ebook-store-action-list">
				        <?php if ($ebook_store_actions): foreach ($ebook_store_actions as $key => $ebook_store_actionValue): ?>
				                <div class="ebook-store-action" id="<?php echo esc_attr($ebook_store_actionValue['id']);?>">
			                        <div class="action-inner plugin-activation-redirect">
			                            <h3 class="action-title"><?php echo esc_html($ebook_store_actionValue['title']); ?></h3>
			                            <div class="action-desc"><?php echo esc_html($ebook_store_actionValue['desc']); ?></div>
			                            <?php echo wp_kses_post($ebook_store_actionValue['link']); ?>
			                        </div>
				                </div>
				            <?php endforeach;
				        endif; ?>
				    </div>
				</div>
			<?php }else{ ?>
				<h3><?php esc_html_e( 'Gutunberg Blocks', 'ebook-store' ); ?></h3>
				<hr class="h3hr">
				<div class="ebook-store-pattern-page">
		    		<a href="<?php echo esc_url( admin_url( 'admin.php?page=ibtana-visual-editor-templates' ) ); ?>" class="vw-pattern-page-btn ibtana-dashboard-page-btn button-primary button"><?php esc_html_e('Ibtana Settings','ebook-store'); ?></a>
			   </div>
			   <div class="link-customizer-with-guternberg-ibtana">
					<h3><?php esc_html_e( 'Link to customizer', 'ebook-store' ); ?></h3>
					<hr class="h3hr">
					<div class="first-row">
						<div class="row-box">
							<div class="row-box1">
								<span class="dashicons dashicons-buddicons-buddypress-logo"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[control]=custom_logo') ); ?>" target="_blank"><?php esc_html_e('Upload your logo','ebook-store'); ?></a>
							</div>
							<div class="row-box2">
								<span class="dashicons dashicons-slides"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_book_store_slidersettings') ); ?>" target="_blank"><?php esc_html_e('Slider Settings','ebook-store'); ?></a>
							</div>
						</div>
						<div class="row-box">
							<div class="row-box1">
								<span class="dashicons dashicons-menu"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[panel]=nav_menus') ); ?>" target="_blank"><?php esc_html_e('Menus','ebook-store'); ?></a>
							</div>
							
							<div class="row-box2">
								<span class="dashicons dashicons-text-page"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_book_store_footer_section') ); ?>" target="_blank"><?php esc_html_e('Footer Text','ebook-store'); ?></a>
							</div>
						</div>

						<div class="row-box">
							<div class="row-box1">
								<span class="dashicons dashicons-format-gallery"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_book_store_post_settings') ); ?>" target="_blank"><?php esc_html_e('Post settings','ebook-store'); ?></a>
							</div>
							 <div class="row-box2">
								<span class="dashicons dashicons-align-center"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_book_store_woocommerce_section') ); ?>" target="_blank"><?php esc_html_e('WooCommerce Layout','ebook-store'); ?></a>
							</div> 
						</div>
						
						<div class="row-box">
							<div class="row-box1">
								<span class="dashicons dashicons-admin-generic"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_book_store_left_right') ); ?>" target="_blank"><?php esc_html_e('General Settings','ebook-store'); ?></a>
							</div>
							 <div class="row-box2">
								<span class="dashicons dashicons-screenoptions"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[panel]=widgets') ); ?>" target="_blank"><?php esc_html_e('Footer Widget','ebook-store'); ?></a>
							</div> 
						</div>
					</div>
				</div>
			<?php } ?>
		</div>

		<div id="product_addons_editor" class="tabcontent">
			<?php if(!class_exists('IEPA_Loader')){
				$plugin_ins = Ebook_Store_Plugin_Activation_Woo_Products::get_instance();
				$ebook_store_actions = $plugin_ins->recommended_actions;
				?>
				<div class="ebook-store-recommended-plugins">
					<div class="ebook-store-action-list">
					  	<?php if ($ebook_store_actions): foreach ($ebook_store_actions as $key => $ebook_store_actionValue): ?>
				         <div class="ebook-store-action" id="<?php echo esc_attr($ebook_store_actionValue['id']);?>">
			               <div class="action-inner plugin-activation-redirect">
									<h3 class="action-title"><?php echo esc_html($ebook_store_actionValue['title']); ?></h3>
			                  <div class="action-desc"><?php echo esc_html($ebook_store_actionValue['desc']); ?></div>
				               <?php echo wp_kses_post($ebook_store_actionValue['link']); ?>
				            </div>
				         </div>
					      <?php endforeach;
					  	endif; ?>
					</div>
				</div>
			<?php }else{ ?>
				<h3><?php esc_html_e( 'Woocommerce Products Blocks', 'ebook-store' ); ?></h3>
				<hr class="h3hr">
				<div class="ebook-store-pattern-page">
					<p><?php esc_html_e('Follow the below instructions to setup Products Templates.','ebook-store'); ?></p>
					<p><b><?php esc_html_e('1. First you need to activate these plugins','ebook-store'); ?></b></p>
					<p><?php esc_html_e('1. Ibtana - WordPress Website Builder ','ebook-store'); ?></p>
					<p><?php esc_html_e('2. Ibtana - Ecommerce Product Addons.','ebook-store'); ?></p>
					<p><?php esc_html_e('3. Woocommerce','ebook-store'); ?></p>
					<p><b><?php esc_html_e('2. Go To Dashboard >> Ibtana Settings >> Woocommerce Templates','ebook-store'); ?></span></b></p>
	            <div class="ebook-store-pattern-page-btn">
			    		<a href="<?php echo esc_url( admin_url( 'admin.php?page=ibtana-visual-editor-woocommerce-templates&ive_wizard_view=parent' ) ); ?>" class="vw-pattern-page-btn ibtana-dashboard-page-btn button-primary button"><?php esc_html_e('Woocommerce Templates','ebook-store'); ?></a>
			    	</div>
	              	<p><?php esc_html_e('You can create a template as you like.','ebook-store'); ?></span></p>
			    </div>
			<?php } ?>
		</div>

		<div id="theme_pro" class="tabcontent">
		  	<h3><?php esc_html_e( 'Premium Theme Information', 'ebook-store' ); ?></h3>
			<hr class="h3hr">
		    <div class="col-left-pro">
		    	<p><?php esc_html_e('This bookstore WordPress theme is clean, reliable, modern and feature-full with great use for book stores, eBook portals, online book sellers, authors, journalists, writers, editors, publishers, libraries, reading clubs, online music, movies and game selling website and all the literature lovers. It is a one-stop-solution for establishing a performance efficient website for all books and reading related businesses. It has all the facility to list your books whether you deal with kids’ sound and story books or literature books for adults. It has sections cascaded to form a continuity to never leave the visitors idle. This bookstore WordPress theme has a fluid layout making it fully responsive. It is cross-browser compatible and translation ready. It is coded from scratch to make it bug-free. It is fearlessly compatible with third party plugins to give an extra edge to your website with the added plugin.','ebook-store'); ?></p>
		    	<div class="pro-links">
			    	<a href="<?php echo esc_url( EBOOK_STORE_LIVE_DEMO ); ?>" target="_blank"><?php esc_html_e('Live Demo', 'ebook-store'); ?></a>
					<a href="<?php echo esc_url( EBOOK_STORE_BUY_NOW ); ?>" target="_blank"><?php esc_html_e('Buy Pro', 'ebook-store'); ?></a>
					<a href="<?php echo esc_url( EBOOK_STORE_PRO_DOC ); ?>" target="_blank"><?php esc_html_e('Pro Documentation', 'ebook-store'); ?></a>
				</div>
		    </div>
		    <div class="col-right-pro">
		    	<img src="<?php echo esc_url(get_theme_file_uri()); ?>/inc/getstart/images/responsive.png" alt="" />
		    </div>
		    <div class="featurebox">
			    <h3><?php esc_html_e( 'Theme Features', 'ebook-store' ); ?></h3>
				<hr class="h3hr">
				<div class="table-image">
					<table class="tablebox">
						<thead>
							<tr>
								<th></th>
								<th><?php esc_html_e('Free Themes', 'ebook-store'); ?></th>
								<th><?php esc_html_e('Premium Themes', 'ebook-store'); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><?php esc_html_e('Theme Customization', 'ebook-store'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Responsive Design', 'ebook-store'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Logo Upload', 'ebook-store'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Social Media Links', 'ebook-store'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Slider Settings', 'ebook-store'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Number of Slides', 'ebook-store'); ?></td>
								<td class="table-img"><?php esc_html_e('4', 'ebook-store'); ?></td>
								<td class="table-img"><?php esc_html_e('Unlimited', 'ebook-store'); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Template Pages', 'ebook-store'); ?></td>
								<td class="table-img"><?php esc_html_e('3', 'ebook-store'); ?></td>
								<td class="table-img"><?php esc_html_e('6', 'ebook-store'); ?></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Home Page Template', 'ebook-store'); ?></td>
								<td class="table-img"><?php esc_html_e('1', 'ebook-store'); ?></td>
								<td class="table-img"><?php esc_html_e('1', 'ebook-store'); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Theme sections', 'ebook-store'); ?></td>
								<td class="table-img"><?php esc_html_e('2', 'ebook-store'); ?></td>
								<td class="table-img"><?php esc_html_e('14', 'ebook-store'); ?></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Contact us Page Template', 'ebook-store'); ?></td>
								<td class="table-img">0</td>
								<td class="table-img"><?php esc_html_e('1', 'ebook-store'); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Blog Templates & Layout', 'ebook-store'); ?></td>
								<td class="table-img">0</td>
								<td class="table-img"><?php esc_html_e('3(Full width/Left/Right Sidebar)', 'ebook-store'); ?></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Page Templates & Layout', 'ebook-store'); ?></td>
								<td class="table-img">0</td>
								<td class="table-img"><?php esc_html_e('2(Left/Right Sidebar)', 'ebook-store'); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Color Pallete For Particular Sections', 'ebook-store'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Global Color Option', 'ebook-store'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Section Reordering', 'ebook-store'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Demo Importer', 'ebook-store'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Allow To Set Site Title, Tagline, Logo', 'ebook-store'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Enable Disable Options On All Sections, Logo', 'ebook-store'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Full Documentation', 'ebook-store'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Latest WordPress Compatibility', 'ebook-store'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Woo-Commerce Compatibility', 'ebook-store'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Support 3rd Party Plugins', 'ebook-store'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Secure and Optimized Code', 'ebook-store'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Exclusive Functionalities', 'ebook-store'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Section Enable / Disable', 'ebook-store'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Section Google Font Choices', 'ebook-store'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Gallery', 'ebook-store'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Simple & Mega Menu Option', 'ebook-store'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Support to add custom CSS / JS ', 'ebook-store'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Shortcodes', 'ebook-store'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Custom Background, Colors, Header, Logo & Menu', 'ebook-store'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Premium Membership', 'ebook-store'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Budget Friendly Value', 'ebook-store'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Priority Error Fixing', 'ebook-store'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Custom Feature Addition', 'ebook-store'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('All Access Theme Pass', 'ebook-store'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Seamless Customer Support', 'ebook-store'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr>
								<td></td>
								<td class="table-img"></td>
								<td class="update-link"><a href="<?php echo esc_url( EBOOK_STORE_BUY_NOW ); ?>" target="_blank"><?php esc_html_e('Upgrade to Pro', 'ebook-store'); ?></a></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="free_pro" class="tabcontent">
		  	<div class="col-3">
		  		<h4><span class="dashicons dashicons-star-filled"></span><?php esc_html_e('Pro Version', 'ebook-store'); ?></h4>
				<p> <?php esc_html_e('To gain access to extra theme options and more interesting features, upgrade to pro version.', 'ebook-store'); ?></p>
				<div class="info-link">
					<a href="<?php echo esc_url( EBOOK_STORE_BUY_NOW ); ?>" target="_blank"><?php esc_html_e('Get Pro', 'ebook-store'); ?></a>
				</div>
		  	</div>
		  	<div class="col-3">
		  		<h4><span class="dashicons dashicons-cart"></span><?php esc_html_e('Pre-purchase Queries', 'ebook-store'); ?></h4>
				<p> <?php esc_html_e('If you have any pre-sale query, we are prepared to resolve it.', 'ebook-store'); ?></p>
				<div class="info-link">
					<a href="<?php echo esc_url( EBOOK_STORE_CONTACT ); ?>" target="_blank"><?php esc_html_e('Question', 'ebook-store'); ?></a>
				</div>
		  	</div>
		  	<div class="col-3">		  		
		  		<h4><span class="dashicons dashicons-admin-customizer"></span><?php esc_html_e('Child Theme', 'ebook-store'); ?></h4>
				<p> <?php esc_html_e('For theme file customizations, make modifications in the child theme and not in the main theme file.', 'ebook-store'); ?></p>
				<div class="info-link">
					<a href="<?php echo esc_url( EBOOK_STORE_CHILD_THEME ); ?>" target="_blank"><?php esc_html_e('About Child Theme', 'ebook-store'); ?></a>
				</div>
		  	</div>

		  	<div class="col-3">
		  		<h4><span class="dashicons dashicons-admin-comments"></span><?php esc_html_e('Frequently Asked Questions', 'ebook-store'); ?></h4>
				<p> <?php esc_html_e('We have gathered top most, frequently asked questions and answered them for your easy understanding. We will list down more as we get new challenging queries. Check back often.', 'ebook-store'); ?></p>
				<div class="info-link">
					<a href="<?php echo esc_url( EBOOK_STORE_FAQ ); ?>" target="_blank"><?php esc_html_e('View FAQ','ebook-store'); ?></a>
				</div>
		  	</div>

		  	<div class="col-3">
		  		<h4><span class="dashicons dashicons-sos"></span><?php esc_html_e('Support Queries', 'ebook-store'); ?></h4>
				<p> <?php esc_html_e('If you have any queries after purchase, you can contact us. We are eveready to help you out.', 'ebook-store'); ?></p>
				<div class="info-link">
					<a href="<?php echo esc_url( EBOOK_STORE_SUPPORT ); ?>" target="_blank"><?php esc_html_e('Contact Us', 'ebook-store'); ?></a>
				</div>
		  	</div>
		</div>
	</div>
</div>
<?php } ?>