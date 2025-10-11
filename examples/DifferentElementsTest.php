<?php

use Eris\Generators;
use Eris\TestTrait;

class Type
{
    const TYPE_A = 1;
    const TYPE_B = 2;
    const TYPE_C = 3;

    private function __construct(private readonly int $type)
    {
    }

    public static function A(): self
    {
        return new self(self::TYPE_A);
    }

    public static function B(): self
    {
        return new self(self::TYPE_B);
    }

    public static function C(): self
    {
        return new self(self::TYPE_C);
    }
}

class DifferentElementsTest extends \PHPUnit\Framework\TestCase
{
    use TestTrait;

    /**
     * @test
     */
    public function a_type_is_different_than_another_one(): void
    {
        $allTypes = [
            Type::A(),
            Type::B(),
            Type::C(),
        ];
        $remove = (fn ($array, $whatToRemove) => array_values(array_filter(
            $array,
            fn ($candidate): bool => $candidate != $whatToRemove
        )));

        $this
            ->forAll(Generators::bind(
                Generators::elements($allTypes),
                fn ($first): \Eris\Generator\TupleGenerator => Generators::tuple(
                    Generators::constant($first),
                    Generators::elements($remove($allTypes, $first))
                )
            ))
            ->then(function ($differentElements): void {
                $this->assertNotEquals($differentElements[0], $differentElements[1], "Several discussion types are equals");
            });
    }
}
