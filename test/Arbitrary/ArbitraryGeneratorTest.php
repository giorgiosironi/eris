<?php

namespace Eris\Arbitrary;

use Eris\Arbitrary\Fixtures\Color;
use Eris\Arbitrary\Fixtures\LineItem;
use Eris\Arbitrary\Fixtures\Priority;
use Eris\Arbitrary\Fixtures\Status;
use Eris\Arbitrary\Fixtures\NestedChild;
use Eris\Arbitrary\Fixtures\NestedParent;
use Eris\Arbitrary\Fixtures\NoAnnotations;
use Eris\Arbitrary\Fixtures\PartialAnnotation;
use Eris\Arbitrary\Fixtures\SimpleValue;
use Eris\Arbitrary\Fixtures\WithConstantAndElements;
use Eris\Arbitrary\Fixtures\WithDateTime;
use Eris\Arbitrary\Fixtures\WithDateTimeImmutable;
use Eris\Arbitrary\Fixtures\WithBackedEnums;
use Eris\Arbitrary\Fixtures\WithEnum;
use Eris\Arbitrary\Fixtures\WithExplicitTypeAttributes;
use Eris\Arbitrary\Fixtures\WithArrays;
use Eris\Arbitrary\Fixtures\WithNullable;
use Eris\Arbitrary\Fixtures\WithObjectArray;
use Eris\Generator\ChooseGenerator;
use Eris\Generator\GeneratedValueOptions;
use Eris\Generators;
use Eris\Random\RandomRange;
use Eris\Random\RandSource;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ArbitraryGeneratorTest extends TestCase
{
    private int $size;
    private RandomRange $rand;

    protected function setUp(): void
    {
        $this->size = 10;
        $this->rand = new RandomRange(new RandSource());
    }

    public function testGeneratesSimpleValueObject(): void
    {
        $generator = new ArbitraryGenerator(SimpleValue::class);
        $generated = $generator($this->size, $this->rand);

        $value = $generated->unbox();
        $this->assertInstanceOf(SimpleValue::class, $value);
        $this->assertIsString($value->name);
        $this->assertIsInt($value->count);
    }

    public function testGeneratesWithAttributeOverrides(): void
    {
        $generator = new ArbitraryGenerator(LineItem::class);
        $generated = $generator($this->size, $this->rand);

        $value = $generated->unbox();
        $this->assertInstanceOf(LineItem::class, $value);
        $this->assertIsString($value->sku);
        $this->assertGreaterThanOrEqual(1, $value->quantity);
        $this->assertLessThanOrEqual(5, $value->quantity);
    }

    public function testPartialAnnotationOnlyGeneratesAnnotatedProperties(): void
    {
        $generator = new ArbitraryGenerator(PartialAnnotation::class);
        $generated = $generator($this->size, $this->rand);

        $value = $generated->unbox();
        $this->assertInstanceOf(PartialAnnotation::class, $value);
        $this->assertGreaterThanOrEqual(1, $value->annotated);
        $this->assertLessThanOrEqual(100, $value->annotated);
    }

    public function testGeneratesWithNullableProperties(): void
    {
        $generator = new ArbitraryGenerator(WithNullable::class);

        $hasNull = false;
        $hasNonNull = false;
        for ($i = 0; $i < 100; $i++) {
            $generated = $generator($this->size, $this->rand);
            $value = $generated->unbox();
            $this->assertInstanceOf(WithNullable::class, $value);
            $this->assertTrue(is_string($value->maybeName) || $value->maybeName === null);
            $this->assertTrue(is_int($value->maybeCount) || $value->maybeCount === null);
            if ($value->maybeName === null) {
                $hasNull = true;
            } else {
                $hasNonNull = true;
            }
        }
        $this->assertTrue($hasNull, 'Expected at least one null value in 100 iterations');
        $this->assertTrue($hasNonNull, 'Expected at least one non-null value in 100 iterations');
    }

    public function testGeneratesWithEnumProperty(): void
    {
        $generator = new ArbitraryGenerator(WithEnum::class);
        $generated = $generator($this->size, $this->rand);

        $value = $generated->unbox();
        $this->assertInstanceOf(WithEnum::class, $value);
        $this->assertInstanceOf(Color::class, $value->color);
        $this->assertIsString($value->label);
    }

    public function testGeneratesNestedObjects(): void
    {
        $generator = new ArbitraryGenerator(NestedParent::class);
        $generated = $generator($this->size, $this->rand);

        $value = $generated->unbox();
        $this->assertInstanceOf(NestedParent::class, $value);
        $this->assertIsString($value->name);
        $this->assertInstanceOf(NestedChild::class, $value->child);
        $this->assertIsString($value->child->value);
    }

    public function testOverridesPropertyGenerators(): void
    {
        $generator = new ArbitraryGenerator(SimpleValue::class, [
            'count' => new ChooseGenerator(42, 42),
        ]);
        $generated = $generator($this->size, $this->rand);

        $value = $generated->unbox();
        $this->assertEquals(42, $value->count);
    }

    public function testShrinksGeneratedValues(): void
    {
        $generator = new ArbitraryGenerator(SimpleValue::class);
        $value = $generator($this->size, $this->rand);

        $shrunk = $generator->shrink($value);
        $shrunkValue = GeneratedValueOptions::mostPessimisticChoice($shrunk);
        $this->assertInstanceOf(SimpleValue::class, $shrunkValue->unbox());
    }

    public function testFacadeMethod(): void
    {
        $generator = Generators::arbitrary(SimpleValue::class);
        $generated = $generator($this->size, $this->rand);

        $this->assertInstanceOf(SimpleValue::class, $generated->unbox());
    }

    public function testThrowsForClassWithNoGenerators(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No generators found');
        new ArbitraryGenerator(NoAnnotations::class);
    }

    public function testGeneratesWithConstantAttribute(): void
    {
        $generator = new ArbitraryGenerator(WithConstantAndElements::class);
        $generated = $generator($this->size, $this->rand);

        $value = $generated->unbox();
        $this->assertInstanceOf(WithConstantAndElements::class, $value);
        $this->assertEquals('fixed', $value->fixed);
        $this->assertContains($value->choice, ['a', 'b', 'c']);
    }

    public function testGeneratesWithExplicitTypeAttributes(): void
    {
        $generator = new ArbitraryGenerator(WithExplicitTypeAttributes::class);
        $generated = $generator($this->size, $this->rand);

        $value = $generated->unbox();
        $this->assertInstanceOf(WithExplicitTypeAttributes::class, $value);
        $this->assertIsString($value->name);
        $this->assertIsInt($value->count);
        $this->assertIsFloat($value->ratio);
        $this->assertIsBool($value->active);
    }

    public function testGeneratesWithBackedEnumProperties(): void
    {
        $generator = new ArbitraryGenerator(WithBackedEnums::class);
        $generated = $generator($this->size, $this->rand);

        $value = $generated->unbox();
        $this->assertInstanceOf(WithBackedEnums::class, $value);
        $this->assertInstanceOf(Status::class, $value->status);
        $this->assertInstanceOf(Priority::class, $value->priority);
        $this->assertContains($value->status, Status::cases());
        $this->assertContains($value->priority, Priority::cases());
    }

    public function testGeneratesWithDateTimeProperty(): void
    {
        $generator = new ArbitraryGenerator(WithDateTime::class);
        $generated = $generator($this->size, $this->rand);

        $value = $generated->unbox();
        $this->assertInstanceOf(WithDateTime::class, $value);
        $this->assertInstanceOf(\DateTime::class, $value->date);
    }

    public function testGeneratesWithDateTimeImmutableProperty(): void
    {
        $generator = new ArbitraryGenerator(WithDateTimeImmutable::class);
        $generated = $generator($this->size, $this->rand);

        $value = $generated->unbox();
        $this->assertInstanceOf(WithDateTimeImmutable::class, $value);
        $this->assertInstanceOf(\DateTimeImmutable::class, $value->date);
    }

    public function testGeneratesWithPrimitiveArrayProperty(): void
    {
        $generator = new ArbitraryGenerator(WithArrays::class);
        $generated = $generator($this->size, $this->rand);

        $value = $generated->unbox();
        $this->assertInstanceOf(WithArrays::class, $value);
        $this->assertIsArray($value->numbers);
        $this->assertIsArray($value->names);
        foreach ($value->numbers as $number) {
            $this->assertIsInt($number);
        }
        foreach ($value->names as $name) {
            $this->assertIsString($name);
        }
    }

    public function testGeneratesWithBoundedArrayProperty(): void
    {
        $generator = new ArbitraryGenerator(WithArrays::class);

        for ($i = 0; $i < 50; $i++) {
            $generated = $generator($this->size, $this->rand);
            $value = $generated->unbox();
            $this->assertGreaterThanOrEqual(2, count($value->bounded));
            $this->assertLessThanOrEqual(5, count($value->bounded));
            foreach ($value->bounded as $item) {
                $this->assertIsInt($item);
            }
        }
    }

    public function testGeneratesWithObjectArrayProperty(): void
    {
        $generator = new ArbitraryGenerator(WithObjectArray::class);
        $generated = $generator($this->size, $this->rand);

        $value = $generated->unbox();
        $this->assertInstanceOf(WithObjectArray::class, $value);
        $this->assertIsString($value->id);
        $this->assertIsArray($value->items);
        foreach ($value->items as $item) {
            $this->assertInstanceOf(SimpleValue::class, $item);
        }
    }
}
