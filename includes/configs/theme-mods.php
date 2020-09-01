<?php
/**
 * Theme mods configuration.
 *
 * @package HiveTheme\Configs
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

return [
	'title_tagline' => [
		'fields' => [
			'copyright_notice' => [
				'label' => esc_html__( 'Copyright Notice', 'hivetheme' ),
				'type'  => 'textarea',
			],
		],
	],

	'colors'        => [
		'title'  => esc_html__( 'Colors', 'hivetheme' ),

		'fields' => [
			'primary_color'   => [
				'label' => esc_html__( 'Primary Color', 'hivetheme' ),
				'type'  => 'color',
			],

			'secondary_color' => [
				'label' => esc_html__( 'Secondary Color', 'hivetheme' ),
				'type'  => 'color',
			],
		],
	],

	'fonts'         => [
		'title'  => esc_html__( 'Fonts', 'hivetheme' ),

		'fields' => [
			'heading_font' => [
				'label' => esc_html__( 'Heading Font', 'hivetheme' ),
				'type'  => 'font',
			],

			'body_font'    => [
				'label' => esc_html__( 'Body Font', 'hivetheme' ),
				'type'  => 'font',
			],
		],
	],
];
