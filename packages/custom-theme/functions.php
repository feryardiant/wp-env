<?php
/**
 * Local custom theme
 *
 * @package feryardiant/wp-custom-theme
 * @copyright Copyright (c) 2026 Fery Wardiyanto <https://feryardiant.id>
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 */

add_action(
	'wp_enqueue_scripts',
	static function (): void {
		$theme = wp_get_theme( get_stylesheet() );

		wp_register_script(
			$theme->stylesheet,
			get_stylesheet_directory_uri() . '/assets/custom.js',
			array(),
			$theme->version,
			array( 'strategy' => 'defer' )
		);

		wp_enqueue_script( $theme->stylesheet );
	}
);

/**
 * Trigger custom theme activation hook.
 */
add_action(
	'after_switch_theme',
	static function (): void {
		do_action( 'ct_activation' );
	},
	10,
	0
);

/**
 * Trigger custom theme deactivation hook.
 */
add_action(
	'switch_theme',
	static function (): void {
		do_action( 'ct_deactivation' );
	},
	10,
	0
);

/**
 * Configure PHPMailer SMTP driver for local development.
 */
add_action(
	'phpmailer_init',
	static function ( WP_PHPMailer $mailer ) {
		if ( ! function_exists( 'getenv_docker' ) ) {
			return;
		}

		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$mailer->Host     = getenv_docker( 'SMTP_HOST', 'mail' );
		$mailer->Port     = (int) getenv_docker( 'SMTP_PORT', 1025 );
		$mailer->Username = getenv_docker( 'SMTP_USER', '' );
		$mailer->Password = getenv_docker( 'SMTP_PASS', '' );
		// phpcs:enable

		$mailer->isSMTP();
	}
);
