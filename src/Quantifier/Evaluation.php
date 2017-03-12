<?php
namespace Eris\Quantifier;

use Eris\Generator\GeneratedValueSingle;
use PHPUnit_Framework_AssertionFailedError;
use PHPUnit\Framework\AssertionFailedError;

/**
 * TODO: change namespace. To what?
 */
final class Evaluation
{
    private $assertion;
    private $onFailure;
    private $onSuccess;
    private $values;

    public static function of($assertion)
    {
        return new self($assertion);
    }

    private function __construct($assertion)
    {
        $this->assertion = $assertion;
        $this->onFailure = function () {
        };
        $this->onSuccess = function () {
        };
    }

    public function with(GeneratedValueSingle $values)
    {
        $this->values = $values;
        return $this;
    }

    public function onFailure(callable $action)
    {
        $this->onFailure = $action;
        return $this;
    }

    public function onSuccess(callable $action)
    {
        $this->onSuccess = $action;
        return $this;
    }

    public function execute()
    {
        try {
            call_user_func_array(
                $this->assertion,
                $this->values->unbox()
            );
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            call_user_func($this->onFailure, $this->values, $e);
            return;
        } catch (AssertionFailedError $e) {
            call_user_func($this->onFailure, $this->values, $e);
            return;
        }
        call_user_func($this->onSuccess, $this->values);
    }
}
