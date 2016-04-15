<?php
namespace Eris\Antecedent;

use Eris\Antecedent;

class SingleCallbackAntecedent implements Antecedent
{
    private $callback;

    public static function from($callback)
    {
        return new self($callback);
    }

    private function __construct($callback)
    {
        $this->callback = $callback;
    }

    public function evaluate(array $values)
    {
        return call_user_func_array($this->callback, $values);
    }
}
