<?php

namespace Eris\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS|Attribute::TARGET_METHOD)]
class ErisShrink implements ErisAttribute
{
    public function __construct(
        public int $shrink
    ){}

    public function getValue()
    {
        return $this->shrink;
    }
}