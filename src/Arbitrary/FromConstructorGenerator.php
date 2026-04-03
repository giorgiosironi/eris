<?php

namespace Eris\Arbitrary;

use Eris\Generator;
use Eris\Generator\GeneratedValue;
use Eris\Generator\TupleGenerator;
use Eris\Random\RandomRange;
use InvalidArgumentException;
use ReflectionClass;

class FromConstructorGenerator implements Generator
{
    private TupleGenerator $tupleGenerator;
    private ReflectionClass $reflectionClass;

    /**
     * @param class-string $className
     * @param array<string, Generator> $overrides
     */
    public function __construct(string $className, array $overrides = [], ?TypeResolver $resolver = null)
    {
        $resolver ??= new TypeResolver();
        $this->reflectionClass = new ReflectionClass($className);

        $constructor = $this->reflectionClass->getConstructor();
        if ($constructor === null) {
            throw new InvalidArgumentException("Class '{$className}' has no constructor");
        }

        $generators = [];
        foreach ($constructor->getParameters() as $parameter) {
            $name = $parameter->getName();
            if (isset($overrides[$name])) {
                $generators[] = $overrides[$name];
            } else {
                $generators[] = $resolver->resolveParameter($parameter);
            }
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
                return $this->reflectionClass->newInstanceArgs($values);
            },
            'fromConstructor'
        );
    }
}
