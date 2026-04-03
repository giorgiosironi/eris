<?php

namespace Eris\Arbitrary\Fixtures;

use Eris\Arbitrary\Generate;

#[Generate]
class SimpleValue
{
    public string $name;
    public int $count;
}
