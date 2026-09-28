<?php
namespace Eris\Generator;

use InvalidArgumentException;
use ArrayIterator;

/**
 * Parametric with respect to the type <T> of its value.
 * Immutable object, modifiers return a new GeneratedValueSingle instance.
 *
 * @template-covariant T
 * @template-implements GeneratedValue<T>
 */
final class GeneratedValueSingle implements GeneratedValue // TODO? interface ShrunkValue extends IteratorAggregate[, Countable]
{
    /** @var T */
    private $value;
    private $input;
    private $generatorName;
    /**
     * @var array
     */
    private $annotations;

    /**
     * A value and the input that was used to derive it.
     * The input usually comes from another Generator.
     *
     * @template TValue
     * @param TValue $value
     * @param GeneratedValueSingle|mixed $input
     * @param string $generatorName  'tuple'
     * @return GeneratedValueSingle<TValue>
     */
    public static function fromValueAndInput($value, $input, $generatorName = null)
    {
        return new self($value, $input, $generatorName);
    }

    /**
     * Input will be copied from value.
     *
     * @template TValue
     * @param TValue $value
     * @param string $generatorName  'tuple'
     * @return GeneratedValueSingle<TValue>
     */
    public static function fromJustValue($value, $generatorName = null)
    {
        return new self($value, $value, $generatorName);
    }

    /**
     * @param T $value
     */
    private function __construct($value, $input, $generatorName, array $annotations = [])
    {
        if ($value instanceof self) {
            throw new InvalidArgumentException("It looks like you are trying to build a GeneratedValueSingle whose value is another GeneratedValueSingle. This is almost always an error as values will be passed as-is to properties and GeneratedValueSingle should be hidden from them.");
        }
        $this->value = $value;
        $this->input = $input;
        $this->generatorName = $generatorName;
        $this->annotations = $annotations;
    }

    /**
     * @return GeneratedValueSingle|mixed
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

    public function __toString(): string
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
     * Produces a new GeneratedValueSingle that wraps this one,
     * and that is labelled with $generatorName.
     * $applyToValue is mapped over the value
     * to build the outer GeneratedValueSingle object $this->value field.
     *
     * T in a parameter of the callable is a covariant position,
     * which Psalm does not recognise (PHPStan does).
     *
     * @template U
     * @param callable(T): U $applyToValue
     * @return GeneratedValueSingle<U>
     * @psalm-suppress InvalidTemplateParam
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
     * Basically changes the name of the Generator,
     * but without introducing an additional layer
     * of wrapping of GeneratedValueSingle objects.
     *
     * @param string $generatorName  'tuple', 'vector'
     * @return GeneratedValueSingle<T>
     */
    public function derivedIn($generatorName)
    {
        return $this->map(
            function ($value) {
                return $value;
            },
            $generatorName
        );
    }

    /**
     * @return \Traversable<int, GeneratedValueSingle<T>>
     */
    public function getIterator(): \Traversable
    {
        return new ArrayIterator([
            $this
        ]);
    }

    public function count(): int
    {
        return 1;
    }

    /**
     * @template U
     * @param GeneratedValueSingle<mixed> $another
     * @param callable(mixed, mixed): U $merge applied to the values and to the inputs
     * @return GeneratedValueSingle<U>
     */
    public function merge(GeneratedValueSingle $another, callable $merge)
    {
        if ($another->generatorName !== $this->generatorName) {
            throw new InvalidArgumentException("Trying to merge a {$this->generatorName} GeneratedValueSingle with a {$another->generatorName} GeneratedValueSingle");
        }
        return self::fromValueAndInput(
            $merge($this->unbox(), $another->unbox()),
            $merge($this->input(), $another->input()),
            $this->generatorName
        );
    }

    /**
     * @template U
     * @param GeneratedValueSingle<U> $value
     * @return GeneratedValueOptions<T|U>
     */
    public function add(GeneratedValueSingle $value)
    {
        return new GeneratedValueOptions([
            $this,
            $value,
        ]);
    }
}
