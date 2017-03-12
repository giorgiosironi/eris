<?php
require __DIR__ . '/../vendor/autoload.php';
if (!class_exists('PHPUnit_Framework_TestCase', true)) {
    class PHPUnit_Framework_TestCase extends PHPUnit\Framework\TestCase
    {
    }
    class PHPUnit_Framework_AssertionFailedError extends PHPUnit\Framework\AssertionFailedError
    {
    }
}
