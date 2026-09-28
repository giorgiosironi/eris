Arbitrary generators
====================

The arbitrary generator creates random instances of your classes by reflecting on their properties and type hints. Instead of manually wiring together generators for each field, you annotate your class with ``#[Generate]`` and let the type system do the work.

This is especially useful for nested value objects, where composing generators by hand becomes tedious and fragile.

.. _arbitrary:

Arbitrary
---------

``Generators::arbitrary()`` generates instances of a class by assigning random values to all its typed public properties. The generator infers the right strategy from each property's type: ``int``, ``string``, ``float``, ``bool``, enums, ``DateTime``, ``DateTimeImmutable``, and other ``#[Generate]``-annotated classes are all handled automatically, with depth tracking and circular reference detection.

Properties can be fine-tuned with attributes like ``#[Choose]``, ``#[Constant]``, ``#[Elements]``, and ``#[Nullable]``. Array properties require an ``#[ArrayOf]`` attribute to specify the element type, since PHP's ``array`` type hint carries no element information.

.. literalinclude:: ../../examples/ArbitraryTest.php
   :language: php

``testOrderHasValidLineItems`` generates random ``Order`` instances, each containing a string ``$id``, an integer ``$quantity`` between 1 and 100, and a variable-length array of ``LineItem`` objects. The nested ``LineItem`` class is also generated automatically because it carries the ``#[Generate]`` attribute.

``testOverridingPropertyGenerators`` shows how to replace the inferred generator for a specific property by passing an overrides array. Here, ``$quantity`` is forced to always be 42.

``testFromConstructorWithNestedObjects`` demonstrates ``Generators::fromConstructor()``, which works like ``arbitrary()`` but populates constructor parameters instead of public properties. This is the natural choice for classes with ``readonly`` promoted properties.

.. _arrayof:

ArrayOf
-------

PHP's ``array`` type hint does not carry element type information. The ``#[ArrayOf]`` attribute bridges this gap by telling the generator what type of elements the array should contain.

.. code-block:: php

    #[Generate]
    class Warehouse {
        public string $name;

        #[ArrayOf(LineItem::class)]
        public array $inventory;

        #[ArrayOf('int', min: 2, max: 5)]
        public array $stockCounts;
    }

The first argument is the element type: a class name or a primitive (``'int'``, ``'string'``, ``'float'``, ``'bool'``). By default, arrays have variable length (0 up to the generation size). The optional ``min`` and ``max`` parameters constrain the array length to a fixed range.

``#[ArrayOf]`` works on both properties and constructor parameters.

.. _fromconstructor:

From Constructor
----------------

``Generators::fromConstructor()`` is an alternative to ``arbitrary()`` for classes whose state is set through the constructor rather than public properties. It reflects on the constructor's parameters and resolves generators the same way, supporting the same set of type hints and attributes.

.. code-block:: php

    #[Generate]
    class LineItem {
        public function __construct(
            public readonly string $sku,
            #[Choose(1, 5)]
            public readonly int $quantity,
        ) {}
    }

    $generator = Generators::fromConstructor(LineItem::class);

Both ``arbitrary()`` and ``fromConstructor()`` accept an optional overrides array to replace the inferred generator for specific properties or parameters:

.. code-block:: php

    $generator = Generators::arbitrary(Order::class, [
        'quantity' => Generators::choose(42, 42),
    ]);

.. _available-attributes:

Available attributes
--------------------

The following attributes can be placed on properties or constructor parameters to override the default type-based inference:

- ``#[Choose(int $min, int $max)]`` — generates an integer in the given range.
- ``#[Constant(mixed $value)]`` — always produces the same value.
- ``#[Elements(mixed ...$values)]`` — picks randomly from the listed values.
- ``#[IsInt]``, ``#[IsString]``, ``#[IsFloat]``, ``#[IsBool]`` — explicit type generators, useful when the property type is broader (e.g. ``mixed``).
- ``#[Nullable(int $nullPercentage = 10)]`` — controls how often a nullable property receives ``null``.
- ``#[ArrayOf(string $type, ?int $min = null, ?int $max = null)]`` — specifies element type for ``array`` properties.
