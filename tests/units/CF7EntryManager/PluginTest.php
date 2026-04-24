<?php

declare(strict_types=1);

namespace UnitTests\CF7EntryManager;

use UnitTests\BaseTestCase;
use Brain\Monkey\Actions;
use Brain\Monkey\Filters;
use Brain\Monkey\Functions;

/**
 * Unit tests for the CF7 Entry Manager plugin main file.
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class PluginTest extends BaseTestCase
{
    /**
     * Verifies that the plugin correctly defines its constants and registers primary hooks during initialization.
     *
     * @return void
     */
    public function testPluginInitialization()
    {
        // Mock WP functions used in the main file
        Functions\when('register_activation_hook')->justReturn();
        Functions\when('register_deactivation_hook')->justReturn();
        Functions\when('plugin_dir_url')->justReturn('https://example.com/wp-content/plugins/cf7-entry-manager/');
        Functions\when('register_post_type')->justReturn();

        // Set WP version global if not available
        if (! isset($GLOBALS['wp_version'])) {
            $GLOBALS['wp_version'] = getenv('WP_VERSION') ?: '6.9';
        }

        // Expect hooks to be added
        // Actions\expectAdded( 'admin_notices' )->never();
        // Actions\expectAdded( 'admin_enqueue_scripts' )->once();
        // Actions\expectAdded( 'wpcf7_init' )->once();
        Actions\expectAdded('init')
            ->once()
            ->whenHappen(function ($callback) {
                Filters\expectAdded('user_contactmethods')->once();
                $callback();
            });

        // Load the plugin file
        require static::packageFile('cf7-entry-manager/cf7-entry-manager.php');

        // Verify constants
        $this->assertTrue(defined('CF7EM_VERSION'));
        $this->assertEquals('0.1.0', CF7EM_VERSION);
        $this->assertTrue(defined('CF7EM__MINIMUM_WP_VERSION'));
        $this->assertTrue(defined('CF7EM__MINIMUM_PHP_VERSION'));
    }
}
