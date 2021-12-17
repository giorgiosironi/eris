<?php

use Eris\Antecedents;
use Eris\Generators;

class CharacterTest extends \PHPUnit\Framework\TestCase
{
    use Eris\TestTrait;

    public function testLengthOfAsciiCharactersInPhp()
    {
        $this->forAll(
            Generators::char(['basic-latin'])
        )
            ->then(function ($char) {
                $this->assertLenghtIs1($char);
            });
    }

    public function testLengthOfPrintableAsciiCharacters()
    {
        $this->forAll(
            Generators::char(['basic-latin'])
        )
            ->when(Antecedents::printableCharacter())
            ->then(function ($char) {
                $this->assertFalse(ord($char) < 32);
            });
    }

    public function testMultiplePrintableCharacters()
    {
        $this
            ->minimumEvaluationRatio(0.1)
            ->forAll(
                Generators::char(['basic-latin']),
                Generators::char(['basic-latin'])
            )
            ->when(Antecedents::printableCharacters())
            ->then(function ($first, $second) {
                $this->assertFalse(ord($first) < 32);
                $this->assertFalse(ord($second) < 32);
            });
    }

    /**
     * @eris-ratio 10
     */
    public function testMultiplePrintableCharactersFromAnnotation()
    {
        $this
            ->forAll(
                Generators::char(['basic-latin']),
                Generators::char(['basic-latin'])
            )
            ->when(Antecedents::printableCharacters())
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
