<?php
use Eris\Generator;
// TODO: maybe Eris\Antecedent?
use Eris\Quantifier\Antecedent as is;

class CharacterTest extends PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testLengthOfAsciiCharactersInPhp()
    {
        $this->forAll([
            Generator\char(['basic-latin']),
        ])
            ->then(function($char) {
                $this->assertLenghtIs1($char);
            });
    }

    public function testLengthOfPrintableAsciiCharacters()
    {
        $this->forAll([
            Generator\char(['basic-latin']),
        ])
            ->when(is\printableCharacter())
            ->then(function($char) {
                $this->assertFalse(ord($char) < 32);
            });
    }

    // TODO: printableCharacter*s*()

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
