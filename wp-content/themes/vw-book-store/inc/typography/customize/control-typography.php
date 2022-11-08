<?php
/**
 * Typography control class.
 *
 * @since  1.0.0
 * @access public
 */

class VW_Book_Store_Control_Typography extends WP_Customize_Control {

	/**
	 * The type of customize control being rendered.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $type = 'typography';

	/**
	 * Array 
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $l10n = array();

	/**
	 * Set up our control.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  object  $manager
	 * @param  string  $id
	 * @param  array   $args
	 * @return void
	 */
	public function __construct( $manager, $id, $args = array() ) {

		// Let the parent class do its thing.
		parent::__construct( $manager, $id, $args );

		// Make sure we have labels.
		$this->l10n = wp_parse_args(
			$this->l10n,
			array(
				'color'       => esc_html__( 'Font Color', 'vw-book-store' ),
				'family'      => esc_html__( 'Font Family', 'vw-book-store' ),
				'size'        => esc_html__( 'Font Size',   'vw-book-store' ),
				'weight'      => esc_html__( 'Font Weight', 'vw-book-store' ),
				'style'       => esc_html__( 'Font Style',  'vw-book-store' ),
				'line_height' => esc_html__( 'Line Height', 'vw-book-store' ),
				'letter_spacing' => esc_html__( 'Letter Spacing', 'vw-book-store' ),
			)
		);
	}

