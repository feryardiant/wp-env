<?php

declare(strict_types=1);

namespace UnitTests\CF7EntryManager\Includes;

use CF7_Entry_Manager\Page_Element;
use PHPUnit\Framework\Attributes\CoversClass;
use UnitTests\CF7EntryManager\TestCase;

/**
 * Unit tests for the Page_Element class.
 */
#[CoversClass(Page_Element::class)]
class PageElementTest extends TestCase
{
    /**
     * Setup before any test in this class runs.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        require_once static::packageFile('cf7-entry-manager/includes/class-page-element.php');
    }

    /**
     * Verifies that the Page_Element class is loaded and available.
     *
     * @return void
     */
    public function testDummy()
    {
        $this->assertTrue(class_exists(Page_Element::class));
    }
}
