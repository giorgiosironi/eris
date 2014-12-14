<?php
namespace Eris\Generator;

class RegexTest extends \PHPUnit_Framework_TestCase
{
    public function testGeneratesOnlyValuesThatMatchTheRegex()
    {
        $expression = "/[a-z0-9]{24}/";
        $generator = new Regex($expression);
        for ($i = 0; $i < 100; $i++) {
            $value = $generator();
            $this->assertRegexp($expression, $value);
            // actually not implemented yet:
            $this->assertTrue($generator->contains($value));
        }
    }  

    public function testShrinkingIsNotImplementedYet()
    {
        $generator = new Regex("/.*");
        $this->assertEquals("something", $generator->shrink("something"));
    }
}
