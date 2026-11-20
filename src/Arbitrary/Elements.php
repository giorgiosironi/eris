<?php

namespace Eris\Arbitrary;

use Attribute;
use Eris\Generator;
use Eris\Generator\ElementsGenerator;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class Elements implements GeneratorAttribute
{
    private readonly array $values;

    public function __construct(mixed ...$values)
    {
        $this->values = $values;
    }

    public function toGenerator(): Generator
    {
        return ElementsGenerator::fromArray($this->values);
    }
}
