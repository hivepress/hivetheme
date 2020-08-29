<?php
/**
 * Template component.
 *
 * @package HiveTheme\Components
 */

namespace HiveTheme\Components;

use HiveTheme\Helpers as ht;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Template component class.
 *
 * @class Template
 */
final class Template extends Component {

	/**
	 * Class constructor.
	 *
	 * @param array $args Component arguments.
	 */
	public function __construct( $args = [] ) {

		// Add theme supports.
		add_action( 'after_setup_theme', [ $this, 'add_theme_supports' ] );

		// Register menu locations.
		add_action( 'after_setup_theme', [ $this, 'register_menu_locations' ] );

		// Register widget areas.
		add_action( 'widgets_init', [ $this, 'register_widget_areas' ] );

		parent::__construct( $args );
	}

	/**
	 * Adds theme supports.
	 */
	public function add_theme_supports() {
		add_theme_support( 'title-tag' );
		add_theme_support( 'automatic-feed-links' );

		foreach ( hivetheme()->get_config( 'theme_supports' ) as $name => $args ) {
			if ( is_array( $args ) ) {
				add_theme_support( $name, $args );
			} else {
				add_theme_support( $args );
			}
		}
	}

	/**
	 * Registers menu locations.
	 */
	public function register_menu_locations() {
		foreach ( hivetheme()->get_config( 'menu_locations' ) as $name => $args ) {
			register_nav_menu( $name, ht\get_array_value( $args, 'description' ) );
		}
	}

	/**
	 * Registers widget areas.
	 */
	public function register_widget_areas() {
		foreach ( hivetheme()->get_config( 'widget_areas' ) as $name => $args ) {
			$plugin = ht\get_array_value( $args, 'plugin' );

			if ( 'hivepress' === $plugin ) {
				$name = 'hp_' . $name;
			}

			if ( empty( $plugin ) || ht\is_plugin_active( $plugin ) ) {
				register_sidebar( array_merge( $args, [ 'id' => $name ] ) );
			}
		}
	}

	/**
	 * Renders template part.
	 *
	 * @param string $path File path.
	 * @param array  $context Template context.
	 * @return string
	 */
	public function render_part( $path, $context = [] ) {
		$output = '';

		// Extract context.
		unset( $context['path'] );
		unset( $context['output'] );

		extract( $context );

		// Render template.
		ob_start();

		include locate_template( $path . '.php' );
		$output = ob_get_contents();

		ob_end_clean();

		return $output;
	}

	/**
	 * Renders template.
	 *
	 * @param string $path Template path.
	 * @param array  $context Template context.
	 * @return string
	 * @deprecated Since version 1.1.0
	 */
	public function render_template( $path, $context = [] ) {
		return $this->render_part( $path, $context );
	}

	/**
	 * Renders header.
	 *
	 * @return string
	 * @deprecated Since version 1.2.0
	 */
	public function render_header() {
		return apply_filters( 'hivetheme/v1/areas/site_hero', '' );
	}
}
