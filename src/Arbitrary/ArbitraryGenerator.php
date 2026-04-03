<?php

namespace Eris\Arbitrary;

use Eris\Generator;
use Eris\Generator\GeneratedValue;
use Eris\Generator\TupleGenerator;
use Eris\Random\RandomRange;
use InvalidArgumentException;
use ReflectionClass;

class ArbitraryGenerator implements Generator
{
    private TupleGenerator $tupleGenerator;
    /** @var string[] */
    private array $propertyNames;
    private ReflectionClass $reflectionClass;

    /**
     * @param class-string $className
     * @param array<string, Generator> $overrides
     */
    public function __construct(string $className, array $overrides = [], ?TypeResolver $resolver = null)
    {
        $resolver ??= new TypeResolver();
        $this->reflectionClass = new ReflectionClass($className);

        $hasGenerate = !empty($this->reflectionClass->getAttributes(Generate::class));
        $generators = [];
        $this->propertyNames = [];

        foreach ($this->reflectionClass->getProperties() as $property) {
            $name = $property->getName();

            if (isset($overrides[$name])) {
                $generators[] = $overrides[$name];
                $this->propertyNames[] = $name;
                continue;
            }

            if ($hasGenerate) {
                $generators[] = $resolver->resolveProperty($property);
                $this->propertyNames[] = $name;
                continue;
            }

            // Without #[Generate], only include properties with explicit GeneratorAttribute
            $attrs = $property->getAttributes();
            foreach ($attrs as $attr) {
                $instance = $attr->newInstance();
                if ($instance instanceof GeneratorAttribute) {
                    $generators[] = $instance->toGenerator();
                    $this->propertyNames[] = $name;
                    break;
                }
            }
        }

        if (empty($generators)) {
            throw new InvalidArgumentException(
                "No generators found for class '{$className}'. Add #[Generate] to the class or generator attributes to properties."
            );
        }

        $this->tupleGenerator = new TupleGenerator($generators);
    }

    public function __invoke($size, RandomRange $rand)
    {
        $tuple = $this->tupleGenerator->__invoke($size, $rand);
        return $this->mapToObject($tuple);
    }

    public function shrink(GeneratedValue $element)
    {
        $input = $element->input();
        $shrunkInput = $this->tupleGenerator->shrink($input);
        return $this->mapToObject($shrunkInput);
    }

    private function mapToObject(GeneratedValue $tuple): GeneratedValue
    {
        return $tuple->map(
            function ($values) {
                $object = $this->reflectionClass->newInstanceWithoutConstructor();
                foreach ($this->propertyNames as $i => $name) {
                    $property = $this->reflectionClass->getProperty($name);
                    $property->setValue($object, $values[$i]);
                }
                return $object;
            },
            'arbitrary'
        );
    }
}
