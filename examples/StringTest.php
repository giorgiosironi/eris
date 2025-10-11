<?php

use Eris\Generators;
use Eris\Listeners;

function string_concatenation(string $first, string $second): string
{
    if (strlen($second) > 5) {
        $second .= 'ERROR';
    }
    return $first . $second;
}

class StringTest extends \PHPUnit\Framework\TestCase
{
    use Eris\TestTrait;

    public function testRightIdentityElement(): void
    {
        $this
            ->forAll(
                Generators::string()
            )
            ->then(function ($string): void {
                $this->assertEquals(
                    $string,
                    string_concatenation($string, ''),
                    "Concatenating '$string' to ''"
                );
            });
    }

    public function testLengthPreservation(): void
    {
        $this
            ->forAll(
                Generators::string(),
                Generators::string()
            )
            ->hook(Listeners::log(sys_get_temp_dir().'/eris-string-shrinking.log'))
            ->then(function ($first, $second): void {
                $result = string_concatenation($first, $second);
                $this->assertEquals(
                    strlen($first) + strlen($second),
                    strlen($result),
                    "Concatenating '$first' to '$second' gives '$result'" . PHP_EOL
                    . var_export($first, true) . PHP_EOL
                    . "strlen(): " . strlen($first) . PHP_EOL
                    . var_export($second, true) . PHP_EOL
                    . "strlen(): " . strlen($second) . PHP_EOL
                    . var_export($result, true) . PHP_EOL
                    . "strlen(): " . strlen($result) . PHP_EOL
                    . "First hex: " . var_export(bin2hex($first), true) . PHP_EOL
                    . "Second hex: " . var_export(bin2hex($second), true) . PHP_EOL
                    . "Result hex: " . var_export(bin2hex($result), true) . PHP_EOL
                );
            });
    }
}
