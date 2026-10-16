<?php

namespace Eris\Arbitrary\Fixtures;

use Eris\Arbitrary\ArrayOf;
use Eris\Arbitrary\Generate;

#[Generate]
class WithObjectArray
{
    public string $id;

    #[ArrayOf(SimpleValue::class)]
    public array $items;
}
