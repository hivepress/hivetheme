<?php
/**
 * Plugin Name: HiveTheme
 * Description: A lightweight framework for HivePress themes.
 * Version: 1.0.0
 * Author: HivePress
 * Author URI: https://hivepress.io/
 * Text Domain: hivetheme
 * Domain Path: /languages/
 *
 * @package HiveTheme
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Define the core file.
if ( ! defined( 'HT_FILE' ) ) {
	define( 'HT_FILE', __FILE__ );
}

// Include the core class.
require_once __DIR__ . '/includes/class-core.php';

/**
 * Returns the core instance.
 *
 * @return HiveTheme\Core
 */
function hivetheme() {
	return HiveTheme\Core::instance();
}

// Initialize HiveTheme.
hivetheme();
