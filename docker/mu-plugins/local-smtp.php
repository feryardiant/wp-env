<?php
/**
 * Plugin Name: Local SMTP
 * Plugin URI: https://github.com/feryardiant/wordpress-env
 * Description: Local SMTP mail configuration.
 * Version: 0.0.0
 * Author: Fery Wardiyanto
 * Author URI: https://feryardiant.id/
 * License: MIT License
 */

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
