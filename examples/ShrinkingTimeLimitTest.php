<?php
use Eris\Generator;

function very_slow_concatenation($first, $second)
{
    if (strlen($second) > 5) {
        $second .= 'ERROR';
    }
    sleep(1);
    return $first . $second;
}

class ShrinkingTimeLimitTest extends PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function setUp()
    {
        $this->shrinkingTimeLimit = 2;
    }

    public function testLengthPreservation()
    {
        $this->forAll([
            Generator\string(1000),
            Generator\string(1000),
        ])
            ->then(function($first, $second) {
                $result = very_slow_concatenation($first, $second);
                $this->assertEquals(
                    strlen($first) + strlen($second),
                    strlen($result),
                    "Concatenating '$first' to '$second' gives '$result'" . PHP_EOL
                );
            });
    }
}
