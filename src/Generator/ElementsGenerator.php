<?php
namespace Eris\Generator;

use Eris\Generator;
use Eris\Generators;
use Eris\Random\RandomRange;

function elements(/*$a, $b, ...*/): mixed
{
    return call_user_func_array(
        Generators::elements(...),
        func_get_args()
    );
}

/**
 * @psalm-template T
 * @template-implements Generator<T>
 */
class ElementsGenerator implements Generator
{
    public static function fromArray(array $domain): self
    {
        return new self($domain);
    }

    private function __construct(private $domain)
    {
    }

    public function __invoke($_size, RandomRange $rand)
    {
        $index = $rand->rand(0, count($this->domain) - 1);
        return GeneratedValueSingle::fromJustValue($this->domain[$index], 'elements');
    }

    public function shrink(GeneratedValue $element): GeneratedValue
    {
        return $element;
    }
}
