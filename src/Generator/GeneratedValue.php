<?php
namespace Eris\Generator;

use InvalidArgumentException;
use IteratorAggregate;
use ArrayIterator;

/**
 * Parametric with respect to the type <T> of its value.
 * Immutable object, modifiers return a new GeneratedValue instance.
 */
/*final*/ class GeneratedValue
    implements IteratorAggregate
    // TODO: interface Options extends IteratorAggregate[, Countable]
{
    private $value;
    private $input;
    private $generatorName;
    
    /**
     * A value and the input that was used to derive it.
     * The input usually comes from another Generator.
     *
     * @param T $value
     * @param GeneratedValue|mixed $input
     * @param string $generatorName  'tuple'
     * @return GeneratedValue
     */
    public static function fromValueAndInput($value, $input, $generatorName = null)
    {
        return new self($value, $input, $generatorName);
    }

    /**
     * Input will be copied from value.
     *
     * @param T $value
     * @param string $generatorName  'tuple'
     * @return GeneratedValue
     */
    public static function fromJustValue($value, $generatorName = null)
    {
        return new self($value, $value, $generatorName);
    }
    
    private function __construct($value, $input, $generatorName, array $annotations = [])
    {
        if ($value instanceof self) {
            throw new InvalidArgumentException("It looks like you are trying to build a GeneratedValue whose value is another GeneratedValue. This is almost always an error as values will be passed as-is to properties and GeneratedValue should be hidden from them.");
        }
        $this->value = $value;
        $this->input = $input;
        $this->generatorName = $generatorName;
        $this->annotations = $annotations;
    }

    /**
     * @return GeneratedValue|mixed
     */
    public function input()
    {
        return $this->input;
    }

    /**
     * @return T
     */
    public function unbox()
    {
        return $this->value;
    }

    public function __toString()
    {
        return var_export($this, true);
    }

    /**
     * @return string
     */
    public function generatorName()
    {
        return $this->generatorName;
    }

    /**
     * @return GeneratedValue
     */
    public function map(callable $applyToValue, $generatorName)
    {
        return new self(
            $applyToValue($this->value),
            $this,
            $generatorName
        );
    }

    /**
     * @param string $generatorName  'tuple', 'vector'
     * @return GeneratedValue
     */
    public function derivedIn($generatorName)
    {
        return $this->map(
            function ($value) { return $value; },
            $generatorName
        );
    }

    public function getIterator()
    {
        return new ArrayIterator([
            $this
        ]);
    }

    public function merge(GeneratedValue $another, callable $merge)
    {
        return self::fromValueAndInput(
            $merge($this->unbox(), $another->unbox()),
            $merge($this->input(), $another->input()),
            // TODO: check $another->generatorName is the same as this
            $this->generatorName
        );
    }
}
