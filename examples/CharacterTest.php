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
                $this->assertLenghtIs1($char);
            });
    }

    public function testLengthOfPrintableAsciiCharacters()
    {
        $this->forAll([
            Generator\charPrintableAscii(),
        ])
            ->then(function($char) {
                $this->assertLenghtIs1($char);
            });
    }

    private function assertLenghtIs1($char)
    {
        $length = strlen($char);
        $this->assertEquals(
            1,
            $length,
            "'$char' is too long: $length"
        );
    }
}
