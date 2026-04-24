<?php

/**
 * PHPUnit Bootstrap
 */

defined('BASE_PATH') || define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/vendor/autoload.php';

// Mock WordPress constants if needed
defined('ABSPATH') || define('ABSPATH', BASE_PATH . '/docker/volumes/wordpress/');

// Any other test initialization can go here.
