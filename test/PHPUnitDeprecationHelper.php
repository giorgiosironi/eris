<?php

namespace Eris;

use PHPUnit\Framework\TestCase;

final class PHPUnitDeprecationHelper
{
    public static function assertIsString($actual, string $message = ''): void
    {
        if (method_exists(TestCase::class, 'assertIsString')) {
            TestCase::assertIsString($actual, $message);
        } elseif (method_exists(TestCase::class, 'assertInternalType')) {
            TestCase::assertInternalType('string', $actual, $message);
        } else {
            TestCase::fail('Unable to find the assertion method');
        }
    }

    public static function assertIsInt($actual, string $message = ''): void
    {
        if (method_exists(TestCase::class, 'assertIsInt')) {
            TestCase::assertIsInt($actual, $message);
        } elseif (method_exists(TestCase::class, 'assertInternalType')) {
            TestCase::assertInternalType('int', $actual, $message);
        } else {
            TestCase::fail('Unable to find the assertion method');
        }
    }

    public static function assertIsArray($actual, string $message = ''): void
    {
        if (method_exists(TestCase::class, 'assertIsArray')) {
            TestCase::assertIsArray($actual, $message);
        } elseif (method_exists(TestCase::class, 'assertInternalType')) {
            TestCase::assertInternalType('array', $actual, $message);
        } else {
            TestCase::fail('Unable to find the assertion method');
        }
    }

    public static function assertIsBool($actual, string $message = ''): void
    {
        if (method_exists(TestCase::class, 'assertIsBool')) {
            TestCase::assertIsBool($actual, $message);
        } elseif (method_exists(TestCase::class, 'assertInternalType')) {
            TestCase::assertInternalType('bool', $actual, $message);
        } else {
            TestCase::fail('Unable to find the assertion method');
        }
    }

    public static function assertIsFloat($actual, string $message = ''): void
    {
        if (method_exists(TestCase::class, 'assertIsFloat')) {
            TestCase::assertIsFloat($actual, $message);
        } elseif (method_exists(TestCase::class, 'assertInternalType')) {
            TestCase::assertInternalType('float', $actual, $message);
        } else {
            TestCase::fail('Unable to find the assertion method');
        }
    }

    public static function assertMatchesRegularExpression(string $pattern, string $string, string $message = ''): void
    {
        if (method_exists(TestCase::class, 'assertMatchesRegularExpression')) {
            TestCase::assertMatchesRegularExpression($pattern, $string, $message);
        } elseif (method_exists(TestCase::class, 'assertInternalType')) {
            TestCase::assertRegExp($pattern, $string, $message);
        } else {
            TestCase::fail('Unable to find the assertion method');
        }
    }

    public static function assertStringNotContainsString(string $needle, string $haystack, string $message = ''): void
    {
        if (method_exists(TestCase::class, 'assertStringNotContainsString')) {
            TestCase::assertStringNotContainsString($needle, $haystack, $message);
        } elseif (method_exists(TestCase::class, 'assertNotContains')) {
            TestCase::assertNotContains($needle, $haystack, $message);
        } else {
            TestCase::fail('Unable to find the assertion method');
        }
    }
}
