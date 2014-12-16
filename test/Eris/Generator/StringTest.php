<?php
namespace Eris\Generator;

class StringTest extends \PHPUnit_Framework_TestCase
{
    public function testPicksStringsOfAMaximumLength()
    {
        $generator = new String(10);
        $lengths = [];
        $usedChars = [];
        for ($i = 0; $i < 1000; $i++) {
            $value = $generator();
            $length = strlen($value);
            $this->assertLessThanOrEqual(10, $length);
            $lengths = $this->accumulateLengths($lengths, $length);
            $usedChars = $this->accumulateUsedChars($usedChars, $value);
        }
        $this->assertSame(11, count($lengths));
        // only readable characters
        $this->assertSame(127 - 32, count($usedChars));
    }

    public function testShrinksByChoppingOffChars()
    {
        $generator = new String(10);
        $lastValue = $generator();
        $this->assertSame('abcde', $generator->shrink('abcdef'));
    }

    public function testCannotShrinkTheEmptyString()
    {
        $generator = new String(10);
        $lastValue = $generator();
        $this->assertSame('', $generator->shrink(''));
    }

    public function testContainsIsBasedOnLength()
    {
        // TODO: what exactly contains() is used for?
        // is this implementation precise enough?
        $generator = new String(10);
        $this->assertTrue($generator->contains('abcdefghij'), 'The generator should contain 10-char strings');
        $this->assertFalse($generator->contains('abcdefghijl'), 'The generator should not contain 11-char strings');
    }

    private function accumulateLengths(array $lengths, $length)
    {
        if (!isset($lengths[$length])) {
            $lengths[$length] = 0;
        }
        $lengths[$length]++;
        return $lengths;
    }

    private function accumulateUsedChars(array $usedChars, $value)
    {
        for ($j = 0; $j < strlen($value); $j++) {
            $char = $value{$j};
            if (!isset($usedChars[$char])) {
                $usedChars[$char] = 0;
            }
            $usedChars[$char]++;
        }
        return $usedChars;
    }

    /**
     * @expectedException DomainException
     */
    public function testExceptionWhenTryingToShrinkValuesOutsideOfTheDomain()
    {
        $generator = new String(10);
        $generator->shrink(true);
    }
}
