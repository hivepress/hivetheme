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

		// Register theme demos.
		add_filter( 'ocdi/import_files', [ $this, 'register_demos' ] );

		// Reset sidebar widgets.
		add_action( 'ocdi/widget_importer_before_widgets_import', [ $this, 'reset_widgets' ] );

		// Save content mapping.
		add_action( 'ocdi/after_content_import_execution', [ $this, 'save_content_mapping' ] );

		// Update customizer file.
		add_action( 'ocdi/customizer_import_execution', [ $this, 'update_customizer_file' ], 5 );

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
	 * Saves content mapping.
	 */
	public function save_content_mapping() {
		$mapping = \OCDI\OneClickDemoImport::get_instance()->importer->get_importer_data()['mapping'] ?? [];

		set_transient( ht\prefix( 'importer_mapping' ), $mapping, HOUR_IN_SECONDS );
	}

	/**
	 * Updates customizer file.
	 *
	 * @param array $files Import files.
	 */
	public function update_customizer_file( $files ) {

		// Check customizer file.
		if ( empty( $files['customizer'] ) ) {
			return;
		}

		// Get content mapping.
		$mapping = get_transient( ht\prefix( 'importer_mapping' ) );

		if ( empty( $mapping['post'] ) && empty( $mapping['term_id'] ) ) {
			return;
		}

		// Get customizer data.
		$raw_data = @file_get_contents( $files['customizer'] );

		if ( false === $raw_data ) {
			return;
		}

		$data = @unserialize( $raw_data, [ 'allowed_classes' => false ] );

		if ( ! is_array( $data ) ) {
			return;
		}

		// Set logo ID.
		if ( isset( $data['mods']['custom_logo'], $mapping['post'][ $data['mods']['custom_logo'] ] ) ) {
			$data['mods']['custom_logo'] = $mapping['post'][ $data['mods']['custom_logo'] ];
		}

		// Set menu IDs.
		if ( ! empty( $data['mods']['nav_menu_locations'] ) && is_array( $data['mods']['nav_menu_locations'] ) ) {
			foreach ( $data['mods']['nav_menu_locations'] as $location => $term_id ) {
				if ( isset( $mapping['term_id'][ $term_id ] ) ) {
					$data['mods']['nav_menu_locations'][ $location ] = $mapping['term_id'][ $term_id ];
				}
			}
		}

		// Set page IDs.
		if ( ! empty( $data['options'] ) && is_array( $data['options'] ) ) {
			foreach ( $data['options'] as $key => $value ) {
				$is_page = in_array( $key, [ 'page_on_front', 'page_for_posts' ], true ) || 0 === strpos( $key, 'hp_page_' );

				if ( $is_page && isset( $mapping['post'][ $value ] ) ) {
					$data['options'][ $key ] = $mapping['post'][ $value ];
				}
			}
		}

		// Update customizer file.
		@file_put_contents( $files['customizer'], serialize( $data ) );

		// Delete content mapping.
		delete_transient( ht\prefix( 'importer_mapping' ) );
	}
}
