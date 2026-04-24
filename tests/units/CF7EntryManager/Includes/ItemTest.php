<?php

declare(strict_types=1);

namespace UnitTests\CF7EntryManager\Includes;

use CF7_Entry_Manager\Item;
use PHPUnit\Framework\Attributes\CoversClass;
use UnitTests\CF7EntryManager\TestCase;

/**
 * Unit tests for the Item class.
 */
#[CoversClass(Item::class)]
class ItemTest extends TestCase
{
    /**
     * Setup before any test in this class runs.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        require_once static::packageFile('cf7-entry-manager/includes/class-item.php');
    }

    /**
     * Verifies that the Item class is loaded and available.
     *
     * @return void
     */
    public function testDummy()
    {
        $this->assertTrue(class_exists(Item::class));
    }
}
