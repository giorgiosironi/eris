<?php

namespace Eris\Arbitrary\Fixtures;

use Eris\Arbitrary\Generate;

#[Generate]
class WithBackedEnums
{
    public Status $status;
    public Priority $priority;
}
