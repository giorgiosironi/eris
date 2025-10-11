<?php
namespace Eris\Generator;

use Eris\Generator;
use Eris\Generators;
use Eris\Random\RandomRange;

/**
 * @return OneOfGenerator
 */
function oneOf(/*$a, $b, ...*/): mixed
{
    return call_user_func_array(
        Generators::oneOf(...),
        func_get_args()
    );
}

/**
 * @psalm-template T
 * @template-implements Generator<Generator<T>>
 */
class OneOfGenerator implements Generator
{
    private $generator;

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

    private function allWithSameFrequency($generators): array
    {
        return array_map(
            fn($generator): array => [1, $generator],
            $generators
        );
    }
}
