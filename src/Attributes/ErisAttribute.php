<?php

namespace Eris\Attributes;

interface ErisAttribute
{
    /**
     * @return int|string
     */
    public function getValue();
}
