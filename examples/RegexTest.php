<?php

use Eris\Generators;

class RegexTest extends \PHPUnit\Framework\TestCase
{
    use Eris\TestTrait;

    /**
     * Note that * and + modifiers are not supported. @see Generators::regex()
     */
    public function testStringsMatchingAParticularRegex()
    {
        $this->forAll(
            Generators::regex("[a-z]{10}")
        )
            ->then(function ($string) {
                $this->assertEquals(10, strlen($string));
            });
    }
}
