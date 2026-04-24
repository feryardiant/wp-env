<?php

declare(strict_types=1);

namespace IntegrationTests;

use Fixtures\TestCase;

/**
 * Base Test Case for integration tests using real WordPress core.
 */
abstract class BaseTestCase extends TestCase
{
    /**
     * Setup before any test in this class runs.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        // Path to the WordPress core directory for testing.
        if (! defined('WP_CORE_DIR')) {
            define('WP_CORE_DIR', ABSPATH);
        }

        defined('WP_TESTS_DOMAIN') || define('WP_TESTS_DOMAIN', '');
        defined('WP_TESTS_EMAIL') || define('WP_TESTS_EMAIL', '');
        defined('WP_TESTS_TITLE') || define('WP_TESTS_TITLE', '');
        defined('WP_PHP_BINARY') || define('WP_PHP_BINARY', '');

        // Path to the wp-phpunit includes directory.
        if (is_dir($_tests_dir = BASE_PATH . '/vendor/wp-phpunit/wp-phpunit')) {
            // Load the test functions.
            require_once $_tests_dir . '/includes/functions.php';

            // 'WP_TESTS_SKIP_INSTALL';

            // Start up the WP testing environment.
            require $_tests_dir . '/includes/bootstrap.php';
        }
    }
}
