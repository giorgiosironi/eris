.. _outside_phpunit:

Using Eris outside of PHPUnit
==========

Eris can be reused as a library for (reproducibly) generating random data, outside of PHPUnit test cases. For example, it may be useful in other testing frameworks or in scripts that run inside your testing infrastructure but not tied to a specific PHPUnit test suite.

Usage
-----

.. literalinclude:: ../examples/generating_integers.php
   :language: php

This script instantiates a ``Eris\Facade``, which offers the same interface as ``Eris\TestTrait``. ``forAll()`` is the main entry point and should be called over this object rather than ``$this``.

The Facade is automatically initialized, and is used here to dump 100 random integers. At this time, reproducibility can be obtained by explicitly setting the `ERIS_SEED` environment variable.
