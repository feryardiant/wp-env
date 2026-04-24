<?php

declare(strict_types=1);

namespace UnitTests\CF7EntryManager;

use Brain\Monkey\Functions;
use UnitTests\BaseTestCase;

/**
 * Base Test Case for CF7 Entry Manager unit tests.
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * Setup before any test in this class runs.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        if (! class_exists('WPCF7_HTMLFormatter')) {
            eval(
                'class WPCF7_HTMLFormatter {
				public const placeholder_block = "pb";
				public const placeholder_inline = "pi";
				public const void_elements = ["br", "hr", "input", "img"];
				public const p_parent_elements = ["div"];
				public const p_nonparent_elements = ["p"];
				public const p_child_elements = ["span"];
				public const br_parent_elements = ["div"];
				public function __construct($opt) {}
				public function append_start_tag($t, $a) {}
				public function append_preformatted($c) {}
				public function end_tag($t) {}
				public function append_comment($c) {}
				public function print() { echo "rendered"; }
			}'
            );
        }
    }

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        Functions\when('wpcf7_kses_allowed_html')->justReturn(array());
    }
}
