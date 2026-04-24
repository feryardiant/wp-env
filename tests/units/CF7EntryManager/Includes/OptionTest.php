<?php

declare(strict_types=1);

namespace UnitTests\CF7EntryManager\Includes;

use CF7_Entry_Manager\Option;
use PHPUnit\Framework\Attributes\CoversClass;
use UnitTests\CF7EntryManager\TestCase;

/**
 * Unit tests for the Option class.
 */
#[CoversClass(Option::class)]
class OptionTest extends TestCase
{
    /**
     * Setup before any test in this class runs.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        require_once static::packageFile('cf7-entry-manager/includes/class-option.php');
    }

    /**
     * Verifies that the Option class is loaded and available.
     *
     * @return void
     */
    public function testDummy()
    {
        $this->assertTrue(class_exists(Option::class));
    }
}
