<?php
/**
 * Plugin Name: HiveTheme
 * Description: todo.
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
