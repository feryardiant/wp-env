<?php

declare(strict_types=1);

namespace Fixtures;

use Brain\Monkey;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

/**
 * Base Test Case for all unit tests.
 */
abstract class TestCase extends PHPUnitTestCase
{
    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();
    }

    /**
     * Tear down the test environment.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }

    /**
     * Gets the path to a file within the packages directory.
     *
     * @param string $file_path The relative path to the file.
     *
     * @return string The absolute path to the file.
     */
    protected static function packageFile(string $file_path): string
    {
        return BASE_PATH . '/packages/' . $file_path;
    }
}
