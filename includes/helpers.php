<?php
/**
 * Helper functions.
 *
 * @package HiveTheme
 */

namespace HiveTheme\Helpers;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Adds HiveTheme prefix.
 *
 * @param mixed $names Names to prefix.
 * @return mixed
 */
function prefix( $names ) {
	$prefixed = '';

	if ( is_array( $names ) ) {
		$prefixed = array_map(
			function( $name ) {
				return 'ht_' . $name;
			},
			$names
		);
	} else {
		$prefixed = 'ht_' . $names;
	}

	return $prefixed;
}

/**
 * Removes HiveTheme prefix.
 *
 * @param mixed $names Names to unprefix.
 * @return mixed
 */
function unprefix( $names ) {
	$unprefixed = '';

	if ( is_array( $names ) ) {
		$unprefixed = array_map(
			function( $name ) {
				return preg_replace( '/^ht_/', '', $name );
			},
			$names
		);
	} else {
		$unprefixed = preg_replace( '/^ht_/', '', $names );
	}

	return $unprefixed;
}

/**
 * Sanitizes slug.
 *
 * @param string $text Text to sanitize.
 * @return string
 */
function sanitize_slug( $text ) {
	return str_replace( '_', '-', \sanitize_key( $text ) );
}

/**
 * Gets array item value by key.
 *
 * @param array  $array Source array.
 * @param string $key Key to search.
 * @param mixed  $default Default value.
 * @return mixed
 */
function get_array_value( $array, $key, $default = null ) {
	$value = $default;

	if ( is_array( $array ) && isset( $array[ $key ] ) ) {
		$value = $array[ $key ];
	}

	return $value;
}

/**
 * Gets first array item value.
 *
 * @param array $array Source array.
 * @param mixed $default Default value.
 * @return mixed
 */
function get_first_array_value( $array, $default = null ) {
	$value = $default;

	if ( is_array( $array ) && $array ) {
		$value = reset( $array );
	}

	return $value;
}

/**
 * Gets last array item value.
 *
 * @param array $array Source array.
 * @param mixed $default Default value.
 * @return mixed
 */
function get_last_array_value( $array, $default = null ) {
	$value = $default;

	if ( is_array( $array ) && $array ) {
		$value = end( $array );
	}

	return $value;
}

/**
 * Merges arrays with mixed values.
 *
 * @return array
 */
function merge_arrays() {
	$merged = [];

	foreach ( func_get_args() as $array ) {
		foreach ( $array as $key => $value ) {
			if ( ! isset( $merged[ $key ] ) || ( ! is_array( $merged[ $key ] ) || ! is_array( $value ) ) ) {
				if ( is_numeric( $key ) ) {
					$merged[] = $value;
				} else {
					$merged[ $key ] = $value;
				}
			} else {
				$merged[ $key ] = merge_arrays( $merged[ $key ], $value );
			}
		}
	}

	return $merged;
}

/**
 * Sorts array by a custom order.
 *
 * @param array $array Source array.
 * @return array
 */
function sort_array( $array ) {
	foreach ( $array as $key => $value ) {
		if ( is_array( $value ) ) {

			// @deprecated since version 1.3.0.
			if ( isset( $value['order'] ) && is_int( $value['order'] ) ) {
				$array[ $key ]['_order'] = $value['order'];
			} elseif ( ! isset( $value['_order'] ) ) {
				$array[ $key ]['_order'] = 0;
			}
		}
	}

	return wp_list_sort( $array, '_order', 'ASC', true );
}

/**
 * Creates class instance.
 *
 * @param string $class Class name.
 * @param array  $args Instance arguments.
 * @return mixed
 */
function create_class_instance( $class, $args = [] ) {
	if ( class_exists( $class ) && ! ( new \ReflectionClass( $class ) )->isAbstract() ) {
		$instance = null;

		if ( empty( $args ) ) {
			$instance = new $class();
		} else {
			$instance = new $class( ...$args );
		}

		return $instance;
	}
}

/**
 * Checks if object is a class instance.
 *
 * @param object $object Class object.
 * @param string $class Class name.
 * @return bool
 */
function is_class_instance( $object, $class ) {
	return is_object( $object ) && strtolower( get_class( $object ) ) === ltrim( strtolower( $class ), '\\' );
}

/**
 * Checks plugin status.
 *
 * @param string $name Plugin name.
 * @return bool
 */
function is_plugin_active( $name ) {
	return class_exists( $name ) || function_exists( $name );
}
