<?php
namespace Eris\Generator;

use ArrayIterator;
use LogicException;

/**
 * Parametric with respect to the type <T> of its value,
 * which should be the type parameter <T> of all the contained GeneratedValueSingle
 * instances.
 *
 * Mainly used in shrinking, to support multiple options as possibilities
 * for shrinking a GeneratedValueSingle.
 *
 * This class tends to delegate operations to its last() elements for
 * backwards compatibility. So it can be used in context where a single
 * value is expected. The last of the options is usually the more conservative
 * in shrinking, for example subtracting 1 for the IntegerGenerator.
 *
 * @template-covariant T
 * @template-implements GeneratedValue<T>
 */
class GeneratedValueOptions implements GeneratedValue
{
    /**
     * @var array<int, GeneratedValueSingle<T>>
     */
    private $generatedValues;

    /**
     * @param array<int, GeneratedValueSingle<T>> $generatedValues
     */
    public function __construct(array $generatedValues)
    {
        $this->generatedValues = $generatedValues;
    }

    /**
     * @template U
     * @param GeneratedValue<U> $value
     * @return GeneratedValue<U>
     */
    public static function mostPessimisticChoice(GeneratedValue $value)
    {
        if ($value instanceof GeneratedValueOptions) {
            return $value->last();
        }
        return $value;
    }

    /**
     * @return GeneratedValueSingle<T>
     */
    public function first()
    {
        return $this->generatedValues[0];
    }

    /**
     * @return GeneratedValueSingle<T>
     */
    public function last()
    {
        if (count($this->generatedValues) == 0) {
            throw new LogicException("This GeneratedValueOptions is empty");
        }
        return $this->generatedValues[count($this->generatedValues) - 1];
    }

    /**
     * T in a parameter of the callable is a covariant position,
     * which Psalm does not recognise (PHPStan does).
     *
     * @template U
     * @param callable(T): U $callable
     * @param string $generatorName
     * @return GeneratedValueOptions<U>
     * @psalm-suppress InvalidTemplateParam
     */
    public function map(callable $callable, $generatorName)
    {
        return new self(array_map(
            function ($value) use ($callable, $generatorName) {
                return $value->map($callable, $generatorName);
            },
            $this->generatedValues
        ));
    }
    
    public function derivedIn($generatorName)
    {
        throw new \RuntimeException("GeneratedValueOptions::derivedIn() is needed, uncomment it");
    }

    /**
     * @template U
     * @param GeneratedValueSingle<U> $value
     * @return GeneratedValueOptions<T|U>
     */
    public function add(GeneratedValueSingle $value)
    {
        return new self(array_merge(
            $this->generatedValues,
            [$value]
        ));
    }

    /**
     * @param GeneratedValue<mixed> $value
     * @return GeneratedValueOptions<T>
     */
    public function remove(GeneratedValue $value)
    {
        $generatedValues = $this->generatedValues;
        $index = array_search($value, $generatedValues);
        if ($index !== false) {
            unset($generatedValues[$index]);
        }
        return new self(array_values($generatedValues));
    }

    /**
     * @override
     * @return T
     */
    public function unbox()
    {
        return $this->last()->unbox();
    }

    /**
     * @override
     */
    public function input()
    {
        return $this->last()->input();
    }

    /**
     * @override
     */
    public function __toString(): string
    {
        return var_export($this, true);
    }

    /**
     * @override
     * @return string
     */
    public function generatorName()
    {
        return $this->last()->generatorName();
    }

    /**
     * @return \Traversable<int, GeneratedValueSingle<T>>
     */
    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->generatedValues);
    }

    public function count(): int
    {
        return count($this->generatedValues);
    }

    /**
     * @template U
     * @param GeneratedValueOptions<mixed> $generatedValueOptions
     * @param callable(mixed, mixed): U $merge
     * @return GeneratedValueOptions<U>
     */
    public function cartesianProduct($generatedValueOptions, callable $merge)
    {
        $options = [];
        foreach ($this as $firstPart) {
            foreach ($generatedValueOptions as $secondPart) {
                $options[] = $firstPart->merge($secondPart, $merge);
            }
        }
        return new self($options);
    }
}
