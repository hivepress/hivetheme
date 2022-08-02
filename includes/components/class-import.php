<?php
/**
 * Import component.
 *
 * @package HiveTheme\Components
 */

namespace HiveTheme\Components;

use HiveTheme\Helpers as ht;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Import component class.
 *
 * @class Import
 */
final class Import extends Component {

	/**
	 * Class constructor.
	 *
	 * @param array $args Component arguments.
	 */
	public function __construct( $args = [] ) {
		if ( is_admin() ) {

			// Register theme demos.
			add_filter( 'pt-ocdi/import_files', [ $this, 'register_demos' ] );

			// Assign data.
			add_action( 'ocdi/after_import', [ $this, 'assign_import_data' ] );

			// Reset sidebar widgets.
			add_action( 'pt-ocdi/widget_importer_before_widgets_import', [ $this, 'reset_widgets' ] );
		}

		parent::__construct( $args );
	}

	/**
	 * Registers theme demos.
	 */
	public function register_demos() {
		return hivetheme()->get_config( 'theme_demos' );
	}

	/**
	 * Resets sidebar widgets.
	 */
	public function reset_widgets() {
		update_option( 'sidebars_widgets', [] );
	}

	/**
	 * Assign import data.
	 *
	 * @param array $selected_import Selected import data.
	 */
	public function assign_import_data( $selected_import ) {

		// Get theme demo config.
		$config = hivetheme()->get_config( 'theme_demos' );

		if ( ! $config ) {
			return;
		}

		// Get settings file.
		$file = wp_remote_get( ht\get_array_value( $config[0], 'settings' ) );

		if ( wp_remote_retrieve_response_code( $file ) !== 200 ) {
			return;
		}

		// Get settings file content.
		$settings = json_decode( wp_remote_retrieve_body( $file ), true );

		// Set pages.
		foreach ( $settings['pages'][ strtolower( $selected_import['import_file_name'] ) ] as $name => $value ) {

			$page = get_page_by_path( $value );

			if ( ! $page ) {
				continue;
			}

			update_option( $name, $page->ID );
		}

		// Update options.
		foreach ( $settings['options'][ strtolower( $selected_import['import_file_name'] ) ] as $name => $value ) {
			update_option( $name, $value );
		}

		// Assign menus.
		$menus = [];

		foreach ( ht\get_array_value( $settings, 'menus' ) as $name ) {
			$menu = get_term_by( 'slug', $name, 'nav_menu' );

			if ( ! $menu ) {
				continue;
			}

			$menus[ $name ] = $menu->term_id;
		}

		if ( $menus ) {
			set_theme_mod( 'nav_menu_locations', $menus );
		}

		// Assign images.
		foreach ( $settings['images'][ strtolower( $selected_import['import_file_name'] ) ] as $mode => $value ) {
			$attachemnt = wp_upload_dir()['baseurl'] . $value;

			if ( 'custom_logo ' === $mode ) {
				$attachemnt = attachment_url_to_postid( wp_upload_dir()['baseurl'] . $value );
			}

			if ( ! $attachemnt ) {
				continue;
			}

			set_theme_mod( $mode, $attachemnt );
		}

	}
}
