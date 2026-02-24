<?php

namespace Eris\Arbitrary;

use Eris\Generator;
use Eris\Generator\BooleanGenerator;
use Eris\Generator\ConstantGenerator;
use Eris\Generator\DateGenerator;
use Eris\Generator\ElementsGenerator;
use Eris\Generator\FloatGenerator;
use Eris\Generator\FrequencyGenerator;
use Eris\Generator\IntegerGenerator;
use Eris\Generator\MapGenerator;
use Eris\Generator\StringGenerator;
use InvalidArgumentException;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;

class TypeResolver
{
    private int $maxDepth;
    private array $resolving;

    public function __construct(int $maxDepth = 3, array $resolving = [])
    {
        $this->maxDepth = $maxDepth;
        $this->resolving = $resolving;
    }

    public function resolveProperty(ReflectionProperty $property): Generator
    {
        $generator = $this->fromAttribute($property->getAttributes());
        if ($generator !== null) {
            return $this->wrapNullable($property->getType(), $property->getAttributes(), $generator);
        }

        return $this->resolveFromType($property->getType(), $property->getAttributes(), $property->getName());
    }

    public function resolveParameter(ReflectionParameter $parameter): Generator
    {
        $generator = $this->fromAttribute($parameter->getAttributes());
        if ($generator !== null) {
            return $this->wrapNullable($parameter->getType(), $parameter->getAttributes(), $generator);
        }

        return $this->resolveFromType($parameter->getType(), $parameter->getAttributes(), $parameter->getName());
    }

    /**
     * @param \ReflectionAttribute[] $attributes
     */
    private function fromAttribute(array $attributes): ?Generator
    {
        foreach ($attributes as $attribute) {
            $instance = $attribute->newInstance();
            if ($instance instanceof GeneratorAttribute) {
                return $instance->toGenerator();
            }
        }

        return null;
    }

    private function resolveFromType(?\ReflectionType $type, array $attributes, string $name): Generator
    {
        if ($type === null) {
            throw new InvalidArgumentException("Cannot resolve generator for untyped property/parameter '{$name}'");
        }

        if (!$type instanceof ReflectionNamedType) {
            throw new InvalidArgumentException("Cannot resolve generator for union/intersection type on '{$name}'");
        }

        $generator = $this->generatorForNamedType($type, $name);

        return $this->wrapNullable($type, $attributes, $generator);
    }

    private function generatorForNamedType(ReflectionNamedType $type, string $name): Generator
    {
        $typeName = $type->getName();

        return match ($typeName) {
            'int' => new IntegerGenerator(),
            'string' => new StringGenerator(),
            'float' => new FloatGenerator(),
            'bool' => new BooleanGenerator(),
            default => $this->resolveClassType($typeName, $name),
        };
    }

    private function resolveClassType(string $typeName, string $name): Generator
    {
        if ($typeName === 'array' || $typeName === 'mixed') {
            throw new InvalidArgumentException("Cannot resolve generator for type '{$typeName}' on '{$name}'");
        }

        if (!class_exists($typeName) && !enum_exists($typeName) && !interface_exists($typeName)) {
            throw new InvalidArgumentException("Cannot resolve generator for type '{$typeName}' on '{$name}'");
        }

        if (enum_exists($typeName)) {
            return ElementsGenerator::fromArray($typeName::cases());
        }

        if ($typeName === \DateTime::class) {
            return new DateGenerator(new \DateTime("@0"), new \DateTime("@" . (pow(2, 31) - 1)));
        }

        if ($typeName === \DateTimeImmutable::class) {
            $dateGenerator = new DateGenerator(new \DateTime("@0"), new \DateTime("@" . (pow(2, 31) - 1)));
            return new MapGenerator(
                fn(\DateTime $dt) => \DateTimeImmutable::createFromMutable($dt),
                $dateGenerator
            );
        }

        $reflection = new \ReflectionClass($typeName);
        if ($reflection->isAbstract() || $reflection->isInterface()) {
            throw new InvalidArgumentException("Cannot resolve generator for abstract class/interface '{$typeName}' on '{$name}'");
        }

        if (in_array($typeName, $this->resolving, true)) {
            throw new InvalidArgumentException("Circular reference detected for '{$typeName}'");
        }

        if (count($this->resolving) >= $this->maxDepth) {
            throw new InvalidArgumentException("Maximum nesting depth ({$this->maxDepth}) exceeded for '{$typeName}'");
        }

        $nestedResolver = new self($this->maxDepth, array_merge($this->resolving, [$typeName]));
        return new ArbitraryGenerator($typeName, [], $nestedResolver);
    }

    /**
     * @param \ReflectionAttribute[] $attributes
     */
    private function wrapNullable(?\ReflectionType $type, array $attributes, Generator $generator): Generator
    {
        if (!$type instanceof ReflectionNamedType || !$type->allowsNull()) {
            return $generator;
        }

        $nullPercentage = 10;
        foreach ($attributes as $attribute) {
            $instance = $attribute->newInstance();
            if ($instance instanceof Nullable) {
                $nullPercentage = $instance->nullPercentage;
                break;
            }
        }

        $nonNullPercentage = 100 - $nullPercentage;

        return new FrequencyGenerator([
            [$nonNullPercentage, $generator],
            [$nullPercentage, ConstantGenerator::box(null)],
        ]);
    }
}
