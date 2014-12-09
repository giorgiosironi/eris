<?php
use Eris\Generator;
use Eris\TestTrait;

function concatenation($first, $second)
{
    if (strlen($second) > 5) {
        $second .= 'ERROR';
    }
    return $first . $second;
}

class StringTest extends PHPUnit_Framework_TestCase
{
    use TestTrait;

    public function testRightIdentityElement()
    {
        $this->forAll([
            Generator\string(1000),
        ])
            ->__invoke(function($string) {
                $this->assertEquals(
                    $string,
                    concatenation($string, ''),
                    "Concatenating $string to ''"
                );
            });
    }

    public function testLengthPreservation()
    {
        $this->forAll([
            Generator\string(1000),
            Generator\string(1000),
        ])
            ->__invoke(function($first, $second) {
                $result = concatenation($first, $second);
                $this->assertEquals(
                    strlen($first) + strlen($second),
                    strlen($result),
                    "Concatenating $first to $second gives $result"
                );
            });
    }
}
