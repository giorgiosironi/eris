<?php
use Eris\Generator;

class CharacterTest extends PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testLengthOfAsciiCharactersInPhp()
    {
        $this->forAll([
            Generator\charAscii(),
        ])
            ->then(function($char) {
                $length = strlen($char);
                $this->assertEquals(
                    1,
                    $length,
                    "'$char' is too long: $length"
                );
            });
    }
}
