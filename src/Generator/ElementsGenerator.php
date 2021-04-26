<?php
namespace Eris\Generator;

use Eris\Generator;
use Eris\Random\RandomRange;

/**
 * @return ElementsGenerator
 */
function elements(/*$a, $b, ...*/)
{
    return ElementsGenerator::elements(func_get_args());
}

class ElementsGenerator implements Generator
{
    private $domain;

    public static function fromArray(array $domain)
    {
        return new self($domain);
    }

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

    /**
     * @return ElementsGenerator
     */
    public static function elements(/*$a, $b, ...*/)
    {
        $arguments = func_get_args();
        if (count($arguments) == 1) {
            return self::fromArray($arguments[0]);
        } else {
            return self::fromArray($arguments);
        }
    }
}
