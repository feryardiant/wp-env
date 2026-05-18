<?php
/**
 * This class handles theme activation, deactivation, and update checking.
 *
 * @package projek-xyz/wp-custom-theme
 * @copyright Copyright (c) 2026 Fery Wardiyanto <https://feryardiant.id>
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 */

declare( strict_types = 1 );

namespace Custom_Theme;

/**
 * Main theme class for managing custom theme functionality.
 */
class Theme {
	/**
	 * Fires the 'ct_activation' action upon theme activation.
	 *
	 * This method is triggered when the theme is activated via the 'after_switch_theme' hook.
	 *
	 * @return void
	 */
	public static function activation(): void {
		\do_action( 'ct_activation' );
	}

	/**
	 * Fires the 'ct_deactivation' action upon theme deactivation.
	 *
	 * This method is triggered when the theme is deactivated via the 'switch_theme' hook.
	 *
	 * @return void
	 */
	public static function deactivation(): void {
		\do_action( 'ct_deactivation' );
	}

	/**
	 * @return void
	 */
	public static function enqueue_scripts(): void {
		$theme = \wp_get_theme();

		\wp_register_script(
			$theme->stylesheet,
			$theme->get_stylesheet_directory_uri() . '/assets/custom.js',
			array(),
			$theme->version,
			array( 'strategy' => 'defer' )
		);

		\wp_enqueue_script( $theme->stylesheet );
	}

	/**
	 * Checks if a theme update is available.
	 *
	 * This method filters the theme update transient to include custom theme updates
	 * from a remote source (e.g., GitHub).
	 *
	 * @param array|false $update           The update transient data.
	 * @param array       $theme_data       Information about the current theme.
	 * @param string      $theme_stylesheet The stylesheet name of the theme being checked.
	 *
	 * @return array|false Updated transient data if a new version is available, otherwise the original data.
	 */
	public static function check_updates(
		array|false $update,
		array $theme_data,
		string $theme_stylesheet,
	): array|false {
		// Only handle our custom theme.
		if ( 'custom-theme' !== $theme_stylesheet ) {
			return $update;
		}

		$update_data = self::get_updates();

		// Check if remote version is newer than current version.
		if ( ! $update_data || version_compare( $update_data['version'], $theme_data['Version'], '<=' ) ) {
			return $update;
		}

		// Return the update metadata for WordPress to handle.
		return array(
			'theme'        => '',
			'package'      => '',
			'version'      => $update_data['version'],
			'url'          => '',
			'description'  => '',
			'tested'       => '',
			'requires_php' => '',
			'translations' => array(),
		);
	}

	/**
	 * Retrieves the latest update information from the remote repository.
	 *
	 * Uses site transients to cache the results and avoid excessive API calls.
	 *
	 * @return array|false The update data from GitHub on success, or false on failure.
	 */
	public static function get_updates(): array|false {
		$cache_key   = 'custom-theme_updates';
		$cached_data = \get_site_transient( $cache_key );

		// Return cached data if available.
		if ( false !== $cached_data ) {
			return $cached_data;
		}

		// Fetch the latest release from GitHub API.
		$response = \wp_remote_get(
			'https://api.github.com/repos/projek-xyz/wp-env/releases/latest',
			array(
				'timeout' => 10,
				'headers' => array( 'Accept' => 'application/vnd.github.v3+json' ),
			)
		);

		// Handle fetch errors or non-200 responses.
		if ( is_wp_error( $response ) || 200 !== \wp_remote_retrieve_response_code( $response ) ) {
			\set_site_transient( $cache_key, null, \HOUR_IN_SECONDS );

			return false;
		}

		$data = json_decode( \wp_remote_retrieve_body( $response ), true );

		// Cache the response data for 12 hours.
		\set_site_transient( $cache_key, $data, 12 * \HOUR_IN_SECONDS );

		return $data;
	}
}
