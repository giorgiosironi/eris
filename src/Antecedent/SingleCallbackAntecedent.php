<?php
namespace Eris\Antecedent;

use Eris\Antecedent;

class SingleCallbackAntecedent implements Antecedent
{
    public static function from($callback): self
    {
        return new self($callback);
    }

    private function __construct(private $callback)
    {
    }

    public function evaluate(array $values): mixed
    {
        return call_user_func_array($this->callback, $values);
    }
}
