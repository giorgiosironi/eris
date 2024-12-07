<?php

namespace Eris\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS|Attribute::TARGET_METHOD)]
class ErisDuration implements ErisAttribute
{
    public function __construct(
        public string $duration
    ) {
    }


    public function getValue()
    {
        return $this->duration;
    }
}