	/**
	 * Enqueue scripts/styles.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue() {
		wp_enqueue_script( 'vw-book-store-ctypo-customize-controls' );
		wp_enqueue_style(  'vw-book-store-ctypo-customize-controls' );
	}

	/**
	 * Add custom parameters to pass to the JS via JSON.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function to_json() {
		parent::to_json();

		// Loop through each of the settings and set up the data for it.
		foreach ( $this->settings as $setting_key => $setting_id ) {

			$this->json[ $setting_key ] = array(
				'link'  => $this->get_link( $setting_key ),
				'value' => $this->value( $setting_key ),
				'label' => isset( $this->l10n[ $setting_key ] ) ? $this->l10n[ $setting_key ] : ''
			);

			if ( 'family' === $setting_key )
				$this->json[ $setting_key ]['choices'] = $this->get_font_families();

			elseif ( 'weight' === $setting_key )
				$this->json[ $setting_key ]['choices'] = $this->get_font_weight_choices();

			elseif ( 'style' === $setting_key )
				$this->json[ $setting_key ]['choices'] = $this->get_font_style_choices();
		}
	}

	/**
	 * Underscore JS template to handle the control's output.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function content_template() { ?>

		<# if ( data.label ) { #>
			<span class="customize-control-title">{{ data.label }}</span>
		<# } #>

		<# if ( data.description ) { #>
			<span class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>

		<ul>

		<# if ( data.family && data.family.choices ) { #>

			<li class="typography-font-family">

				<# if ( data.family.label ) { #>
					<span class="customize-control-title">{{ data.family.label }}</span>
				<# } #>

				<select {{{ data.family.link }}}>

					<# _.each( data.family.choices, function( label, choice ) { #>
						<option value="{{ choice }}" <# if ( choice === data.family.value ) { #> selected="selected" <# } #>>{{ label }}</option>
					<# } ) #>

				</select>
			</li>
		<# } #>

		<# if ( data.weight && data.weight.choices ) { #>

			<li class="typography-font-weight">

				<# if ( data.weight.label ) { #>
					<span class="customize-control-title">{{ data.weight.label }}</span>
				<# } #>

				<select {{{ data.weight.link }}}>

					<# _.each( data.weight.choices, function( label, choice ) { #>

						<option value="{{ choice }}" <# if ( choice === data.weight.value ) { #> selected="selected" <# } #>>{{ label }}</option>

					<# } ) #>

				</select>
			</li>
		<# } #>

		<# if ( data.style && data.style.choices ) { #>

			<li class="typography-font-style">

				<# if ( data.style.label ) { #>
					<span class="customize-control-title">{{ data.style.label }}</span>
				<# } #>

				<select {{{ data.style.link }}}>

					<# _.each( data.style.choices, function( label, choice ) { #>

						<option value="{{ choice }}" <# if ( choice === data.style.value ) { #> selected="selected" <# } #>>{{ label }}</option>

					<# } ) #>

				</select>
			</li>
		<# } #>

		<# if ( data.size ) { #>

			<li class="typography-font-size">

				<# if ( data.size.label ) { #>
					<span class="customize-control-title">{{ data.size.label }} (px)</span>
				<# } #>

				<input type="number" min="1" {{{ data.size.link }}} value="{{ data.size.value }}" />

			</li>
		<# } #>

		<# if ( data.line_height ) { #>

			<li class="typography-line-height">

				<# if ( data.line_height.label ) { #>
					<span class="customize-control-title">{{ data.line_height.label }} (px)</span>
				<# } #>

				<input type="number" min="1" {{{ data.line_height.link }}} value="{{ data.line_height.value }}" />

			</li>
		<# } #>

		<# if ( data.letter_spacing ) { #>

			<li class="typography-letter-spacing">

				<# if ( data.letter_spacing.label ) { #>
					<span class="customize-control-title">{{ data.letter_spacing.label }} (px)</span>
				<# } #>

				<input type="number" min="1" {{{ data.letter_spacing.link }}} value="{{ data.letter_spacing.value }}" />

			</li>
		<# } #>

		</ul>
	<?php }

	/**
	 * Returns the available fonts.  Fonts should have available weights, styles, and subsets.
	 *
	 * @todo Integrate with Google fonts.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function get_fonts() { return array(); }

	/**
	 * Returns the available font families.
	 *
	 * @todo Pull families from `get_fonts()`.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	function get_font_families() {

		return array(
			'' => __( 'No Fonts', 'vw-book-store' ),
        'Abril Fatface' => __( 'Abril Fatface', 'vw-book-store' ),
        'Acme' => __( 'Acme', 'vw-book-store' ),
        'Anton' => __( 'Anton', 'vw-book-store' ),
        'Architects Daughter' => __( 'Architects Daughter', 'vw-book-store' ),
        'Arimo' => __( 'Arimo', 'vw-book-store' ),
        'Arsenal' => __( 'Arsenal', 'vw-book-store' ),
        'Arvo' => __( 'Arvo', 'vw-book-store' ),
        'Alegreya' => __( 'Alegreya', 'vw-book-store' ),
        'Alfa Slab One' => __( 'Alfa Slab One', 'vw-book-store' ),
        'Averia Serif Libre' => __( 'Averia Serif Libre', 'vw-book-store' ),
        'Bangers' => __( 'Bangers', 'vw-book-store' ),
        'Boogaloo' => __( 'Boogaloo', 'vw-book-store' ),
        'Bad Script' => __( 'Bad Script', 'vw-book-store' ),
        'Bitter' => __( 'Bitter', 'vw-book-store' ),
        'Bree Serif' => __( 'Bree Serif', 'vw-book-store' ),
        'BenchNine' => __( 'BenchNine', 'vw-book-store' ),
        'Cabin' => __( 'Cabin', 'vw-book-store' ),
        'Cardo' => __( 'Cardo', 'vw-book-store' ),
        'Courgette' => __( 'Courgette', 'vw-book-store' ),
        'Cherry Swash' => __( 'Cherry Swash', 'vw-book-store' ),
        'Cormorant Garamond' => __( 'Cormorant Garamond', 'vw-book-store' ),
        'Crimson Text' => __( 'Crimson Text', 'vw-book-store' ),
        'Cuprum' => __( 'Cuprum', 'vw-book-store' ),
        'Cookie' => __( 'Cookie', 'vw-book-store' ),
        'Chewy' => __( 'Chewy', 'vw-book-store' ),
        'Days One' => __( 'Days One', 'vw-book-store' ),
        'Dosis' => __( 'Dosis', 'vw-book-store' ),
        'Droid Sans' => __( 'Droid Sans', 'vw-book-store' ),
        'Economica' => __( 'Economica', 'vw-book-store' ),
        'Fredoka One' => __( 'Fredoka One', 'vw-book-store' ),
        'Fjalla One' => __( 'Fjalla One', 'vw-book-store' ),
        'Francois One' => __( 'Francois One', 'vw-book-store' ),
        'Frank Ruhl Libre' => __( 'Frank Ruhl Libre', 'vw-book-store' ),
        'Gloria Hallelujah' => __( 'Gloria Hallelujah', 'vw-book-store' ),
        'Great Vibes' => __( 'Great Vibes', 'vw-book-store' ),
        'Handlee' => __( 'Handlee', 'vw-book-store' ),
        'Hammersmith One' => __( 'Hammersmith One', 'vw-book-store' ),
        'Inconsolata' => __( 'Inconsolata', 'vw-book-store' ),
        'Indie Flower' => __( 'Indie Flower', 'vw-book-store' ),
        'IM Fell English SC' => __( 'IM Fell English SC', 'vw-book-store' ),
        'Julius Sans One' => __( 'Julius Sans One', 'vw-book-store' ),
        'Josefin Slab' => __( 'Josefin Slab', 'vw-book-store' ),
        'Josefin Sans' => __( 'Josefin Sans', 'vw-book-store' ),
        'Kanit' => __( 'Kanit', 'vw-book-store' ),
        'Lobster' => __( 'Lobster', 'vw-book-store' ),
        'Lato' => __( 'Lato', 'vw-book-store' ),
        'Lora' => __( 'Lora', 'vw-book-store' ),
        'Libre Baskerville' => __( 'Libre Baskerville', 'vw-book-store' ),
        'Lobster Two' => __( 'Lobster Two', 'vw-book-store' ),
        'Merriweather' => __( 'Merriweather', 'vw-book-store' ),
        'Monda' => __( 'Monda', 'vw-book-store' ),
        'Montserrat' => __( 'Montserrat', 'vw-book-store' ),
        'Muli' => __( 'Muli', 'vw-book-store' ),
        'Marck Script' => __( 'Marck Script', 'vw-book-store' ),
        'Noto Serif' => __( 'Noto Serif', 'vw-book-store' ),
        'Open Sans' => __( 'Open Sans', 'vw-book-store' ),
        'Overpass' => __( 'Overpass', 'vw-book-store' ),
        'Overpass Mono' => __( 'Overpass Mono', 'vw-book-store' ),
        'Oxygen' => __( 'Oxygen', 'vw-book-store' ),
        'Orbitron' => __( 'Orbitron', 'vw-book-store' ),
        'Patua One' => __( 'Patua One', 'vw-book-store' ),
        'Pacifico' => __( 'Pacifico', 'vw-book-store' ),
        'Padauk' => __( 'Padauk', 'vw-book-store' ),
        'Playball' => __( 'Playball', 'vw-book-store' ),
        'Playfair Display' => __( 'Playfair Display', 'vw-book-store' ),
        'PT Sans' => __( 'PT Sans', 'vw-book-store' ),
        'Philosopher' => __( 'Philosopher', 'vw-book-store' ),
        'Permanent Marker' => __( 'Permanent Marker', 'vw-book-store' ),
        'Poiret One' => __( 'Poiret One', 'vw-book-store' ),
        'Quicksand' => __( 'Quicksand', 'vw-book-store' ),
        'Quattrocento Sans' => __( 'Quattrocento Sans', 'vw-book-store' ),
        'Raleway' => __( 'Raleway', 'vw-book-store' ),
        'Rubik' => __( 'Rubik', 'vw-book-store' ),
        'Rokkitt' => __( 'Rokkitt', 'vw-book-store' ),
        'Russo One' => __( 'Russo One', 'vw-book-store' ),
        'Righteous' => __( 'Righteous', 'vw-book-store' ),
        'Slabo' => __( 'Slabo', 'vw-book-store' ),
        'Source Sans Pro' => __( 'Source Sans Pro', 'vw-book-store' ),
        'Shadows Into Light Two' => __( 'Shadows Into Light Two', 'vw-book-store'),
        'Shadows Into Light' => __( 'Shadows Into Light', 'vw-book-store' ),
        'Sacramento' => __( 'Sacramento', 'vw-book-store' ),
        'Shrikhand' => __( 'Shrikhand', 'vw-book-store' ),
        'Tangerine' => __( 'Tangerine', 'vw-book-store' ),
        'Ubuntu' => __( 'Ubuntu', 'vw-book-store' ),
        'VT323' => __( 'VT323', 'vw-book-store' ),
        'Varela Round' => __( 'Varela Round', 'vw-book-store' ),
        'Vampiro One' => __( 'Vampiro One', 'vw-book-store' ),
        'Vollkorn' => __( 'Vollkorn', 'vw-book-store' ),
        'Volkhov' => __( 'Volkhov', 'vw-book-store' ),
        'Yanone Kaffeesatz' => __( 'Yanone Kaffeesatz', 'vw-book-store' )
		);
	}

	/**
	 * Returns the available font weights.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function get_font_weight_choices() {

		return array(
			'' => esc_html__( 'No Fonts weight', 'vw-book-store' ),
			'100' => esc_html__( 'Thin',       'vw-book-store' ),
			'300' => esc_html__( 'Light',      'vw-book-store' ),
			'400' => esc_html__( 'Normal',     'vw-book-store' ),
			'500' => esc_html__( 'Medium',     'vw-book-store' ),
			'700' => esc_html__( 'Bold',       'vw-book-store' ),
			'900' => esc_html__( 'Ultra Bold', 'vw-book-store' ),
		);
	}

	/**
	 * Returns the available font styles.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function get_font_style_choices() {

		return array(
			'normal'  => esc_html__( 'Normal', 'vw-book-store' ),
			'italic'  => esc_html__( 'Italic', 'vw-book-store' ),
			'oblique' => esc_html__( 'Oblique', 'vw-book-store' )
		);
	}
}
