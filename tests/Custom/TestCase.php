<?php

namespace Behat\Mink\Tests\Driver\Custom;

use PHPUnit\Framework\TestCase as BaseTestCase;
use PHPUnit\Runner\Version;

if (class_exists('PHPUnit\Runner\Version') && version_compare(Version::id(), '6.0.0', '>=')) {

    /**
     * Implementation for compatibility with PHPUnit 6+.
     */
    abstract class TestCase extends BaseTestCase
    {
    }

} elseif (version_compare(\PHPUnit_Runner_Version::id(), '4.0.0', '>=')) {

    /**
     * Implementation for compatibility with PHPUnit 4 and 5.
     */
    abstract class TestCase extends \PHPUnit_Framework_TestCase
    {
    }
}

