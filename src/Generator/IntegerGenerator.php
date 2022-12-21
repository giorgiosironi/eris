<?php
namespace Eris\Generator;

use Eris\Generator;
use Eris\Generators;
use Eris\Random\RandomRange;

/**
 * Generates a positive or negative integer (with absolute value bounded by
 * the generation size).
 */
function int()
{
    return Generators::int();
}

/**
 * Generates a positive integer (bounded by the generation size).
 */
function pos()
{
    return Generators::pos();
}

function nat()
{
    return Generators::nat();
}

/**
 * Generates a negative integer (bounded by the generation size).
 */
function neg()
{
    return Generators::neg();
}

function byte()
{
    return Generators::byte();
}

/**
 * @template-implements Generator<int>
 */
class IntegerGenerator implements Generator
{
    private $mapFn;

    public function __construct(callable $mapFn = null)
    {
        if (is_null($mapFn)) {
            $this->mapFn = $this->identity();
        } else {
            $this->mapFn = $mapFn;
        }
    }

    public function __invoke($size, RandomRange $rand)
    {
        $value = $rand->rand(0, $size);
        $mapFn = $this->mapFn;

        $result = $rand->rand(0, 1) === 0
                          ? $mapFn($value)
                          : $mapFn($value * (-1));
        return GeneratedValueSingle::fromJustValue(
            $result,
            'integer'
        );
    }

    public function shrink(GeneratedValue $element)
    {
        $mapFn = $this->mapFn;
        $element = $element->input();

        if ($element > 0) {
            $options = [];
            $nextHalf = $element;
            while (($nextHalf = (int) floor($nextHalf / 2)) > 0) {
                $options[] = GeneratedValueSingle::fromJustValue(
                    $mapFn($element - $nextHalf),
                    'integer'
                );
            }
            $options = array_unique($options, SORT_REGULAR);
            if ($options) {
                return new GeneratedValueOptions($options);
            } else {
                return GeneratedValueSingle::fromJustValue($mapFn($element - 1), 'integer');
            }
        }
        if ($element < 0) {
            // TODO: shrink with options also negative values
            return GeneratedValueSingle::fromJustValue($mapFn($element + 1), 'integer');
        }

        return GeneratedValueSingle::fromJustValue($element, 'integer');
    }

    private function identity()
    {
        return function ($n) {
            return $n;
        };
    }
}
