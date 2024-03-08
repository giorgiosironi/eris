<?php

namespace Eris\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS|Attribute::TARGET_METHOD)]
class ErisMethod implements ErisAttribute
{
    public function __construct(
        public string $method
    ) {
    }

    public function getValue()
    {
        return $this->method;
    }
}
