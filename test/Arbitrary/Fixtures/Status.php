<?php

namespace Eris\Arbitrary\Fixtures;

enum Status: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Pending = 'pending';
}
