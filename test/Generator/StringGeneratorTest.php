<?php
namespace Eris\Generator;

use Eris\Random\RandomRange;
use Eris\Random\RandSource;

class StringGeneratorTest extends \PHPUnit\Framework\TestCase
{
    private \Eris\Random\RandomRange $rand;

    public function setUp(): void
    {
        $this->rand = new RandomRange(new RandSource());
    }
    
    public function testRandomlyPicksLengthAndCharacters(): void
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

    public function testRespectsTheGenerationSize(): void
    {
        $generationSize = 100;
        $generator = new StringGenerator();
        $value = $generator($generationSize, $this->rand)->unbox();

        $this->assertLessThanOrEqual($generationSize, strlen($value));
    }

    public function testShrinksByChoppingOffChars(): void
    {
        $generator = new StringGenerator();
        $generator($size = 10, $this->rand);
        $this->assertSame('abcde', $generator->shrink(GeneratedValueSingle::fromJustValue('abcdef'))->unbox());
    }

    public function testCannotShrinkTheEmptyString(): void
    {
        $generator = new StringGenerator();
        $minimumValue = GeneratedValueSingle::fromJustValue('');
        $this->assertEquals($minimumValue, $generator->shrink($minimumValue));
    }

    private function accumulateLengths(array $lengths, int $length): array
    {
        if (!isset($lengths[$length])) {
            $lengths[$length] = 0;
        }
        $lengths[$length]++;
        return $lengths;
    }

    private function accumulateUsedChars(array $usedChars, $value): array
    {
        for ($j = 0; $j < strlen((string) $value); $j++) {
            $char = $value[$j];
            if (!isset($usedChars[$char])) {
                $usedChars[$char] = 0;
            }
            $usedChars[$char]++;
        }
        return $usedChars;
    }
}
