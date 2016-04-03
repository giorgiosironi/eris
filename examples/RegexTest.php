<?php
use Eris\Generator;

class RegexTest extends \PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    /**
     * Note that * and + modifiers are not supported. @see Generator\regex
     */
    public function testStringsMatchingAParticularRegex()
    {
        $this->forAll(
            Generator\regex("[a-z]{10}")
        )
            ->then(function ($string) {
                $this->assertEquals(10, strlen($string));
            });
    }
}
