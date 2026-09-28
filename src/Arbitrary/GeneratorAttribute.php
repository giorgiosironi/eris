<?php

namespace Eris\Arbitrary;

use Eris\Generator;

interface GeneratorAttribute
{
    public function toGenerator(): Generator;
}
