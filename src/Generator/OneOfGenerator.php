<?php
namespace Eris\Generator;

use Eris\Generator;
use Eris\Generators;
use Eris\Random\RandomRange;

/**
 * @template T
 * @param Generator<T>|T ...$generators
 * @return OneOfGenerator<T>
 */
function oneOf(...$generators)
{
    return Generators::oneOf(...$generators);
}

/**
 * @template-covariant T
 * @template-implements Generator<T>
 */
class OneOfGenerator implements Generator
{
    private $generator;

    /**
     * @param array<Generator<T>|T> $generators
     */
    public function __construct($generators)
    {
        $this->generator = new FrequencyGenerator($this->allWithSameFrequency($generators));
    }

    public function __invoke($size, RandomRange $rand)
    {
        return $this->generator->__invoke($size, $rand);
    }

    public function shrink(GeneratedValue $element)
    {
        return $this->generator->shrink($element);
    }

    private function allWithSameFrequency($generators)
    {
        return array_map(
            function ($generator) {
                return [1, $generator];
            },
            $generators
        );
    }
}
