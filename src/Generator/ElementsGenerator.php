<?php
namespace Eris\Generator;

use Eris\Generator;
use Eris\Generators;
use Eris\Random\RandomRange;

/**
 * elements($a, $b, ...) or elements([$a, $b, ...])
 *
 * @phpstan-template TFirst
 * @phpstan-template TMore
 * @param mixed $elementOrElements the array of all elements, or the first of several elements
 * @param mixed ...$moreElements
 * @phpstan-param TFirst $elementOrElements
 * @phpstan-param TMore ...$moreElements
 * @return ElementsGenerator<mixed>
 * @phpstan-return ($moreElements is array{} ? ElementsGenerator<value-of<TFirst>> : ElementsGenerator<TFirst|TMore>)
 */
function elements($elementOrElements, ...$moreElements)
{
    return Generators::elements($elementOrElements, ...$moreElements);
}

/**
 * @template-covariant T
 * @template-implements Generator<T>
 */
class ElementsGenerator implements Generator
{
    private $domain;

    /**
     * @template U
     * @param array<U> $domain
     * @return self<U>
     */
    public static function fromArray(array $domain)
    {
        return new self($domain);
    }

    /**
     * @param array<T> $domain
     */
    private function __construct($domain)
    {
        $this->domain = $domain;
    }

    public function __invoke($_size, RandomRange $rand)
    {
        $index = $rand->rand(0, count($this->domain) - 1);
        return GeneratedValueSingle::fromJustValue($this->domain[$index], 'elements');
    }

    public function shrink(GeneratedValue $element)
    {
        return $element;
    }
}
