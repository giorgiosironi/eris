<?php

namespace Eris;

use PHPUnit\Framework\TestCase;

final class PHPUnitDeprecationHelper
{
    public static function assertIsString($actual, string $message = null): void
    {
        if (method_exists(TestCase::class, 'assertIsString')) {
            TestCase::assertIsString($actual, $message);
        } else {
            TestCase::assertInternalType('string', $actual, $message);
        }
    }

    public static function assertIsInt($actual, string $message = null): void
    {
        if (method_exists(TestCase::class, 'assertIsInt')) {
            TestCase::assertIsInt($actual, $message);
        } else {
            TestCase::assertInternalType('int', $actual, $message);
        }
    }
}
