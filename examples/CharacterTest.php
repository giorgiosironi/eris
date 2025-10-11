<?php

use Eris\Antecedents;
use Eris\Attributes\ErisRatio;
use Eris\Generators;

class CharacterTest extends \PHPUnit\Framework\TestCase
{
    use Eris\TestTrait;

    public function testLengthOfAsciiCharactersInPhp(): void
    {
        $this->forAll(
            Generators::char()
        )
            ->then(function ($char): void {
                $this->assertLenghtIs1($char);
            });
    }

    public function testLengthOfPrintableAsciiCharacters(): void
    {
        $this->forAll(
            Generators::char()
        )
            ->when(Antecedents::printableCharacter())
            ->then(function ($char): void {
                $this->assertFalse(ord($char) < 32);
            });
    }

    public function testMultiplePrintableCharacters(): void
    {
        $this
            ->minimumEvaluationRatio(0.1)
            ->forAll(
                Generators::char(),
                Generators::char()
            )
            ->when(Antecedents::printableCharacters())
            ->then(function ($first, $second): void {
                $this->assertFalse(ord($first) < 32);
                $this->assertFalse(ord($second) < 32);
            });
    }

    #[ErisRatio(ratio: 10)]
    public function testMultiplePrintableCharactersFromAnnotation(): void
    {
        $this
            ->forAll(
                Generators::char(),
                Generators::char()
            )
            ->when(Antecedents::printableCharacters())
            ->then(function ($first, $second): void {
                $this->assertFalse(ord($first) < 32);
                $this->assertFalse(ord($second) < 32);
            });
    }

    private function assertLenghtIs1($char): void
    {
        $length = strlen((string) $char);
        $this->assertEquals(
            1,
            $length,
            "'$char' is too long: $length"
        );
    }
}
