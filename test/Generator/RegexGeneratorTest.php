<?php
namespace Eris\Generator;

use Eris\Random\RandomRange;
use Eris\Random\RandSource;
use PHPUnit\Framework\Attributes\DataProvider;

class RegexGeneratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var int
     */
    private $size;
    /**
     * @var RandomRange
     */
    private $rand;

    public static function supportedRegexes()
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
    public function testGeneratesOnlyValuesThatMatchTheRegex($expression)
    {
        $generator = new RegexGenerator($expression);
        for ($i = 0; $i < 100; $i++) {
            $value = $generator($this->size, $this->rand)->unbox();
            static::assertMatchesRegularExpression("/{$expression}/", $value);
        }
    }

    /**
     * @see https://github.com/giorgiosironi/eris/issues/193
     */
    #[DataProvider('unboundedQuantifierRegexes')]
    public function testDoesNotCrashOnUnboundedQuantifiers($expression)
    {
        $generator = new RegexGenerator($expression);
        for ($i = 0; $i < 20; $i++) {
            $value = $generator($this->size, $this->rand)->unbox();
            static::assertMatchesRegularExpression("/^(?:{$expression})$/s", (string) $value);
        }
    }

    public function testUnboundedMaxLimitsTheRepetitionsOfStarAndPlus()
    {
        $generator = new RegexGenerator("a+b*", 3);
        for ($i = 0; $i < 50; $i++) {
            $value = $generator($this->size, $this->rand)->unbox();
            static::assertMatchesRegularExpression("/^a{1,3}b{0,3}$/", $value);
        }
    }

    #[DataProvider('randomGenerators')]
    public function testRandomGeneratorIsConfigurable($random)
    {
        $generator = new RegexGenerator("[a-z]{5}", null, $random);
        $value = $generator($this->size, $this->rand)->unbox();
        static::assertMatchesRegularExpression("/^[a-z]{5}$/", $value);
    }

    public static function randomGenerators()
    {
        return [["srand"], ["mersenne"], ["simple"]];
    }

    public function testUnknownRandomGeneratorIsRejected()
    {
        $this->expectException(\InvalidArgumentException::class);
        new RegexGenerator("a", null, "unknown");
    }

    public static function unboundedQuantifierRegexes()
    {
        return [
            ["a*"],
            ["a+"],
            ["[a-z]*"],
            ["[a-z]+"],
            ["\\d+\\.\\d*"],
        ];
    }

    public function testShrinkingIsNotImplementedYet()
    {
        $generator = new RegexGenerator(".*");
        $word = GeneratedValueSingle::fromJustValue("something");
        $this->assertEquals($word, $generator->shrink($word));
    }
}
