<?php
/**
 * Theme styles configuration.
 *
 * @package HiveTheme\Configs
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

return [
	[
		'selector'   => '
			h1,
			h2,
			h3,
			h4,
			h5,
			h6,
			fieldset legend,
			.header-logo__name,
			.comment__author,
			.hp-review__author,
			.hp-message--view-block hp-message__sender,
			.woocommerce ul.product_list_widget li .product-title
		',

		'properties' => [
			[
				'name'      => 'font-family',
				'theme_mod' => 'heading_font',
			],
		],
	],

	[
		'selector'   => '
			body
		',

		'properties' => [
			[
				'name'      => 'font-family',
				'theme_mod' => 'body_font',
			],
		],
	],

	[
		'selector'   => '
			blockquote,
			.wp-block-quote,
			.comment.bypostauthor .comment__image
		',

		'properties' => [
			[
				'name'      => 'border-color',
				'theme_mod' => 'primary_color',
			],
		],
	],

	[
		'selector'   => '
			.hp-field input[type=radio]:checked + span::before,
			.hp-field input[type=checkbox]:checked + span::before
		',

		'properties' => [
			[
				'name'      => 'border-color',
				'theme_mod' => 'secondary_color',
			],
		],
	],
];
