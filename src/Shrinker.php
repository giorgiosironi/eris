<?php
namespace Eris;

use Eris\Generator\GeneratedValue;
use Eris\Generator\TupleGenerator;
use Eris\Shrinker\TimeLimit;
use PHPUnit_Framework_AssertionFailedError as AssertionFailed;

/**
 * @private  this interface is not stable yet and may change in the future
 */
interface Shrinker
{
    /**
     * Use its assertion to rethrow the minimal assertion failure derived
     * from shrinking $elements.
     * $elements contains an array of GeneratedValue objects corresponding
     * to the elements that lead to the original failure of the assertion.
     * @throws AssertionFailed
     */
    public function from(GeneratedValue $elements, AssertionFailed $exception);

    /**
     * Configuration: allows specifying a time limit that should stop
     * shrinking if reached and return the best minimimization
     * found so far.
     * @return $this
     */
    public function setTimeLimit(TimeLimit $timeLimit);

    /**
     * Add a condition that must be respected (e.g. a `when()`)
     * from the shrunk elements. Elements not satisfying `$condition`
     * will be discarded.
     *
     * `$condition` takes a number of arguments equal to the cardinality
     * of `$elements`, and accepts the unboxed values. Returns a boolean.
     *
     * @return $this
     */
    public function addGoodShrinkCondition(callable $condition);

    /**
     * Adds callables that will be passed all the attempt to shrink $elements.
     * Data structure is, in fact, the same as `$elements`.
     * 
     * @return $this
     */
    public function onAttempt(callable $listener);
}
