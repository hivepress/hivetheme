<?php
/**
 * Meta boxes configuration.
 *
 * @package HiveTheme\Configs
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

return [
	'page_settings' => [
		'title'    => esc_html__( 'Layout', 'listinghive' ),
		'screen'   => 'page',
		'context'  => 'side',
		'priority' => 'default',

		'fields'   => [
			'hide_title'  => [
				'label'  => esc_html__( 'Hide page title', 'listinghive' ),
				'type'   => 'checkbox',
				'_order' => 10,
			],

			'hide_header' => [
				'label'  => esc_html__( 'Hide site header', 'listinghive' ),
				'type'   => 'checkbox',
				'_order' => 20,
			],

			'hide_footer' => [
				'label'  => esc_html__( 'Hide site footer', 'listinghive' ),
				'type'   => 'checkbox',
				'_order' => 30,
			],
		],
	],

	'post_settings' => [
		'title'    => esc_html__( 'Layout', 'listinghive' ),
		'screen'   => 'post',
		'context'  => 'side',
		'priority' => 'default',

		'fields'   => [
			'hide_hero'   => [
				'label'  => esc_html__( 'Hide post header', 'listinghive' ),
				'type'   => 'checkbox',
				'_order' => 10,
			],

			'hide_header' => [
				'label'  => esc_html__( 'Hide site header', 'listinghive' ),
				'type'   => 'checkbox',
				'_order' => 20,
			],

			'hide_footer' => [
				'label'  => esc_html__( 'Hide site footer', 'listinghive' ),
				'type'   => 'checkbox',
				'_order' => 30,
			],
		],
	],
];
