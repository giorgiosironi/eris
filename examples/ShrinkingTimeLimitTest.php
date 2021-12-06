<?php

use Eris\Generator;
use Eris\Generators;

function very_slow_concatenation($first, $second)
{
    if (strlen($second) > 10) {
        sleep(2);
        $second .= 'ERROR';
    }
    return $first . $second;
}

class ShrinkingTimeLimitTest extends \PHPUnit\Framework\TestCase
{
    use Eris\TestTrait;

    public function testLengthPreservation()
    {
        $this
            ->shrinkingTimeLimit(2)
            ->forAll(
                Generators::string(),
                Generators::string()
            )
            ->then(function ($first, $second) {
                $result = very_slow_concatenation($first, $second);
                $this->assertEquals(
                    strlen($first) + strlen($second),
                    strlen($result),
                    "Concatenating '$first' to '$second' gives '$result'" . PHP_EOL
                );
            });
    }

    /**
     * @eris-shrink 2
     */
    public function testLengthPreservationFromAnnotation()
    {
        $this
            ->forAll(
                Generator\string(),
                Generator\string()
            )
            ->then(function ($first, $second) {
                $result = very_slow_concatenation($first, $second);
                $this->assertEquals(
                    strlen($first) + strlen($second),
                    strlen($result),
                    "Concatenating '$first' to '$second' gives '$result'" . PHP_EOL
                );
            });
    }
}
