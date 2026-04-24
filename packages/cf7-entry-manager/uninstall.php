<?php
/**
 * Entry Manager for Contact Form 7 Uninstaller
 *
 * Uninstalling Entry Manager for Contact Form 7 deletes submissions and options.
 *
 * @package feryardiant/cf7-entry-manager
 * @copyright Copyright (c) 2026 Fery Wardiyanto <https://feryardiant.id>
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

// Clear any cached data that has been removed.
wp_cache_flush();
