<?php
/**
 * Admin component.
 *
 * @package HiveTheme\Components
 */

namespace HiveTheme\Components;

use HiveTheme\Helpers as ht;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Admin component class.
 *
 * @class Admin
 */
final class Admin extends Component {

	/**
	 * Class constructor.
	 *
	 * @param array $args Component arguments.
	 */
	public function __construct( $args = [] ) {

		// Manage meta boxes.
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ], 10, 2 );
		add_action( 'save_post', [ $this, 'update_meta_box' ] );

		parent::__construct( $args );
	}

	/**
	 * Adds meta boxes.
	 *
	 * @param string  $post_type Post type.
	 * @param WP_Post $post Post object.
	 */
	public function add_meta_boxes( $post_type, $post ) {

		// Check permissions.
		if ( ! ht\is_class_instance( $post, 'WP_Post' ) || ! current_user_can( 'edit_post', $post->ID ) ) {
			return;
		}

		// Add meta boxes.
		foreach ( hivetheme()->get_config( 'meta_boxes' ) as $name => $args ) {
			if ( in_array( $post_type, (array) $args['screen'], true ) && $args['fields'] ) {
				add_meta_box( ht\prefix( $name ), $args['title'], [ $this, 'render_meta_box' ], $args['screen'], $args['context'], $args['priority'] );
			}
		}
	}

	/**
	 * Renders meta box fields.
	 *
	 * @param WP_Post $post Post object.
	 * @param array   $args Meta box arguments.
	 */
	public function render_meta_box( $post, $args ) {
		$output = '';

		// Get meta box name.
		$meta_box_name = ht\unprefix( $args['id'] );

		// Get meta box.
		$meta_box = ht\get_array_value( hivetheme()->get_config( 'meta_boxes' ), $meta_box_name );

		if ( $meta_box ) {
			foreach ( ht\sort_array( $meta_box['fields'] ) as $field_name => $field_args ) {

				// Check field type.
				if ( 'checkbox' !== $field_args['type'] ) {
					continue;
				}

				// Get field name.
				$field_name = ht\prefix( $field_name );

				// Get field value.
				$field_value = get_post_meta( $post->ID, $field_name, true );

				// Render field.
				$output .= '<div class="editor-post-panel__row"><div class="editor-post-panel__row-control">';
				$output .= '<label for="' . esc_attr( $field_name ) . '">';

				$output .= '<input type="checkbox" name="' . esc_attr( $field_name ) . '" id="' . esc_attr( $field_name ) . '" value="1" ' . checked( '1', $field_value, false ) . '>';
				$output .= '<span>' . esc_html( $field_args['label'] ) . '</span>';

				$output .= '</label>';
				$output .= '</div></div>';
			}
		}

		echo $output;
	}

	/**
	 * Updates meta box values.
	 *
	 * @param int $post_id Post ID.
	 */
	public function update_meta_box( $post_id ) {

		// Check permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Check action.
		if ( ht\get_array_value( $_POST, 'action' ) !== 'editpost' ) {
			return;
		}

		// Check autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check post ID.
		if ( get_the_ID() !== $post_id ) {
			return;
		}

		// Remove action.
		remove_action( 'save_post', [ $this, 'update_meta_box' ] );

		// Get post type.
		$post_type = get_post_type( $post_id );

		// Update field values.
		foreach ( hivetheme()->get_config( 'meta_boxes' ) as $meta_box ) {
			if ( in_array( $post_type, (array) $meta_box['screen'], true ) ) {
				foreach ( $meta_box['fields'] as $field_name => $field_args ) {

					// Check field type.
					if ( 'checkbox' !== $field_args['type'] ) {
						continue;
					}

					// Get field name.
					$field_name = ht\prefix( $field_name );

					// Update field values.
					if ( isset( $_POST[ $field_name ] ) ) {
						update_post_meta( $post_id, $field_name, '1' );
					} else {
						delete_post_meta( $post_id, $field_name );
					}
				}
			}
		}
	}
}
