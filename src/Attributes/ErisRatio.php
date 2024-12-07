<?php

namespace Eris\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS|Attribute::TARGET_METHOD)]
class ErisRatio implements ErisAttribute
{
    public function __construct(
        public int $ratio
    ) {
    }

    public function getValue()
    {
        return $this->ratio;
    }
}
