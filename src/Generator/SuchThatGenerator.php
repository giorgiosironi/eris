<?php
namespace Eris\Generator;

use Eris\Generator;
use Eris\Generators;
use Eris\Random\RandomRange;
use LogicException;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\ExpectationFailedException;
use Traversable;

/**
 * @param callable|Constraint $filter
 * @return SuchThatGenerator
 */
function filter($filter, Generator $generator, $maximumAttempts = 100)
{
    return Generators::suchThat($filter, $generator, $maximumAttempts);
}

/**
 * @param callable|Constraint $filter
 * @return SuchThatGenerator
 */
function suchThat($filter, Generator $generator, $maximumAttempts = 100)
{
    return Generators::suchThat($filter, $generator, $maximumAttempts);
}

class SuchThatGenerator implements Generator
{
    private $filter;
    private $generator;
    private $maximumAttempts;
    
    /**
     * @param callable|Constraint
     */
    public function __construct($filter, $generator, $maximumAttempts = 100)
    {
        $this->filter = $filter;
        $this->generator = $generator;
        $this->maximumAttempts = $maximumAttempts;
    }

    public function __invoke($size, RandomRange $rand)
    {
        $value = $this->generator->__invoke($size, $rand);
        $attempts = 0;
        while (!$this->predicate($value)) {
            if ($attempts >= $this->maximumAttempts) {
                throw new SkipValueException("Tried to satisfy predicate $attempts times, but could not generate a good value. You should try to improve your generator to make it more likely to output good values, or to use a less restrictive condition. Last generated value was: " . $value);
            }
            $value = $this->generator->__invoke($size, $rand);
            $attempts++;
        }
        return $value;
    }

    public function shrink(GeneratedValue $value)
    {
        $shrunk = $this->generator->shrink($value);
        $attempts = 0;
        while (!($filtered = $this->filterForPredicate($shrunk))) {
            if ($attempts >= $this->maximumAttempts) {
                return $value;
            }
            $shrunk = $this->generator->shrink($shrunk);
            $attempts++;
        }
        return new GeneratedValueOptions($filtered);
    }

    /**
     * @return array  of GeneratedValueSingle
     */
    private function filterForPredicate(Traversable $options)
    {
        $goodOnes = [];
        foreach ($options as $option) {
            if ($this->predicate($option)) {
                $goodOnes[] = $option;
            }
        }
        return $goodOnes;
    }

    private function predicate(GeneratedValueSingle $value)
    {
        if ($this->filter instanceof Constraint) {
            try {
                $this->filter->evaluate($value->unbox());
                return true;
            } catch (ExpectationFailedException $e) {
                return false;
            }
        }

        if (is_callable($this->filter)) {
            return call_user_func($this->filter, $value->unbox());
        }

        throw new LogicException("Specified filter does not seem to be of the correct type. Please pass a callable or a PHPUnit\Framework\Constraint instead of " . var_export($this->filter, true));
    }
}
