<?php
namespace Eris\Generator;

use Eris\Random\RandomRange;
use Eris\Random\RandSource;
use PHPUnit\Framework\Attributes\DataProvider;

class RegexGeneratorTest extends \PHPUnit\Framework\TestCase
{
    private int $size;
    private \Eris\Random\RandomRange $rand;

    public static function supportedRegexes(): array
    {
        return [
            // [".{0,100}"] sometimes generates NULL
            ["[a-z0-9]{24}"],
            ["[a-z]{1,5}"],
            ["^[a-z]$"],
            ["a|b|c"],
            ["\d\s\w"],
        ];
    }

    protected function setUp(): void
    {
        $this->size = 10;
        $this->rand = new RandomRange(new RandSource());
    }

    #[DataProvider('supportedRegexes')]
    public function testGeneratesOnlyValuesThatMatchTheRegex(string $expression): void
    {
        $generator = new RegexGenerator($expression);
        for ($i = 0; $i < 100; $i++) {
            $value = $generator($this->size, $this->rand)->unbox();
            self::assertMatchesRegularExpression("/{$expression}/", $value);
        }
    }

    public function testShrinkingIsNotImplementedYet(): void
    {
        $generator = new RegexGenerator(".*");
        $word = GeneratedValueSingle::fromJustValue("something");
        $this->assertEquals($word, $generator->shrink($word));
    }
}
