<?php

use Eris\Arbitrary\ArrayOf;
use Eris\Arbitrary\Choose;
use Eris\Arbitrary\Generate;
use Eris\Generators;

/**
 * Value objects used in the examples below.
 */
#[Generate]
class LineItem
{
    public function __construct(
        public readonly string $sku,
        #[Choose(1, 5)]
        public readonly int $quantity,
    ) {
    }
}

#[Generate]
class Order
{
    public string $id;

    #[Choose(1, 100)]
    public int $quantity;

    #[ArrayOf(LineItem::class)]
    public array $lineItems;
}

/**
 * These tests demonstrate the arbitrary generator, which creates random
 * instances of annotated classes without manual generator wiring.
 */
class ArbitraryTest extends \PHPUnit\Framework\TestCase
{
    use Eris\TestTrait;

    public function testOrderHasValidLineItems()
    {
        $this
            ->forAll(
                Generators::arbitrary(Order::class)
            )
            ->then(function (Order $order) {
                $this->assertIsString($order->id);
                $this->assertGreaterThanOrEqual(1, $order->quantity);
                $this->assertLessThanOrEqual(100, $order->quantity);
                foreach ($order->lineItems as $item) {
                    $this->assertInstanceOf(LineItem::class, $item);
                    $this->assertGreaterThanOrEqual(1, $item->quantity);
                    $this->assertLessThanOrEqual(5, $item->quantity);
                }
            });
    }

    public function testOverridingPropertyGenerators()
    {
        $this
            ->forAll(
                Generators::arbitrary(Order::class, [
                    'quantity' => Generators::choose(42, 42),
                ])
            )
            ->then(function (Order $order) {
                $this->assertEquals(42, $order->quantity);
            });
    }

    public function testFromConstructorWithNestedObjects()
    {
        $this
            ->forAll(
                Generators::fromConstructor(LineItem::class)
            )
            ->then(function (LineItem $item) {
                $this->assertIsString($item->sku);
                $this->assertGreaterThanOrEqual(1, $item->quantity);
                $this->assertLessThanOrEqual(5, $item->quantity);
            });
    }
}
