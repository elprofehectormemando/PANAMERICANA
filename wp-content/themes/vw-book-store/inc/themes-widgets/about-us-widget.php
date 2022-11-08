<?php
/**
 * Custom About us Widget
 */

class VW_Book_Store_About_Widget extends WP_Widget {
	function __construct() {
		parent::__construct(
			'VW_Book_Store_About_Widget',
			__('VW About us', 'vw-book-store'),
			array( 'description' => __( 'Widget for about us section in sidebar', 'vw-book-store' ), ) 
		);
	}
	
	public function widget( $args, $instance ) {
		?>
		<aside class="widget">
			<?php
			$title = isset( $instance['title'] ) ? $instance['title'] : '';
			$author = isset( $instance['author'] ) ? $instance['author'] : '';
			$designation = isset( $instance['designation'] ) ? $instance['designation'] : '';
			$description = isset( $instance['description'] ) ? $instance['description'] : '';
			$read_more_url = isset( $instance['read_more_url'] ) ? $instance['read_more_url'] : '';
			$read_more_text = isset( $instance['read_more_text'] ) ? $instance['read_more_text'] : '';
			$upload_image = isset( $instance['upload_image'] ) ? $instance['upload_image'] : '';

	        echo '<div class="custom-about-us">';
	        if(!empty($title) ){ ?><h3 class="custom_title"><?php echo esc_html($title); ?></h3><?php } ?>
		        <?php if($upload_image): ?>
	      			<img src="<?php echo esc_url($upload_image); ?>" alt="">
				<?php endif; ?>
				<?php if(!empty($author) ){ ?><p class="custom_author"><?php echo esc_html($author); ?></p><?php } ?>
				<?php if(!empty($designation) ){ ?><p class="custom_designation"><?php echo esc_html($designation); ?></p><?php } ?>
		        <?php if(!empty($description) ){ ?><p class="custom_desc"><?php echo esc_html($description); ?></p><?php } ?>
		        <?php if(!empty($read_more_url) ){ ?><div class="more-button"><a class="custom_read_more" href="<?php echo esc_url($read_more_url); ?>"><?php if(!empty($read_more_text) ){ ?><?php echo esc_html($read_more_text); ?><?php } ?></a></div><?php } ?>
	        <?php echo '</div>';
			?>
		</aside>
		<?php
	}
	
	// Widget Backend 
	public function form( $instance ) {	

		$title= ''; $author = ''; $designation = ''; $description= ''; $read_more_text = ''; $read_more_url = ''; $upload_image = '';

		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		$author = isset( $instance['author'] ) ? $instance['author'] : '';
		$designation = isset( $instance['designation'] ) ? $instance['designation'] : '';
		$description = isset( $instance['description'] ) ? $instance['description'] : '';
		$read_more_url = isset( $instance['read_more_url'] ) ? $instance['read_more_url'] : '';
		$read_more_text = isset( $instance['read_more_text'] ) ? $instance['read_more_text'] : '';
		$upload_image = isset( $instance['upload_image'] ) ? $instance['upload_image'] : '';
	?>
		<p>
        <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:','vw-book-store'); ?></label>
        <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
    	</p>
    	<p>
        <label for="<?php echo esc_attr($this->get_field_id('author')); ?>"><?php esc_html_e('Author Name:','vw-book-store'); ?></label>
        <input class="widefat" id="<?php echo esc_attr($this->get_field_id('author')); ?>" name="<?php echo esc_attr($this->get_field_name('author')); ?>" type="text" value="<?php echo esc_attr($author); ?>">
    	</p>
    	<p>
        <label for="<?php echo esc_attr($this->get_field_id('designation')); ?>"><?php esc_html_e('Designation:','vw-book-store'); ?></label>
        <input class="widefat" id="<?php echo esc_attr($this->get_field_id('designation')); ?>" name="<?php echo esc_attr($this->get_field_name('designation')); ?>" type="text" value="<?php echo esc_attr($designation); ?>">
    	</p>
    	<p>
        <label for="<?php echo esc_attr($this->get_field_id('description')); ?>"><?php esc_html_e('Description:','vw-book-store'); ?></label>
        <input class="widefat" id="<?php echo esc_attr($this->get_field_id('description')); ?>" name="<?php echo esc_attr($this->get_field_name('description')); ?>" type="text" value="<?php echo esc_attr($description); ?>">
    	</p>
    	<p>
		<label for="<?php echo esc_attr($this->get_field_id('read_more_text')); ?>"><?php esc_html_e('Button Text:','vw-book-store'); ?></label>
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id('read_more_text')); ?>" name="<?php echo esc_attr($this->get_field_name('read_more_text')); ?>" type="text" value="<?php echo esc_attr($read_more_text); ?>">
		</p>
		<p>
		<label for="<?php echo esc_attr($this->get_field_id('read_more_url')); ?>"><?php esc_html_e('Button Url:','vw-book-store'); ?></label>
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id('read_more_url')); ?>" name="<?php echo esc_attr($this->get_field_name('read_more_url')); ?>" type="text" value="<?php echo esc_attr($read_more_url); ?>">
		</p>
		<p>
		<label for="<?php echo esc_attr($this->get_field_id( 'upload_image' )); ?>"><?php esc_html_e( 'Image Url:','vw-book-store'); ?></label>
		<?php
			if ( $upload_image != '' ) :
			echo '<img class="custom_media_image" src="' . esc_url($upload_image) . '" style="margin:10px 0;padding:0;max-width:100%;float:left;display:inline-block" /><br />';
			endif;
		?>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'upload_image' ) ); ?>" name="<?php echo esc_attr($this->get_field_name( 'upload_image' )); ?>" type="text" value="<?php echo esc_url( $upload_image ); ?>" />
	   	</p>
		<?php 
	}
	
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();	
		$instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
		$instance['author'] = ( ! empty( $new_instance['author'] ) ) ? strip_tags($new_instance['author']) : '';
		$instance['designation'] = ( ! empty( $new_instance['designation'] ) ) ? strip_tags($new_instance['designation']) : '';
		$instance['description'] = (!empty($new_instance['description']) ) ? strip_tags($new_instance['description']) : '';
        $instance['read_more_text'] = (!empty($new_instance['read_more_text']) ) ? strip_tags($new_instance['read_more_text']) : '';
        $instance['read_more_url'] = (!empty($new_instance['read_more_url']) ) ? esc_url_raw($new_instance['read_more_url']) : '';
        $instance['upload_image'] = ( ! empty( $new_instance['upload_image'] ) ) ? strip_tags($new_instance['upload_image']) : '';

		return $instance;
	}
}
// Register and load the widget
function vw_book_store_about_custom_load_widget() {
	register_widget( 'VW_Book_Store_About_Widget' );
}
add_action( 'widgets_init', 'vw_book_store_about_custom_load_widget' );