<?php

declare(strict_types=1);

namespace UnitTests\CustomTheme;

use UnitTests\BaseTestCase;
use Brain\Monkey\Actions;

/**
 * Unit tests for the custom theme's functions.php.
 */
class FunctionsTest extends BaseTestCase
{
    /**
     * Verifies that the 'ct_activation' action is fired when the 'after_switch_theme' hook is triggered.
     *
     * @return void
     */
    public function testCtActivationTriggeredOnAfterSwitchTheme()
    {
        // 1. Verify action is added
        Actions\expectAdded('after_switch_theme')
            ->once()
            ->whenHappen(function ($callback) {
                // 2. Expect ct_activation to be called when the callback runs
                Actions\expectDone('ct_activation')->once();

                // 3. Execute the callback
                $callback();

                $this->addToAssertionCount(2); // add_action and do_action
            });

        // Load the file to trigger add_action calls
        require $this->packageFile('custom-theme/functions.php');
    }

    /**
     * Verifies that the 'ct_deactivation' action is fired when the 'switch_theme' hook is triggered.
     *
     * @return void
     */
    public function testCtDeactivationTriggeredOnSwitchTheme()
    {
        // 1. Verify action is added
        Actions\expectAdded('switch_theme')
            ->once()
            ->whenHappen(function ($callback) {
                // 2. Expect ct_deactivation to be called when the callback runs
                Actions\expectDone('ct_deactivation')->once();

                // 3. Execute the callback
                $callback();

                $this->addToAssertionCount(2); // add_action and do_action
            });

        // Load the file (it will be loaded again but functions.php doesn't have class/function re-declarations)
        // Actually require_once will skip it if already loaded, but it's fine for this test if we run them together.
        // For proper isolation, we'd use separate test methods and ensure the file is loaded.
        // Since it's all top-level add_action calls, they run on load.
        require $this->packageFile('custom-theme/functions.php');
    }
}
