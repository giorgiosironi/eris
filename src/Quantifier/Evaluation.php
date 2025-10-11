<?php
namespace Eris\Quantifier;

use Eris\Generator\GeneratedValueSingle;
use PHPUnit\Framework\AssertionFailedError;

/**
 * TODO: change namespace. To what?
 */
final class Evaluation
{
    private $onFailure;
    private $onSuccess;
    private ?\Eris\Generator\GeneratedValueSingle $values = null;

    public static function of($assertion): self
    {
        return new self($assertion);
    }

    private function __construct(private $assertion)
    {
        $this->onFailure = function (): void {
        };
        $this->onSuccess = function (): void {
        };
    }

    public function with(GeneratedValueSingle $values): self
    {
        $this->values = $values;
        return $this;
    }

    public function onFailure(callable $action): self
    {
        $this->onFailure = $action;
        return $this;
    }

    public function onSuccess(callable $action): self
    {
        $this->onSuccess = $action;
        return $this;
    }

    public function execute(): void
    {
        try {
            call_user_func_array(
                $this->assertion,
                $this->values->unbox()
            );
        } catch (AssertionFailedError $e) {
            call_user_func($this->onFailure, $this->values, $e);
            return;
        }
        call_user_func($this->onSuccess, $this->values);
    }
}
