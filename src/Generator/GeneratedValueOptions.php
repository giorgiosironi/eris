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
 * @psalm-template T
 * @template-implements GeneratedValue<T>
 */
class GeneratedValueOptions implements GeneratedValue, \Stringable
{
    public function __construct(private array $generatedValues)
    {
    }

    public static function mostPessimisticChoice(GeneratedValue $value)
    {
        if ($value instanceof GeneratedValueOptions) {
            return $value->last();
        }
        return $value;
    }

    public function first()
    {
        return $this->generatedValues[0];
    }

    public function last()
    {
        if (count($this->generatedValues) === 0) {
            throw new LogicException("This GeneratedValueOptions is empty");
        }
        return $this->generatedValues[count($this->generatedValues) - 1];
    }

    public function map(callable $callable, $generatorName): self
    {
        return new self(array_map(
            fn($value) => $value->map($callable, $generatorName),
            $this->generatedValues
        ));
    }
    
    public function derivedIn($generatorName): never
    {
        throw new \RuntimeException("GeneratedValueOptions::derivedIn() is needed, uncomment it");
    }

    public function add(GeneratedValueSingle $value): self
    {
        return new self(array_merge(
            $this->generatedValues,
            [$value]
        ));
    }

    public function remove(GeneratedValue $value): self
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

    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->generatedValues);
    }

    public function count(): int
    {
        return count($this->generatedValues);
    }

    public function cartesianProduct($generatedValueOptions, callable $merge): self
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
