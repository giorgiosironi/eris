<?php
namespace Eris\Generator;

use Eris\Generator;
use DomainException;

function elements(/*$a, $b, ...*/)
{
    $arguments = func_get_args();
    if (count($arguments) == 1) {
        return Generator\ElementsGenerator::fromArray($arguments[0]);
    } else {
        return Generator\ElementsGenerator::fromArray($arguments);
    }
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

    public function __invoke($_size)
    {
        $index = rand(0, count($this->domain) - 1);
        return GeneratedValue::fromJustValue($this->domain[$index], 'elements');
    }

    public function shrink(GeneratedValue $element)
    {
        if (!$this->contains($element)) {
            throw new DomainException(
                $element . ' does not belong to the domain with elements ' .
                var_export($this->domain, true)
            );
        }

        return $element;
    }

    public function contains($element)
    {
        return in_array($element->unbox(), $this->domain);
    }
}
