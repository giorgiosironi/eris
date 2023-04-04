<?php

namespace Eris\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS|Attribute::TARGET_METHOD)]
class ErisRepeat implements ErisAttribute
{
    public function __construct(
        public int $repeat
    ){}

    public function getValue()
    {
        return $this->repeat;
    }
}