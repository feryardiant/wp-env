<?php

declare(strict_types=1);

namespace UnitTests;

use Brain\Monkey\Functions;
use Fixtures\TestCase;

/**
 * Base Test Case for all unit tests.
 */
abstract class BaseTestCase extends TestCase
{
    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Mock WP functions used in the main file
        Functions\when('__')->returnArg(1);
        Functions\when('_x')->returnArg(1);
        Functions\when('esc_attr')->returnArg(1);
        Functions\when('esc_html')->returnArg(1);
        Functions\when('esc_html__')->returnArg(1);
        Functions\when('esc_html_e')->echoArg(1);

        Functions\when('is_wp_error')->justReturn(false);
        Functions\when('wp_parse_args')->alias(
            fn($a, $b) => array_merge($b, $a)
        );
    }
}
