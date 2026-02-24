<?php

namespace Eris\Arbitrary;

use Eris\Arbitrary\Fixtures\Order;
use Eris\Arbitrary\Fixtures\OrderWithAttributes;
use Eris\Arbitrary\Fixtures\OrderWithNullable;
use Eris\Generator\ChooseGenerator;
use Eris\Generator\GeneratedValueOptions;
use Eris\Generators;
use Eris\Random\RandomRange;
use Eris\Random\RandSource;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class FromConstructorGeneratorTest extends TestCase
{
    private int $size;
    private RandomRange $rand;

    protected function setUp(): void
    {
        $this->size = 10;
        $this->rand = new RandomRange(new RandSource());
    }

    public function testGeneratesFromConstructor(): void
    {
        $generator = new FromConstructorGenerator(Order::class);
        $generated = $generator($this->size, $this->rand);

        $value = $generated->unbox();
        $this->assertInstanceOf(Order::class, $value);
        $this->assertIsString($value->product);
        $this->assertIsInt($value->quantity);
        $this->assertIsFloat($value->price);
    }

    public function testOverridesConstructorParameterGenerators(): void
    {
        $generator = new FromConstructorGenerator(Order::class, [
            'quantity' => new ChooseGenerator(1, 100),
        ]);
        $generated = $generator($this->size, $this->rand);

        $value = $generated->unbox();
        $this->assertInstanceOf(Order::class, $value);
        $this->assertGreaterThanOrEqual(1, $value->quantity);
        $this->assertLessThanOrEqual(100, $value->quantity);
    }

    public function testShrinksGeneratedValues(): void
    {
        $generator = new FromConstructorGenerator(Order::class);
        $value = $generator($this->size, $this->rand);

        $shrunk = $generator->shrink($value);
        $shrunkValue = GeneratedValueOptions::mostPessimisticChoice($shrunk);
        $this->assertInstanceOf(Order::class, $shrunkValue->unbox());
    }

    public function testThrowsForClassWithoutConstructor(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('no constructor');
        new FromConstructorGenerator(NoConstructor::class);
    }

    public function testFacadeMethod(): void
    {
        $generator = Generators::fromConstructor(Order::class, [
            'quantity' => Generators::choose(1, 100),
        ]);
        $generated = $generator($this->size, $this->rand);

        $value = $generated->unbox();
        $this->assertInstanceOf(Order::class, $value);
        $this->assertGreaterThanOrEqual(1, $value->quantity);
        $this->assertLessThanOrEqual(100, $value->quantity);
    }

    public function testConstructorWithAttributeAnnotatedParameters(): void
    {
        $generator = new FromConstructorGenerator(OrderWithAttributes::class);
        $generated = $generator($this->size, $this->rand);

        $value = $generated->unbox();
        $this->assertInstanceOf(OrderWithAttributes::class, $value);
        $this->assertIsString($value->product);
        $this->assertGreaterThanOrEqual(1, $value->quantity);
        $this->assertLessThanOrEqual(10, $value->quantity);
    }

    public function testConstructorWithNullableParameters(): void
    {
        $generator = new FromConstructorGenerator(OrderWithNullable::class);

        $hasNull = false;
        $hasNonNull = false;
        for ($i = 0; $i < 100; $i++) {
            $generated = $generator($this->size, $this->rand);
            $value = $generated->unbox();
            $this->assertInstanceOf(OrderWithNullable::class, $value);
            $this->assertIsString($value->product);
            $this->assertTrue(is_int($value->quantity) || $value->quantity === null);
            if ($value->quantity === null) {
                $hasNull = true;
            } else {
                $hasNonNull = true;
            }
        }
        $this->assertTrue($hasNull, 'Expected at least one null value in 100 iterations');
        $this->assertTrue($hasNonNull, 'Expected at least one non-null value in 100 iterations');
    }
}

class NoConstructor
{
    public string $value;
}
