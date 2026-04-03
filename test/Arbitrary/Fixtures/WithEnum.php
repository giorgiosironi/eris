<?php

namespace Eris\Arbitrary\Fixtures;

use Eris\Arbitrary\Generate;

#[Generate]
class WithEnum
{
    public Color $color;
    public string $label;
}
