<?php

namespace Eris\Arbitrary\Fixtures;

use Eris\Arbitrary\Generate;

#[Generate]
class NestedParent
{
    public string $name;
    public NestedChild $child;
}
