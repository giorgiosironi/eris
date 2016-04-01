<?php
namespace Eris\Generator;

class RegexGeneratorTest extends \PHPUnit_Framework_TestCase
{
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

    public function setUp()
    {
        $this->size = 10;
    }

    /**
     * @dataProvider supportedRegexes
     */
    public function testGeneratesOnlyValuesThatMatchTheRegex($expression)
    {
        $generator = new RegexGenerator($expression);
        for ($i = 0; $i < 100; $i++) {
            $value = $generator($this->size)->unbox();
            $this->assertTrue(
                $generator->contains(GeneratedValue::fromJustValue($value)), 
                "Failed asserting that " . var_export($value, true) . " matches the regexp $expression"
            );
        }
    }

    public function testShrinkingIsNotImplementedYet()
    {
        $generator = new RegexGenerator(".*");
        $word = GeneratedValue::fromJustValue("something");
        $this->assertEquals($word, $generator->shrink($word));
    }
}
