<?php

namespace Eris\Arbitrary;

use Eris\Arbitrary\Fixtures\Color;
use Eris\Arbitrary\Fixtures\Priority;
use Eris\Arbitrary\Fixtures\Status;
use Eris\Generator\BooleanGenerator;
use Eris\Generator\DateGenerator;
use Eris\Generator\ElementsGenerator;
use Eris\Generator\MapGenerator;
use Eris\Generator\FloatGenerator;
use Eris\Generator\FrequencyGenerator;
use Eris\Generator\IntegerGenerator;
use Eris\Generator\StringGenerator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionParameter;
use ReflectionProperty;

class TypeResolverTest extends TestCase
{
    private TypeResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new TypeResolver();
    }

    public function testResolvesIntType(): void
    {
        $generator = $this->resolver->resolveProperty($this->propertyOf('int'));
        $this->assertInstanceOf(IntegerGenerator::class, $generator);
    }

    public function testResolvesStringType(): void
    {
        $generator = $this->resolver->resolveProperty($this->propertyOf('string'));
        $this->assertInstanceOf(StringGenerator::class, $generator);
    }

    public function testResolvesFloatType(): void
    {
        $generator = $this->resolver->resolveProperty($this->propertyOf('float'));
        $this->assertInstanceOf(FloatGenerator::class, $generator);
    }

    public function testResolvesBoolType(): void
    {
        $generator = $this->resolver->resolveProperty($this->propertyOf('bool'));
        $this->assertInstanceOf(BooleanGenerator::class, $generator);
    }

    public function testResolvesEnumType(): void
    {
        $generator = $this->resolver->resolveProperty($this->propertyOf('enum'));
        $this->assertInstanceOf(ElementsGenerator::class, $generator);
    }

    public function testResolvesStringBackedEnumType(): void
    {
        $generator = $this->resolver->resolveProperty($this->propertyOf('stringBackedEnum'));
        $this->assertInstanceOf(ElementsGenerator::class, $generator);
    }

    public function testResolvesIntBackedEnumType(): void
    {
        $generator = $this->resolver->resolveProperty($this->propertyOf('intBackedEnum'));
        $this->assertInstanceOf(ElementsGenerator::class, $generator);
    }

    public function testResolvesDateTimeType(): void
    {
        $generator = $this->resolver->resolveProperty($this->propertyOf('dateTime'));
        $this->assertInstanceOf(DateGenerator::class, $generator);
    }

    public function testResolvesDateTimeImmutableType(): void
    {
        $generator = $this->resolver->resolveProperty($this->propertyOf('dateTimeImmutable'));
        $this->assertInstanceOf(MapGenerator::class, $generator);
    }

    public function testResolvesNullableType(): void
    {
        $generator = $this->resolver->resolveProperty($this->propertyOf('nullable'));
        $this->assertInstanceOf(FrequencyGenerator::class, $generator);
    }

    public function testThrowsForUntypedProperty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('untyped');
        $this->resolver->resolveProperty($this->propertyOf('untyped'));
    }

    public function testThrowsForArrayType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('array');
        $this->resolver->resolveProperty($this->propertyOf('array'));
    }

    public function testThrowsForMixedType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('mixed');
        $this->resolver->resolveProperty($this->propertyOf('mixed'));
    }

    public function testThrowsForUnionType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('union/intersection');
        $this->resolver->resolveProperty($this->propertyOf('union'));
    }

    public function testThrowsForAbstractClass(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('abstract class/interface');
        $this->resolver->resolveProperty($this->propertyOf('abstract'));
    }

    public function testThrowsForInterface(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('abstract class/interface');
        $this->resolver->resolveProperty($this->propertyOf('interface'));
    }

    public function testThrowsForCircularReference(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Circular reference');
        $resolver = new TypeResolver(3, [TypeResolverCircularFixture::class]);
        $resolver->resolveProperty(
            new ReflectionProperty(TypeResolverCircularFixture::class, 'self')
        );
    }

    public function testThrowsForMaxDepthExceeded(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Maximum nesting depth');
        // Depth 1: resolving TypeResolverDepthAFixture uses slot 0,
        // then resolving its nested TypeResolverDepthBFixture tries to use slot 1 which exceeds max
        $resolver = new TypeResolver(1);
        $resolver->resolveProperty(
            new ReflectionProperty(TypeResolverDepthAFixture::class, 'nested')
        );
    }

    public function testExplicitAttributeOverridesTypeInference(): void
    {
        $generator = $this->resolver->resolveProperty($this->propertyOf('attributed'));
        $this->assertInstanceOf(\Eris\Generator\ChooseGenerator::class, $generator);
    }

    public function testNullableWithExplicitAttribute(): void
    {
        // A nullable property with a GeneratorAttribute should still be wrapped in FrequencyGenerator
        $generator = $this->resolver->resolveProperty($this->propertyOf('nullableAttributed'));
        $this->assertInstanceOf(FrequencyGenerator::class, $generator);
    }

    public function testResolvesParameter(): void
    {
        $param = new ReflectionParameter([TypeResolverParamFixture::class, '__construct'], 'name');
        $generator = $this->resolver->resolveParameter($param);
        $this->assertInstanceOf(StringGenerator::class, $generator);
    }

    public function testResolvesParameterWithAttribute(): void
    {
        $param = new ReflectionParameter([TypeResolverParamFixture::class, '__construct'], 'count');
        $generator = $this->resolver->resolveParameter($param);
        $this->assertInstanceOf(\Eris\Generator\ChooseGenerator::class, $generator);
    }

    public function testResolvesNullableParameter(): void
    {
        $param = new ReflectionParameter([TypeResolverParamFixture::class, '__construct'], 'optional');
        $generator = $this->resolver->resolveParameter($param);
        $this->assertInstanceOf(FrequencyGenerator::class, $generator);
    }

    private function propertyOf(string $which): ReflectionProperty
    {
        return new ReflectionProperty(TypeResolverTestFixture::class, $which);
    }
}

class TypeResolverTestFixture
{
    public int $int;
    public string $string;
    public float $float;
    public bool $bool;
    public Color $enum;
    public Status $stringBackedEnum;
    public Priority $intBackedEnum;
    public \DateTime $dateTime;
    public \DateTimeImmutable $dateTimeImmutable;
    public ?string $nullable;
    public $untyped;
    public array $array;
    public mixed $mixed;
    public int|string $union;
    public TypeResolverInterfaceFixture $interface;
    public TypeResolverAbstractFixture $abstract;
    public TypeResolverNestedFixture $nestedClass;

    #[Choose(10, 20)]
    public int $attributed;

    #[Choose(10, 20)]
    public ?int $nullableAttributed;
}

abstract class TypeResolverAbstractFixture
{
}

interface TypeResolverInterfaceFixture
{
}

#[Generate]
class TypeResolverCircularFixture
{
    public TypeResolverCircularFixture $self;
}

#[Generate]
class TypeResolverNestedFixture
{
    public string $value;
}

#[Generate]
class TypeResolverDepthAFixture
{
    public TypeResolverDepthBFixture $nested;
}

#[Generate]
class TypeResolverDepthBFixture
{
    public TypeResolverDepthCFixture $nested;
}

#[Generate]
class TypeResolverDepthCFixture
{
    public string $value;
}

class TypeResolverParamFixture
{
    public function __construct(
        public string $name,
        #[Choose(1, 10)]
        public int $count,
        public ?string $optional,
    ) {
    }
}
