<?php
namespace Eris\Generator;

class StringGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->rand = 'rand';
    }
    
    public function testRandomlyPicksLengthAndCharacters()
    {
        $size = 10;
        $generator = new StringGenerator();
        $lengths = [];
        $usedChars = [];
        for ($i = 0; $i < 1000; $i++) {
            $value = $generator($size, $this->rand)->unbox();
            $length = strlen($value);
            $this->assertLessThanOrEqual(10, $length);
            $lengths = $this->accumulateLengths($lengths, $length);
            $usedChars = $this->accumulateUsedChars($usedChars, $value);
        }
        $this->assertSame(11, count($lengths));
        // only readable characters
        $this->assertEquals(126 - 32, count($usedChars));
    }

    public function testRespectsTheGenerationSize()
    {
        $generationSize = 100;
        $generator = new StringGenerator();
        $value = $generator($generationSize, $this->rand)->unbox();

        $this->assertLessThanOrEqual($generationSize, strlen($value));
    }

    public function testShrinksByChoppingOffChars()
    {
        $generator = new StringGenerator();
        $lastValue = $generator($size = 10, $this->rand);
        $this->assertSame('abcde', $generator->shrink(GeneratedValueSingle::fromJustValue('abcdef'))->unbox());
    }

    public function testCannotShrinkTheEmptyString()
    {
        $generator = new StringGenerator();
        $minimumValue = GeneratedValueSingle::fromJustValue('');
        $this->assertEquals($minimumValue, $generator->shrink($minimumValue));
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
}
