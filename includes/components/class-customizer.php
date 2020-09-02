<?php
/**
 * Customizer component.
 *
 * @package HiveTheme\Components
 */

namespace HiveTheme\Components;

use HiveTheme\Helpers as ht;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Customizer component class.
 *
 * @class Customizer
 */
final class Customizer extends Component {

	/**
	 * Array of defaults.
	 *
	 * @var array
	 */
	protected $defaults = [];

	/**
	 * Class constructor.
	 *
	 * @param array $args Component arguments.
	 */
	public function __construct( $args = [] ) {

		// Set theme defaults.
		add_action( 'init', [ $this, 'set_theme_defaults' ] );

		// Register theme mods.
		add_action( 'customize_register', [ $this, 'register_theme_mods' ], 1000 );

		if ( is_admin() ) {

			// Add editor fonts.
			add_action( 'admin_init', [ $this, 'add_editor_fonts' ] );

			// Reset theme styles.
			add_action( 'customize_save_after', [ $this, 'reset_theme_styles' ] );
		} else {

			// Add theme styles.
			add_action( 'wp_enqueue_scripts', [ $this, 'add_theme_styles' ] );

			// Enqueue fonts.
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_fonts' ] );
		}

		parent::__construct( $args );
	}

	/**
	 * Sets theme defaults.
	 */
	public function set_theme_defaults() {
		foreach ( hivetheme()->get_config( 'theme_mods' ) as $section ) {
			foreach ( $section['fields'] as $name => $args ) {
				if ( isset( $args['default'] ) ) {
					$this->defaults[ $name ] = $args['default'];

					add_filter( 'theme_mod_' . $name, [ $this, 'set_theme_default' ] );
				}
			}
		}
	}

	/**
	 * Sets theme default.
	 *
	 * @param mixed $value Mod value.
	 * @return mixed
	 */
	public function set_theme_default( $value ) {
		if ( false === $value ) {
			$name = substr( current_filter(), strlen( 'theme_mod_' ) );

			$value = $this->defaults[ $name ];
		}

		return $value;
	}

	/**
	 * Registers theme mods.
	 *
	 * @param WP_Customize_Manager $wp_customize Manager object.
	 */
	public function register_theme_mods( $wp_customize ) {
		foreach ( hivetheme()->get_config( 'theme_mods' ) as $section_name => $section ) {

			// Add custom section.
			if ( ! in_array( $section_name, array_keys( $wp_customize->sections() ), true ) ) {
				$wp_customize->add_section(
					$section_name,
					[
						'title'       => $section['title'],
						'description' => ht\get_array_value( $section, 'description' ),
						'priority'    => ht\get_array_value( $section, 'priority' ),
					]
				);
			}

			foreach ( $section['fields'] as $field_name => $field ) {

				// Set sanitization callback.
				$sanitize_callback = 'sanitize_text_field';

				switch ( $field['type'] ) {

					// Text field.
					case 'textarea':
						$sanitize_callback = 'wp_kses_post';

						break;

					// Color field.
					case 'color':
						$sanitize_callback = 'sanitize_hex_color';

						break;

					// Font field.
					case 'font':
						$sanitize_callback = [ $this, 'sanitize_select_field' ];

						break;
				}

				// Add mod setting.
				$wp_customize->add_setting(
					$field_name,
					[
						'default'           => ht\get_array_value( $field, 'default' ),
						'sanitize_callback' => $sanitize_callback,
					]
				);

				// Add mod control.
				$control = array_merge(
					$field,
					[
						'section'  => $section_name,
						'settings' => $field_name,
					]
				);

				unset( $control['type'] );

				switch ( $field['type'] ) {

					// Color field.
					case 'color':
						$wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, $field_name, $control ) );

						break;

					// Default field.
					default:
						$control['type'] = $field['type'];

						if ( 'font' === $control['type'] ) {
							$control['type']    = 'select';
							$control['choices'] = hivetheme()->get_config( 'fonts' );
						}

						$wp_customize->add_control( $field_name, $control );

						break;
				}
			}
		}
	}

	/**
	 * Sanitizes select field.
	 *
	 * @param string               $input Input value.
	 * @param WP_Customize_Setting $setting Setting object.
	 * @return string
	 */
	public function sanitize_select_field( $input, $setting ) {
		$output = $setting->default;

		if ( array_key_exists( $input, $setting->manager->get_control( $setting->id )->choices ) ) {
			$output = $input;
		}

		return $output;
	}

	/**
	 * Resets theme styles.
	 */
	public function reset_theme_styles() {
		remove_theme_mod( 'theme_styles' );
	}

	/**
	 * Adds theme styles.
	 */
	public function add_theme_styles() {

		// Get cached styles.
		$styles = get_theme_mod( 'theme_styles' );

		if ( false === $styles || is_customize_preview() ) {

			// Get styles.
			$styles = '';

			foreach ( hivetheme()->get_config( 'theme_styles' ) as $style ) {

				// Get rules.
				$rules = '';

				foreach ( $style['properties'] as $property ) {

					// Get value.
					$value = get_theme_mod( $property['theme_mod'] );

					if ( $value ) {
						switch ( $property['name'] ) {

							// Background image.
							case 'background-image':
								$value = 'url(' . esc_url( $value ) . ')';

								break;

							// Font family.
							case 'font-family':
								$value .= ', sans-serif';

								break;
						}

						$rules .= $property['name'] . ':' . $value . ';';
					}
				}

				// Add rules.
				if ( $rules ) {
					$styles .= $style['selector'] . '{' . $rules . '}';
				}
			}

			// Minify styles.
			$styles = preg_replace( '/[\t\r\n]+/', '', $styles );

			// Cache styles.
			set_theme_mod( 'theme_styles', $styles );
		}

		// Add styles.
		wp_add_inline_style( 'hivetheme-parent-frontend', $styles );
	}

	/**
	 * Gets fonts URL.
	 *
	 * @return string
	 */
	protected function get_fonts_url() {
		$url = '';

		// Get fonts.
		$fonts = [];

		foreach ( [ 'heading_font', 'body_font' ] as $name ) {
			$font = get_theme_mod( $name );

			if ( $font ) {
				if ( $font === $this->defaults[ $name ] ) {
					$font_weight = get_theme_mod( $name . '_weight' );

					if ( $font_weight ) {
						$font .= ':' . $font_weight;
					}
				}

				$fonts[] = $font;
			}
		}

		// Set URL.
		if ( $fonts ) {
			$url = 'https://fonts.googleapis.com/css?' . http_build_query(
				[
					'family'  => implode( '|', $fonts ),
					'display' => 'swap',
				]
			);
		}

		return $url;
	}

	/**
	 * Enqueues fonts.
	 */
	public function enqueue_fonts() {

		// Get URL.
		$url = $this->get_fonts_url();

		// Enqueue fonts.
		if ( $url ) {
			wp_enqueue_style( 'google-fonts', esc_url( $url ), [], null );
		}
	}

	/**
	 * Adds editor fonts.
	 */
	public function add_editor_fonts() {

		// Get URL.
		$url = $this->get_fonts_url();

		// Enqueue fonts.
		if ( $url ) {
			add_editor_style( esc_url( $url ) );
		}
	}
}
