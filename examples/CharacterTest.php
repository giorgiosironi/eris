<?php
use Eris\Generator;
use Eris\Antecedent;

class CharacterTest extends PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testLengthOfAsciiCharactersInPhp()
    {
        $this->forAll(
            Generator\char(['basic-latin'])
        )
            ->then(function ($char) {
                $this->assertLenghtIs1($char);
            });
    }

    public function testLengthOfPrintableAsciiCharacters()
    {
        $this->forAll(
            Generator\char(['basic-latin'])
        )
            ->when(Antecedent\printableCharacter())
            ->then(function ($char) {
                $this->assertFalse(ord($char) < 32);
            });
    }

    /**
     * @eris-ratio 10
     */
    public function testMultiplePrintableCharacters()
    {
        $this
            ->forAll(
                Generator\char(['basic-latin']),
                Generator\char(['basic-latin'])
            )
            ->when(Antecedent\printableCharacters())
            ->then(function ($first, $second) {
                $this->assertFalse(ord($first) < 32);
                $this->assertFalse(ord($second) < 32);
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
