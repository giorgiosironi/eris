<?php
use Eris\Generator;

function very_slow_concatenation($first, $second)
{
    if (strlen($second) > 10) {
        sleep(2);
        $second .= 'ERROR';
    }
    return $first . $second;
}

class ShrinkingTimeLimitTest extends PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testLengthPreservation()
    {
        $this
            ->shrinkingTimeLimit(2)
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
